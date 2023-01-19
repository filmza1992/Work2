<?php

ini_set('display_error',1);
error_reporting(E_ALL);


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class product{
    public $id;
    public $name;
    
    function __construct($id , $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}

$app ->get('/products', function(Request $request , Response $response , array $args){
    $conn = $GLOBALS['dbconn'];
    $result = mysqli_query($conn , "SELECT * from products");
    $data = array();

    while($row = $result->fetch_assoc()){
        array_push($data , $row);
    }

    $json = json_encode($data);

    $response -> getBody()->write($json);
    
    return $response;
});

$app -> post('/products',function(Request $request , Response $response , array $args){
    $conn = $GLOBALS['dbconn'];

    $body = $request->getParsedBody();

    $id = $body['id'];
    $name = $body['name'];
    
    try{
        $result = mysqli_query($conn , "INSERT into products value('$id' , '$name')");
    }catch(Exception $e){
        $response -> getBody() ->write("Can't add product because have same ID");
        return $response;
    }
    $result = mysqli_query($conn , "SELECT * from products");
    $data = array();

    while($row = $result->fetch_assoc()){
        array_push($data ,$row);
    }
    $json = json_encode($data);
    $response -> getBody()->write($json);
    return $response;
    
});



$app->DELETE('/products/{id}',function(Request $request , Response $response , array $args){
    $conn = $GLOBALS['dbconn'];

    $id = $args['id'];

    $result = mysqli_query($conn , "DELETE from products where pid = $id");

    $result = mysqli_query($conn , "SELECT * from products");
    $data = array();
    while($row = $result->fetch_assoc()){
        array_push($data , $row);
    }    

    $json = json_encode($data);
    $response->getBody()->write("$json");
    
    return $response;

});

$app -> post('/products/{id}',function(Request $request , Response $response , array $args){
    $body = $request->getParsedBody();
    $conn = $GLOBALS['dbconn'];

    $originalID = $args['id'];
    $id = $body['newID'];
    $name = $body['newName'];

    $result = mysqli_query($conn , "UPDATE products 
                                    set pid = '$id',
                                    name = '$name'
                                    where pid = '$originalID'");
    $result = mysqli_query($conn , "SELECT * from products");
    $data = array();

    while($row = $result -> fetch_assoc()){
        array_push($data , $row);
    }

    $json = json_encode($data);
    $response->getBody()->write($json);

    return $response;

});

$app -> get('/products/{text}',function(Request $request , Response $response , array $args){
    $body = $request->getParsedBody();
    $conn = $GLOBALS['dbconn'];

    $text= $args['text'];

    $result = mysqli_query($conn , "SELECT * from products
                                    where name like '%$text%'
                                    or  pid like '%$text%'");
    $data = array();

    while($row = $result -> fetch_assoc()){
        array_push($data , $row);
    }

    $json = json_encode($data);
    $response->getBody()->write($json);

    return $response;

});















