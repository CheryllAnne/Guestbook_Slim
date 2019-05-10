<?php
/**
 * Created by PhpStorm.
 * User: CHERYLLANNE
 * Date: 4/25/2019
 * Time: 5:42 PM
 */
require_once 'app/controllers/Guestbook.php';
require_once 'app/controllers/Session.php';

use composer\Guestbook;
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
    protected $app;

    /**
     * @var
     */
    protected $stack;
    protected $session;

    /**
     * @var Guestbook
     */
    private $sql;

    /**
     * @var
     */
    protected $guestbook;

    public function setUp(  ): void {
        $this->app = new \Slim\App( [
            'settings' => [
                'displayErrorDetails' => true,
            ]
        ] );

        $this->container = $this->app->getContainer();
        $this->session = new Session();
        $this->sql = new Guestbook(container);

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

        return $request;
    }

    /**
     * @param RequestInterface $request
     * @param App|null $app
     * @return Response
     * @throws Exception
     */
    protected function sendHttpRequest( \Psr\Http\Message\ServerRequestInterface $request, App $app = null ): Response {
        if ( !$app ) {
            $app = container()->get( App::class );
        }

        $response = $app->process( $request, new Response() );
        return $response;
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testViewEntries() {

        $request = $this->getRequest( 'get', '/v1/posts/view' );
        $response = $this->sendHttpRequest( $request, $this->app );
        $result = (new Guestbook())->viewEntries($request, $response);


        //$response = $this->sql->viewEntries();
        $this->assertEquals( count($result), 200 );

    }

//    public function testNewEntry_withAllFields() {
//        $req = $this->getRequest( 'post', '/v1/posts/new', ['name'=> 'Tuesday', 'email'=> 'tue@april.com', 'comment'=> 'tuesdayyy'] );
//
//            $res = $this->sendHttpRequest( $req, $this->app );
//
//
//        $this->assertSame( $res->getStatusCode(), 200 );
//    }
//
}

