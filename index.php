<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath("/work2");

require __DIR__ . '/dbconnect.php';
require __DIR__ . '/api/customers.php';
require __DIR__ . '/api/products.php';
require __DIR__ . '/api/employees.php';

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});


$app->get('/hello/{name}',function(Request $request , Response $response , array $args){
    $name = $args['name'];
    $response ->getBody()->write("Hello Get, $name");
    return $response;
});

$app->post('/hello',function(Request $request , Response $response , array $args){
    $body = $request->getParsedBody();
    $name = $body['name'];
    $response ->getBody()->write("Hello Post, $name");
    return $response;
});

$app->run();


