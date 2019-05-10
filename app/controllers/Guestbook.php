<?php

namespace composer;
use utility\Session;
use Psr\Container\ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
error_reporting(E_ALL ^ E_NOTICE);
/**
 * Created by PhpStorm.
 * User: CHERYLL ANNE
 * Date: 4/12/2019
 * Time: 4:39 PM
 */
require_once 'Session.php';

class AppController{

    protected $value = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->value[$name];
    }
}


class Guestbook extends AppController
{

    private $database = null;

    protected function connect(){
        $mysqli = mysqli_connect("localhost:3306", "root", "root", "guestbook") or die(mysqli_error($mysqli));
        return $mysqli;
    }

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        //$this->session = new \utility\Session();
        //$this->session;
        $this->session = $container->get('session');
        //$this->session = $session;
        $this->database = $this->connect();
    }

    public function viewEntries($request, $response){

        //$this->checkEntry($response);
        /*if ($this->session->check('logged_id') == false) {
            $error = "Please login!";
            return $response->withStatus(404)->withJson($error);
            exit;
        }*/


        //$mysqli = $this->connect();
        $query = "Select * from guestList order by id";
        $result = $this->database->query($query); // use this for all the functions

        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if(isset($data)) {
            header('Content-Type: application/json');
            echo json_encode($data);
        }

        return $response;
    }


    public function newEntry(Request $request, Response $response) {

//        if ($this->session->check('logged_id') == false) {
//            $error = "Please login!";
//            return $response->withStatus(404)->withJson($error);
//            exit;
//        }

        $mysqli = $this->connect();
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
            return $response->withStatus(400)->withJson($error);
        }

        exit;
    }


    public function delete($request, $response, $args) {

        if ($this->session->check('logged_id') == false) {
            $error = "Please login!";
            return $response->withStatus(404)->withJson($error);
            exit;
        }

        $mysqli = $this->connect();
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
            return $response->withStatus(400)->withJson($error);
        }
    }


    public function edit(Request $request, Response $response, $args){

        if ($this->session->check('logged_id') == false) {
            $error = "Please login!";
            return $response->withStatus(404)->withJson($error);
            exit;
        }

        $mysqli = $this->connect();
        $id = $args['id'];
        $error = "Editing Fail!!";
        $success = "Successfully Edited";
        $comment = json_decode($request->getBody())->comment;
        $edit = "UPDATE `guestList` SET comment = '$comment' WHERE id = ('$id')";
        $result = $mysqli->query($edit) or die($mysqli->error);

        if($result == true){
            return $response->withStatus(200)->withJson($success);
        }
        else{
            return $response->withStatus(400)->withJson($error);
        }
    }
    /*********************************************************************
     **************************** USERS **********************************
     *********************************************************************/
    public function login(Request $request, Response $response){

        $mysqli = $this->connect();
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
    }

    public function viewUser(Request $request, Response $response){

        $mysqli = $this->connect();
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
    }

    public function deleteUser($request, $response, $args){

        $mysqli = $this->connect();
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
    }

    public function editUser(Request $request, Response $response, $args){

        $mysqli = $this->connect();
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
    }

    public function register(Request $request, Response $response){

        $mysqli = $this->connect();
        $error = "An error has occured! Please try again";
        $success = "New user added!";

        $username = json_decode($request->getBody())->username; //striptags to avoid cross site scripting
        $email = json_decode($request->getBody())->email;
        $password = json_decode($request->getBody())->password;
        //$password =password_hash($password1, PASSWORD_DEFAULT);
        $newUser = "Insert into `user` (username, email, password) VALUES ('$username', '$email', '$password')";
        $result = $mysqli->query($newUser) or die($mysqli->error);

        if($result == true){
            return $response->withStatus(200)->withJson($success);
        }
        else {
            return $response->withStatus(404)->withJson($error);
        }
    }

    public function logout($response){
        $logOut = $this->session->unsetSession();
        $exit = "You have exited the page";
        if($logOut == true) {
            return $response->withStatus(200)->withJson($exit);
        }
    }

}






