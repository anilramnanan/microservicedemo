<?php
require 'vendor/autoload.php';
require 'database.php';
require 'lib.php';

$validToken = "99999";

$app = new \Slim\App();

$app->get('/ping/{name}', function ($request, $response, array $args) {
 $body = json_encode(['ack' => time()]);
 $name = $args['name'];
 $response->write($name);
 $response = $response->withHeader(
 'Content-Type', 'application/json');
 return $response;
});


$app->get('/product', function ($request, $response, array $args) {

  $database = new Database();
  $db = $database->getConnection();


  $response = $response->write(json_encode("Product"));
  $response = $response->withHeader(
 'Content-Type', 'application/json');
 return $response;
});

$app->get('/', function ($request, $response) {

  $response = $response->write(json_encode("Hello"));
  $response = $response->withHeader(
 'Content-Type', 'application/json');
 return $response;
});

$app->run();
