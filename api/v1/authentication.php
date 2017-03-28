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
    $b = json_decode($app->request->getHeaders());
    verifyRequiredParams(array('email', 'password'),$r->customer);
    $response = array();
    $db = new DbHandler();
    $email = $r->customer->email;
    $password = $r->customer->password;
    $token = bin2hex(openssl_random_pseudo_bytes(8));  //generate a random token
    $tokenExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));  //the expiration date will be in one hour from the current moment
    $user = $db->getOneRecord("select id,name,password,email,created,admin from customers where email='$email'");
    if ($user != NULL) {
        if(passwordHash::check_password($user['password'],$password)){
            if (updateCustomerToken('{"id":'.$user['id'].',"token":'.$token.',"token_expire":'.$tokenExpiration.'}')) {
                $response['id'] = $user['id'];
                $response['name'] = $user['name'];
                $response['token'] = $token;
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
            }
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

function updateCustomerToken($param){
    $p = json_decode($param);
    $tabble_name = "customers";
    $column_names = array('token', 'token_expire');
    $column_where = "id";
    $result = $db->updateIntoTable($p, $column_names, $tabble_name, $column_where);
    if ($result != NULL) {
        $response['status'] = "success";
        $response['message'] = 'Logged in successfully.';
        return TRUE;
    } else {
        $response["status"] = "error";
        $response['message'] = 'Login failed. Incorrect credentials';
        return FALSE;
    }
}

?>