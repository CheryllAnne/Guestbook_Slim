<?php

use Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface;
use utility\Session;
use composer\Guestbook;

require_once dirname(__FILE__) . '/controllers/Session.php';
require_once dirname(__FILE__, 2) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/controllers/Guestbook.php';

// instantiate the App object
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

$container = $app->getContainer();

$container['session'] = function ($container) {
    return new Session();
};

$container['guestbook'] = function ($container) {
    $guestbook = new Guestbook();
    return $guestbook;
};

$container['HomeController'] = function ($container) {
    return new \App\controllers\HomeController($container->view);
};

/*************** Routes ******************/

/*************** GUESTBOOK ENTRIES ******************/
$app->group('/v1/posts', function () use ($app) {

    /*if ($this->session->check('logged_id') == false) {
        $error = "Please login!";
        return $response->withStatus(404)->withJson($error);
        exit;
    }*/
    //view all entries in the Guestbook
    $app->get('/view', function(ServerRequestInterface $request, ResponseInterface $response) {
        $response = $response->withStatus(200);
        return $response;

    });
    //submit a new entry
    $app->post('/new', Guestbook::class . ':newEntry');
    //delete an entry
    $app->delete('/delete/{id}', Guestbook::class . ':delete');
    //edit an entry
    $app->put('/edit/{id}', Guestbook::class . ':edit');
});

/******************* USERS **********************/
$app->group('/v1/user', function () use ($app) {
    //view all users
    $app->get('/view', Guestbook::class . ':viewUser');
    //delete a user by id
    $app->delete('/delete/{id}', Guestbook::class . ':deleteUser');
    //edit username of a user
    $app->put('/edit/{id}', Guestbook::class . ':editUser');
    //register user
    $app->post('/register', Guestbook::class . ':register');
    //login
    $app->post('/login', Guestbook::class . ':login');
    //logout
    $app->post('/logout', Guestbook::class . ':logout');

});

// Run application
$app->run();