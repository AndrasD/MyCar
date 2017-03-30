<?php
// Routes
// get all customers
$app->get('/customers', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT * FROM customers");
    $sth->execute();
    $customers = $sth->fetchAll();
    return $this->response->withJson($customers);
});

// Retrieve customer with id
$app->get('/customer/[{id}]', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT * FROM customer WHERE id=:id");
    $sth->bindParam("id", $args['id']);
    $sth->execute();
    $customers = $sth->fetchObject();
    return $this->response->withJson($customers);
});

// Search for customers with given search teram in their name
// f.e: query = "owner=1"
$app->get('/customers/search/[{query}]', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT * FROM customers WHERE :query");
    $sth->bindParam("query", $args['query']);
    $sth->execute();
    $customers = $sth->fetchAll();
    return $this->response->withJson($customers);
});

// Add a new customer
$app->post('/customer', function ($request, $response) {
    require_once 'passwordHash.php';
    $input = $request->getParsedBody();
    $isUserExists = $this->db->query('SELECT 1 FROM customers WHERE email='.$input['email']);
    if (!$isUserExists) {
        $sql = "INSERT INTO customers (email, name, phone, password, address, city, admin, owner) 
                VALUES (:email, :name, :phone, :password, :address, :city, :admin, :owner)";
            $sth = $this->db->prepare($sql);
        $sth->bindParam("email", $input['email']);
        $sth->bindParam("name", $input['name']);
        $sth->bindParam("phone", $input['phone']);
        $sth->bindParam("password", passwordHash::hash($password));
        $sth->bindParam("address", $input['address']);
        $sth->bindParam("city", $input['city']);
        $sth->bindParam("admin", $input['admin']);
        $sth->bindParam("owner", $input['owner']);
        $sth->execute();
        $response['id'] = $this->db->lastInsertId();
        $response['status'] = "success";
        $response['message'] = "User account created successfully";
        return $this->response->withJson($response);
    } else {

    }
});

// Delete a customer with given id
$app->delete('/customer/[{id}]', function ($request, $response, $args) {
        $sth = $this->db->prepare("DELETE FROM customers WHERE id=:id");
    $sth->bindParam("id", $args['id']);
    $sth->execute();
    $response['status'] = "success";
    $response['message'] = "User account deleted successfully";
    return $this->response->withJson($response);
});

// Update customer with given id
$app->put('/customer/[{id}]', function ($request, $response, $args) {
    $input = $request->getParsedBody();
    $sql = "UPDATE customers SET name=:name, phone=:phone, address=:address, city=:city WHERE id=:id";
        $sth = $this->db->prepare($sql);
    $sth->bindParam("id", $args['id']);
    $sth->bindParam("name", $input['name']);
    $sth->bindParam("phone", $input['phone']);
    $sth->bindParam("address", $input['address']);
    $sth->bindParam("city", $input['city']);
    $sth->execute();
    $input['id'] = $args['id'];
    return $this->response->withJson($input);
});


$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
