<?php

// This is the entry file for the web application. 
// This is where we will initialize the router and define our routes.
// If necessary, contents of a specified file can be rendered through custom view engine
require_once(dirname(__DIR__) . '/src/routing/Request.php');
require_once(dirname(__DIR__) . '/src/routing/View.php');
require_once(dirname(__DIR__) . '/src/routing/Router.php');
// Handle DB logic
require_once(dirname(__DIR__) . '/src/db/CRUD.php'); // Perform CRUD operations with Crud class

$router = new Router(new Request);

$router->get('/', function($req) {
    header('Content-Type: text/html; text/javascript; text/css; charset=utf-8');
    return new View(dirname(__DIR__) . '/assets/homepage/index.html');
});

$router->get('/app.js', function($req) {
    header('Content-Type: text/javascript');
    return new View(dirname(__DIR__) . '/assets/homepage/app.js');
});

$router->get('/jquery.js', function($req) {
    header('Content-Type: text/javascript');
    return new View(dirname(__DIR__) . '/assets/modules/jquery.js');
});

$router->get('/styles.css', function($req) {
    header('Content-Type: text/css');
    return new View(dirname(__DIR__) . '/assets/homepage/styles.css');
});
// Select all products to show on the homepage/frontpage
$router->post('/', function($req) {
    $selection = (new Crud($req->getBody()))->select();
    echo json_encode($selection);
});
// Delete selected products
$router->post('/delete', function($req) {
    $deletion = (new Crud($req->getBody()))->delete_selected();
    echo json_encode($deletion);
});

$router->get('/add-product', function($req) {
    header('Content-Type: text/html; text/javascript; text/css; charset=utf-8');
    return new View(dirname(__DIR__) . '/assets/add-product/index.html');
});

$router->get('/add-product/add-product.js', function($req) {
    header('Content-Type: text/javascript;');
    return new View(dirname(__DIR__) . '/assets/add-product/add-product.js');
});

$router->get('/add-product/styles.css', function($req) {
    header('Content-Type: text/css;');
    return new View(dirname(__DIR__) . '/assets/add-product/styles.css');
});

$router->get('/add-product/validate.js', function($req) {
    header('Content-Type: text/javascript;');
    return new View(dirname(__DIR__) . '/assets/modules/validate.js');
});

$router->post('/add-product', function($req) {
    $insertion = (new Crud($req->getBody()))->insert();
    echo json_encode($insertion); 
});

$router->resolve(); // Traverse all precedent (defined) routes and call callback function on matched route;

?>