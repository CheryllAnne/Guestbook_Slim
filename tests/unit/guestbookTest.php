<?php
/**
 * Created by PhpStorm.
 * User: CHERYLLANNE
 * Date: 4/25/2019
 * Time: 5:42 PM
 */
require_once 'app/controllers/Guestbook.php';
require_once 'app/controllers/Session.php';

use controller\Guestbook;
use utility\Session;
use \PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;


class guestbookTest extends TestCase {

    /**
     * @var App
     */
    protected $app, $session;
    private $guestBook;

    /**
     * @var Guestbook
     */
    protected function setUp():void
    {
        parent::setUp();

        //$this->app = new \Slim\App();
//        $this->session = new Session();
        $this->guestBook = new Guestbook($this->session);
    }

    protected function getRequest( string $method, string $path, array $data = [] ) {
        $method = strtoupper( $method );
        $request = Request::createFromEnvironment( Environment::mock( [
            'REQUEST_METHOD' => strtoupper( $method ),
            'REQUEST_URI' => $path,
            'QUERY_STRING' => ( $method == "GET" ) ? http_build_query( $data ) : "",
        ] ) );
        $request = $request->withHeader( 'Content-Type', 'application/json' );
        if ( $method == "POST" ) {
            $request->getBody()->write( json_encode( $data ) );
        }
        //if($method == "DELETE"){}
        return $request;
    }


    /**
     * @param RequestInterface $request
     * @param App|null $app
     * @return Response
     * @throws Exception
     */
//    protected function sendHttpRequest( \Psr\Http\Message\ServerRequestInterface $request, App $app = null ): Response {
//////        if ( !$app ) {
//////            $app = container()->get( App::class );
//////        }
////
////        //$response = $app->process( $request, new Response() );
////        $response = new Response();
////        return $response;
////    }
    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testViewEntries() {

        $request = $this->getRequest( 'get', '/v1/posts/view' );
//        $request = Request::createFromEnvironment($env);
        //$response = $this->sendHttpRequest( $request, $this->app );
        $response = new Response();
        $result = $this->guestBook->viewEntries($request, $response);
        //$response = $this->sql->viewEntries();
        //$this->assertEquals;
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testNewEntry() {

        $data = array('name'=> 'Tuesday',
                'email'=> 'tue@april.com',
                'comment'=> 'tuesdayyy' );

        $request = $this->getRequest( 'post', '/v1/posts/new', $data );
        $response = new Response();
        $result = $this->guestBook->newEntry($request, $response);
        $this->assertEquals(200, $result->getStatusCode());

    }

    //delete function
    public function Delete() {

        $data = array('id'=> '2');
        $request = $this->getRequest( 'delete', '/v1/posts/delete/' , $data );
        $response = new Response();
        $args = $data;
        $result = $this->guestBook->delete($request, $response, $args);
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function testViewUser(){
        $request = $this->getRequest( 'get', '/v1/user/view/' );
        $response = new Response();
        $result = $this->guestBook->viewUser($request, $response);
        $this->assertEquals(200, $result->getStatusCode());
        //$this->assertEquals(20, count($this->guestBook->viewUser()));

    }

    public function RegisterUser() {

        $data = array('username'=> 'Tuesday',
            'email'=> 'tue@april.com',
            'password'=> 'TUESDAY' );

        $request = $this->getRequest( 'post', '/v1/user/register', $data );
        $response = new Response();
        $result = $this->guestBook->register($request, $response);
        $this->assertEquals(200, $result->getStatusCode());

    }

    /// checkk
    public function DeleteUser() {

        //$id = $args['id'];
        $data = array('id'=> '5');
        $request = $this->getRequest( 'delete', '/v1/user/delete/' , $data );
        $response = new Response();
        $args = $data;
        $result = $this->guestBook->deleteUser($request, $response, $args);
        $this->assertEquals(200, $result->getStatusCode());
       // $id = $args['id'];
    }
}

