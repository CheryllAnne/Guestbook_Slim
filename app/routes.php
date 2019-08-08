<?php

use Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface;
use utility\Session;
use controller\Guestbook;

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
    $guestbook = new Guestbook($container);
    return $guestbook;
};

$container['HomeController'] = function ($container) {
    return new \App\controllers\HomeController($container->view);
};

/*************** Routes ******************/

/*************** GUESTBOOK ENTRIES ******************/
$app->group('/v1/posts', function () use ($app) {

    //view all entries in the Guestbook
    $app->get('/view', \controller\Guestbook::class . ':viewEntries');
    //submit a new entry
    $app->post('/new', \controller\Guestbook::class . ':newEntry');
    //delete an entry
    $app->delete('/delete/{id}', \controller\Guestbook::class . ':delete');
    //edit an entry
    $app->put('/edit/{id}', \controller\Guestbook::class . ':edit');
});

/******************* USERS **********************/
$app->group('/v1/user', function () use ($app) {
    //view all users
    $app->get('/view', \controller\Guestbook::class . ':viewUser');
    //delete a user by id
    $app->delete('/delete/{id}', \controller\Guestbook::class . ':deleteUser');
    //edit username of a user
    $app->put('/edit/{id}', \controller\Guestbook::class . ':editUser');
    //register user
    $app->post('/register', \controller\Guestbook::class . ':register');
    //login
    $app->post('/login', Guestbook::class . ':login');
    //logout
    $app->post('/logout', Guestbook::class . ':logout');

});

// Run application
$app->run();