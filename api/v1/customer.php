<?php

$app->post('/getOwnCustomers', function() use ($app) {
    $r = json_decode($app->request->getBody());
    $id = $r->user->id;
//    $id = $app->request()->params('id');
    $db = new DbHandler();
    $customers = $db->getAllRecord("select * from customers where owner='$id'");
    if ($customers->num_rows > 0 ){
        while($row = $customers->fetch_assoc()){
            $json[] = $row;
        }
    }
    echoResponse(200, $json);
});

$app->post('/addCustomer', function() use ($app) {
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

$app->post('/updCustomer', function() use ($app) {
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
        $column_where = "id";
        $result = $db->updateIntoTable($r->customer, $column_names, $tabble_name, $column_where);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "User account changed successfully";
            $response["id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
//            $response["message"] = "Failed to change customer. Please try again";
            $response["message"] = $result;
            echoResponse(201, $response);
        }
    }
});

?>