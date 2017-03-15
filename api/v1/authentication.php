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

$app->post('/signUp', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'name', 'password'),$r->customer);
    require_once 'passwordHash.php';
    $db = new DbHandler();
    $phone = $r->customer->phone;
    $name = $r->customer->name;
    $email = $r->customer->email;
    $address = $r->customer->address;
    $password = $r->customer->password;
    $isUserExists = $db->getOneRecord("select 1 from customers where email='$email'");
    if(!$isUserExists){
        $r->customer->password = passwordHash::hash($password);
        $tabble_name = "customers";
        $column_names = array('phone', 'name', 'email', 'password', 'city', 'address');
        $result = $db->insertIntoTable($r->customer, $column_names, $tabble_name);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "User account created successfully";
            $response["id"] = $result;
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['id'] = $response["id"];
            $_SESSION['phone'] = $phone;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create customer. Please try again";
            echoResponse(201, $response);
        }
    }else{
        $response["status"] = "error";
        $response["message"] = "An user with the provided email exists!";
        echoResponse(201, $response);
    }
});

$app->post('/customerUpdate', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'name'),$r->customer);
    $db = new DbHandler();
    $id = $r->customer->id;
    $phone = $r->customer->phone;
    $name = $r->customer->name;
    $email = $r->customer->email;
    $address = $r->customer->address;
    $isUserExists = $db->getOneRecord("select 1 from customers where id='$id'");
    if($isUserExists){
        $tabble_name = "customers";
        $column_names = array('phone', 'name', 'city', 'address');
        $column_where = 'id';
        $result = $db->updateIntoTable($r->customer, $column_names, $tabble_name, $column_where);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "User account changed successfully";
            $response["id"] = $result;
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['id'] = $response["id"];
            $_SESSION['phone'] = $phone;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to change customer. Please try again";
            echoResponse(201, $response);
        }
    }else{
        $response["status"] = "error";
        $response["message"] = "An user with the provided email not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/logout', function() {
    $db = new DbHandler();
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    echoResponse(200, $response);
});

?>