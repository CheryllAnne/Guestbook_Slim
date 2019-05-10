<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
error_reporting(E_ALL ^ E_NOTICE);
/**
 * Created by PhpStorm.
 * User: CHERYLLANNE
 * Date: 4/12/2019
 * Time: 4:39 PM
 */

use Slim\Views\Twig as TwigView;

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

$container = $app->getContainer();

// view all guestbook entries
$app->get('/v1/posts/view', function ($request, $response){
    //echo "Welcome to books";
    if ($this->session->check('logged_id') == false) {
        $error = "Please login!";
        return $response->withStatus(404)->withJson($error);
        exit;
    }

    require_once('dbconnect.php');
    $query = "Select * from guestList order by id";
    $result = $mysqli->query($query);

    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }

    if(isset($data)){
        header('Content-Type: application/json');
        echo json_encode($data);
    }
});

// new post
$app->post('/v1/posts/new', function (Request $request, Response $response){

    require_once('dbconnect.php');
    $error = "An error has occured! Please try again";
    $success = "New post Successful!";

    $name = json_decode($request->getBody())->name; //striptags to avoid cross site scripting
    $email = json_decode($request->getBody())->email;
    $comment = json_decode($request->getBody())->comment;
    $image = null;
    $file_type = null;
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $time = date("h:i:s A");
    $date = date("F d,Y");
    $addNew = "Insert into guestList (name, email, comment, image, type, time, date) VALUES ('$name', '$email', '$comment', '$image', '$file_type', '$time', '$date')";
    $result = $mysqli->query($addNew) or die($mysqli->error);

    if($result == true){
        return $response->withStatus(200)->withJson($success);
    }
    else {
        return $response->withStatus(404)->withJson($error);
    }
});

//delete function
$app->delete('/v1/posts/delete/{id}', function ($request, $response, $args){

    require_once ('dbconnect.php');
    //$id = $request->getAttribute('id');
    $id = $args['id'];
    $error = "Post doesn't exist";
    $success = "Successfully deleted";

    $delete = "DELETE FROM guestList WHERE id=$id";
    $result = $mysqli->query($delete) or die($mysqli->error);

    if($result == true){
        return $response->withStatus(200)->withJson($success);
    }
    else {
        return $response->withStatus(404)->withJson($error);
    }
});

//edit function
$app->put('/v1/posts/edit/{id}', function (Request $request, Response $response, $args){

    require_once ('dbconnect.php');
    $id = $args['id'];
    $error = "Edit unsuccessful";
    $success = "Successfully Edited";
    $comment = json_decode($request->getBody())->comment;

    $edit = "UPDATE `guestList` SET comment = '$comment' WHERE id = ('$id')";
    $result = $mysqli->query($edit) or die($mysqli->error);


    if($result == true){
        return $response->withStatus(200)->withJson($success);
    }
    else {
        return $response->withStatus(404)->withJson($error);
    }
    //exit;
});

/*********************************************************************
 **************************** USERS **********************************
 *********************************************************************/

//login
$app->post('/v1/login', function (Request $request, Response $response){

    require_once('dbconnect.php');
    $error = "An error has occured! Please try again";
    $success = "Logged In!";

    $username = json_decode($request->getBody())->username; //striptags to avoid cross site scripting
    //$password = json_decode($request->getBody())->password;
    //$password =password_hash($password1, PASSWORD_DEFAULT);
    $login = "SELECT * FROM `user` WHERE username = '$username'"; ///////////
    $result = $mysqli->query($login) or die($mysqli->error);

    if($result == true){
        $this->session->set('logged_id', true);
        return $response->withStatus(200)->withJson($success);
    }
    else {
        return $response->withStatus(404)->withJson($error);
    }
});

//view all users
$app->get('/v1/user/view', function (Request $request, Response $response){

    require_once('dbconnect.php');
    $query = "Select * from `user` order by id";
    $result = $mysqli->query($query);

    while($row = $result->fetch_assoc()){
        $users[] = $row;
    }
    //var_dump($users);
    if(isset($users)){
        header('Content-Type: application/json');
        echo json_encode($users);
    }
});

//delete function
$app->delete('/v1/user/delete/{id}', function ($request, $response, $args) {

    require_once('dbconnect.php');
    //$id = $request->getAttribute('id');
    $id = $args['id'];
    $error = "User doesn't exist";
    $success = "User deleted";

    $delete = "DELETE FROM `user` WHERE id=$id";
    $result = $mysqli->query($delete) or die($mysqli->error);

    if ($result == true) {
        return $response->withStatus(200)->withJson($success);
    } else {
        return $response->withStatus(404)->withJson($error);
    }
});

//edit function
$app->put('/v1/user/edit/{id}', function (Request $request, Response $response, $args){

    require_once ('dbconnect.php');
    $id = $args['id'];
    $error = "Edit unsuccessful";
    $success = "Successfully Edited";
    $username = json_decode($request->getBody())->username;

    $edit = "UPDATE `user` SET username = '$username' WHERE id = ('$id')";
    $result = $mysqli->query($edit) or die($mysqli->error);


    if($result == true)
        return $response->withStatus(200)->withJson($success);

    else
        return $response->withStatus(404)->withJson($error);

    exit;
});

//register user
$app->post('/v1/register', function (Request $request, Response $response){

    require_once('dbconnect.php');
    $error = "An error has occured! Please try again";
    $success = "New user added!";

    $username = json_decode($request->getBody())->username; //striptags to avoid cross site scripting
    $email = json_decode($request->getBody())->email;
    $password = json_decode($request->getBody())->password;
    //$password =password_hash($password1, PASSWORD_DEFAULT);
    $newUser = "Insert into guestList (username, email, password) VALUES ('$username', '$email', '$password')";
    $result = $mysqli->query($newUser) or die($mysqli->error);

    if($result == true){
        return $response->withStatus(200)->withJson($success);
    }
    else {
        return $response->withStatus(404)->withJson($error);
    }
});



