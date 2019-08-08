<?php

namespace controller;
use PDO;
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



    public function __construct()//ContainerInterface $container
    {
        //$this->container = $container;
        //$this->session = $container->get('session');

        $this->database = $this->connect();
    }



    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->value[$name];
    }
}


class Guestbook extends AppController
{

    protected $database;
    protected $value = array();
    //protected  $session;

//    public function __construct(ContainerInterface $session){
//        $this->session = $session;
//        //$this->database = $this->connect();
//    }

    protected function connect(){

        $servername = "localhost:3306";
        $username = "root";
        $password = "root";

        $mysqli = new PDO("mysql:host=$servername;dbname=guestbook;charset=utf8mb4", $username, $password);
        return $mysqli;
    }

    public function __construct(  ) {   //ContainerInterface $container
        //parent::__construct( $container );
        $this->database = $this->connect();
        $this->session = new Session();
    }


//
//    public function __construct(ContainerInterface $container) {
//        $this->container = $container;
//        //$this->session = new \utility\Session();
//        //$this->session;
//
//        //$this->session = $session;
//    }

    public function viewEntries($request, $response) {

        //$this->checkEntry($response);
//        if ($this->session->check('logged_id') == false) {
//            $error = "Please login!";
//            return $response->withStatus(404)->withJson($error);
//            exit;
//        }
//        $mysqli = $this->connect();

        $query = "Select * from guestList order by id";
        $result = $this->database->query($query); // use this for all the functions

        if ($result == true) {
            while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
                $data[] = $row;
            }

            if ( isset( $data ) ) {
                header( 'Content-Type: application/json' );
                echo json_encode( $data );
                return $response->withJson(200);
            }
        } else{
            return $response->withJson(400);
        }
    }


    public function newEntry(Request $request, Response $response) {

        if ($this->session->check('logged_id') == false) {
            $error = "Please login!";
            return $response->withStatus(404)->withJson($error);
            exit;
        }

        //$mysqli = $this->connect();
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
        $addNew = "Insert into guestList (name, email, comment, image, type, time, date) VALUES (???????)";
        //$result = $mysqli->query($addNew) or die($mysqli->error);
        $result = $this->database->prepare($addNew)or die($this->database->error);
//        $result->bindParam("name", $name);
////        $result->bindParam("email", $email);
////        $result->bindParam("comment", $comment);
        $result->execute([$name, $email, $comment, $image, $file_type, $time, $date]);

        if($result == true){
            return $response->withStatus(200)->withJson($success);
        }
        else {
            return $response->withStatus(400)->withJson($error);
        }

    }


    public function delete($request, $response, $args) {

        if ($this->session->check('logged_id') == false) {
            $error = "Please login!";
            return $response->withStatus(404)->withJson($error);
            exit;
        }

//        $mysqli = $this->connect();
        //$id = $request->getAttribute('id');
        $id = $args['id'];
        $error = "Post doesn't exist";
        $success = "Successfully deleted";
        $delete = "DELETE FROM guestList WHERE id=$id";
        $result = $this->database->prepare($delete) or die($this->database->error);
        $result->bindValue('id', $id);
        $result->execute();

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

        $id = $args['id'];
        $error = "Editing Fail!!";
        $success = "Successfully Edited";
        $comment = json_decode($request->getBody())->comment;
        $edit = "UPDATE `guestList` SET comment = ? WHERE id = ? ";
        $result = $this->database->prepare($edit) or die($this->database->error);
        $result->execute([$comment, $id]);

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

        $error = "An error has occured! Please try again";
        $success = "Logged In!";

        $username = json_decode($request->getBody())->username; //striptags to avoid cross site scripting
        //$password = json_decode($request->getBody())->password;
        //$password = password_hash($password1, PASSWORD_DEFAULT);
        $login = "SELECT * FROM `user` WHERE username = ? "; ///////////
        $result = $this->database->prepare($login) or die($this->database->error);
        $result->execute([$username]);

        if($result == true){
            $this->session->set('logged_id', true);
            return $response->withStatus(200)->withJson($success);
        }
        else {
            return $response->withStatus(404)->withJson($error);
        }
    }

    public function viewUser(Request $request, Response $response){

        $query = "Select * from `user` order by id";
        $result = $this->database->query( $query );

        if ($result == true) {

            while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
                $users[] = $row;
            }

            //var_dump($users);
            if ( isset( $users ) ) {
                header( 'Content-Type: application/json' );
                echo json_encode( $users );
                return $response->withStatus(200);

            }
        }else{
            return $response->withJson(400);
        }

            //return $users;
    }

    public function deleteUser($request, $response, $args){

        //$id = $request->getAttribute('id');
        $id = $args['id'];
        $error = "User doesn't exist";
        $success = "User deleted";

        $delete = "DELETE FROM `user` WHERE id=$id";
        $result = $this->database->prepare($delete) or die($this->database->error);
        $result->bindValue('id', $id);
        $result->execute();

        if ($result == true) {
            return $response->withStatus(200)->withJson($success);
        } else {
            return $response->withStatus(404)->withJson($error);
        }
    }

    public function editUser(Request $request, Response $response, $args){

        $id = $args['id'];
        $error = "Edit unsuccessful";
        $success = "Successfully Edited";
        $username = json_decode($request->getBody())->username;

        $edit = "UPDATE `user` SET username = ? WHERE id = ? ";
        $result = $this->database->prepare($edit) or die($this->database->error);
        $result->bindValue('id', $id);
        $result->execute([$username]);


        if($result == true)
            return $response->withStatus(200)->withJson($success);

        else
            return $response->withStatus(404)->withJson($error);

        exit;
    }

    public function register(Request $request, Response $response){

        $error = "An error has occured! Please try again";
        $success = "New user added!";

        $username = json_decode($request->getBody())->username; //striptags to avoid cross site scripting
        $email = json_decode($request->getBody())->email;
        $password = json_decode($request->getBody())->password;
        //$password =password_hash($password1, PASSWORD_DEFAULT);
        $newUser = "Insert into `user` (username, email, password) VALUES (???)";
        $result = $this->database->prepare($newUser) or die($this->database->error);
        $result->execute([$username, $email, $password]);

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






