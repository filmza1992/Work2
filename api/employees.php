<?php

ini_set('display_error',1);
error_reporting(E_ALL);


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app ->get('/employees', function(Request $request , Response $response , array $args){
    $conn = $GLOBALS['dbconn'];
    $result = mysqli_query($conn , "SELECT * from employees");
    $data = array();

    while($row = $result->fetch_assoc()){
        array_push($data , $row);
    }

    $json = json_encode($data);

    $response -> getBody()->write($json);
    
    return $response;
});

$app -> post('/employees/add',function(Request $request , Response $response , array $args){
    $body = $request->getParsedBody();

    $user = $body['name'];
    $id = $body['email'];
    $password = $body['password'];

    $conn = $GLOBALS['dbconn'];

    $hashPas = password_hash($password , PASSWORD_DEFAULT);
    $result = mysqli_query($conn , "INSERT into employees value(null,'$user','$id','$password','$hashPas')");

    $response->getBody()->write("Add success !!! \n in employees");
    return $response;
});


$app->post('/employees',function(Request $request , Response $response , array $args){
    $body = $request->getParsedBody();

    $userID = $body['email'];
    $password = $body['password'];

    $conn = $GLOBALS['dbconn'];


    $dbpassword = getPasswordfromDB($conn , $userID);
    
    
    if(password_verify($password , $dbpassword)){
        $str = "Success login userID $userID";
    }else{
        $str = "Error don't have this userID in db.";
    }
   

    $response->getBody()->write("$str");
    return $response;
});


$app -> post('/employees/change',function(Request $request , Response $response , array $args){
    
    $body = $request->getParsedBody();
    $conn = $GLOBALS['dbconn'];

    $email = $body['email'];
    $originalPass = $body['originalPass'];
    $changePass = $body['changePass'];

    $dbpassword = getPasswordfromDB($conn ,$email);

    $id = getID($conn , $email);
    if(password_verify($originalPass , $dbpassword)){
        $newHashPass = password_hash($changePass , PASSWORD_DEFAULT);

        $result = mysqli_query($conn ,"UPDATE employees set passwordHash = '$newHashPass' , password = '$changePass' where eid = $id");

        $response -> getBody() ->write("Update pass success \n-> old password $originalPass = $dbpassword \nto $changePass = $newHashPass");
        return $response;
    }
    $response -> getBody() ->write("Wrong original Password");
    return $response;

});
function getPasswordfromDB(mysqli $con ,string $email){
    $result = mysqli_query($con , "SELECT * from employees where email = '$email'");
    if($result->num_rows == 1){
        $row = $result->fetch_assoc();
        $dbpassword = $row['passwordHash'];
    }
    return $dbpassword;
};

function getID(mysqli $con , string $email){
    $result = mysqli_query($con , "SELECT * from employees where email = '$email'");
    if($result->num_rows == 1){
        $row = $result->fetch_assoc();
        $id = $row['eid'];
    }
    return $id;
}