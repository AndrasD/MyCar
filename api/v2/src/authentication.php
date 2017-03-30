<?php

// get session
$app->get('/session', function($request, $response, $args) {
    if (!isset($_SESSION)) {
        session_start();
    }
    $sess = array();
    if (isset($_SESSION['id']))
    {
        $sess["id"] = $_SESSION['id'];
        $sess["name"] = $_SESSION['name'];
        $sess["email"] = $_SESSION['email'];
        $sess["admin"] = $_SESSION['admin'];
    } else {
        $sess["id"] = '';
        $sess["name"] = 'Guest';
        $sess["email"] = '';
        $sess["admin"] = '';
    }
    return $this->response->withJson($sess);
});

$app->post('/login', function($request, $response) {
    require_once 'passwordHash.php';
    $input = $request->getParsedBody();
    
    $sth = $this->db->prepare("SELECT * FROM customer WHERE email=:email");
    $sth->bindParam("email", $input['email']);
    $sth->execute();
    $user = $sth->fetchAll();
    if ($user != NULL) {
        if (passwordHash::check_password($user['password'],$input['password'])) {
            $token = bin2hex(openssl_random_pseudo_bytes(8));  //generate a random token
            $tokenExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));  //the expiration date will be in one hour from the current moment
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['admin'] = $user['admin'];
            $_SESSION['token'] = $token;
            $_SESSION['tokenExpiration'] = $tokenExpiration;

            $response['status'] = "success";
            $response['message'] = 'Logged in successfully.';
            $response['id'] = $user['id'];
            $response['name'] = $user['name'];
            $response['token'] = $token;
            $response['email'] = $user['email'];
            $response['createdAt'] = $user['created'];
            $response['admin'] = $user['admin'];

        } else {
            $response['status'] = "error";
            $response['message'] = 'Login failed. Incorrect credentials';
        }
    } else {
        $response['status'] = "error";
        $response['message'] = 'No such user is registered';
    }
    return $this->response->withJson($response);
});

