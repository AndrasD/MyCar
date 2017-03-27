<?php

$app->get('/session', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["id"] = $session['id'];
    $response["email"] = $session['email'];
    $response["name"] = $session['name'];
    $response["admin"] = $session['admin'];
    echoResponse(200, $session);
});

$app->post('/login', function() use ($app) {
    require_once 'passwordHash.php';
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'password'),$r->customer);
    $response = array();
    $db = new DbHandler();
    $password = $r->customer->password;
    $email = $r->customer->email;
    $user = $db->getOneRecord("select id,name,password,email,created,admin from customers where email='$email'");
    if ($user != NULL) {
        if(passwordHash::check_password($user['password'],$password)){
            $response['status'] = "success";
            $response['message'] = 'Logged in successfully.';
            $response['name'] = $user['name'];
            $response['id'] = $user['id'];
            $response['email'] = $user['email'];
            $response['createdAt'] = $user['created'];
            $response['admin'] = $user['admin'];
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $user['name'];
            $_SESSION['admin'] = $user['admin'];
        } else {
            $response['status'] = "error";
            $response['message'] = 'Login failed. Incorrect credentials';
        }
    }else {
        $response['status'] = "error";
        $response['message'] = 'No such user is registered';
    }
    echoResponse(200, $response);
});

$app->get('/logout', function() {
    $db = new DbHandler();
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    echoResponse(200, $response);
});

?>