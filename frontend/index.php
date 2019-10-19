<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
require 'vendor/autoload.php';


$app = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer('templates/');


$app->get('/', function (Request $request, Response $response, array $args) {

    $json = file_get_contents('http://100.115.92.202:5002/all');
    $obj = json_decode($json);
    //print_r($obj->data);die;

    $response = $this->view->render($response, 'index.phtml', ['obj' => $obj->data, 'router' => $this->router]);
    return $response;
})->setName('index');


// ---------------------------------- Person -------------------------------------------
$app->get('/people', function (Request $request, Response $response, array $args) {

    $json = file_get_contents('http://100.115.92.202:5002/all');
    $obj = json_decode($json);
    //print_r($obj->data);die;

    $response = $this->view->render($response, 'people.phtml', ['obj' => $obj->data, 'router' => $this->router]);
    return $response;
})->setName('people');


$app->post('/addperson', function (Request $request, Response $response, array $args) {

  $allPostPutVars = $request->getParsedBody();
  $name = $allPostPutVars['inputName'];
  $email = $allPostPutVars['inputEmail'];

  $jsonData = array(
      'name' => $name,
      'email' => $email
  );

  $jsonDataEncoded = json_encode($jsonData);

  //API Url
  $url = 'http://100.115.92.202:5002/add';
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  $result = curl_exec($ch);

  $url =$this->router->pathFor('people');
  return $response->withRedirect($url);

})->setName('add-person');


$app->get('/deleteperson/{id}', function (Request $request, Response $response, array $args) {

  $id = $args['id'];
  $url = 'http://100.115.92.202:5002/delete/'.$id;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  $result = json_decode($result);
  curl_close($ch);


  $url =$this->router->pathFor('people');
  return $response->withRedirect($url);

})->setName('delete-person');


// ---------------------------------- Places -------------------------------------------
$app->get('/places', function (Request $request, Response $response, array $args) {

    $json = file_get_contents('http://100.115.92.202:5001/all');
    $obj = json_decode($json);
    //print_r($obj->data);die;

    $response = $this->view->render($response, 'places.phtml', ['obj' => $obj->data, 'router' => $this->router]);
    return $response;
})->setName('places');

$app->post('/addplace', function (Request $request, Response $response, array $args) {

  $allPostPutVars = $request->getParsedBody();
  $name = $allPostPutVars['inputName'];

  $jsonData = array(
      'name' => $name
  );

  $jsonDataEncoded = json_encode($jsonData);
  //print_r($jsonDataEncoded);die;
  //API Url
  $url = 'http://100.115.92.202:5001/add';
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  $result = curl_exec($ch);

  $url =$this->router->pathFor('places');
  return $response->withRedirect($url);

})->setName('add-place');


$app->get('/deleteplace/{id}', function (Request $request, Response $response, array $args) {

  $id = $args['id'];
  $url = 'http://100.115.92.202:5001/delete/'.$id;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  $result = json_decode($result);
  curl_close($ch);


  $url =$this->router->pathFor('places');
  return $response->withRedirect($url);

})->setName('delete-place');

// ---------------------------------- items -------------------------------------------
$app->get('/items', function (Request $request, Response $response, array $args) {

    $json = file_get_contents('http://100.115.92.202:5003/all');
    $obj = json_decode($json);

    $json = file_get_contents('http://100.115.92.202:5001/all');
    $places = json_decode($json);

    $json = file_get_contents('http://100.115.92.202:5002/all');
    $people = json_decode($json);
    //print_r($obj->data);die;

    $response = $this->view->render($response, 'items.phtml', ['obj' => $obj->data, 'places' => $places->data, 'people' => $people->data, 'router' => $this->router]);
    return $response;
})->setName('items');

$app->post('/additem', function (Request $request, Response $response, array $args) {

  $allPostPutVars = $request->getParsedBody();
  $name = $allPostPutVars['inputName'];
  $serialno = $allPostPutVars['inputSerialNo'];
  $location = $allPostPutVars['inputLocation'];
  $assignedto = $allPostPutVars['inputAssignedTo'];

  $jsonData = array(
      'name' => $name,
      'serialno' => $serialno,
      'location' => $location,
      'assignedto' => $assignedto
  );

  $jsonDataEncoded = json_encode($jsonData);
  //print_r($jsonDataEncoded);die;
  //API Url
  $url = 'http://100.115.92.202:5003/add';
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  $result = curl_exec($ch);

  $url =$this->router->pathFor('items');
  return $response->withRedirect($url);

})->setName('add-item');


$app->get('/deleteitem/{id}', function (Request $request, Response $response, array $args) {

  $id = $args['id'];
  $url = 'http://100.115.92.202:5003/delete/'.$id;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  $result = json_decode($result);
  curl_close($ch);


  $url =$this->router->pathFor('items');
  return $response->withRedirect($url);

})->setName('delete-item');

$app->run();
