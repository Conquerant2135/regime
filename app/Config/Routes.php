<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/regime-sport', 'RegimeSportController::getRegimeSport');

$routes->get('/login', 'AuthController::loginForm');
$routes->post('/login','AuthController::login');
$routes->get('/inscription/contact' , 'AuthController::inscriptionFormContact');
$routes->post('/inscription/info' , 'AuthController::inscriptionFormInfoPerso');
$routes->post('/inscription' , 'AuthController::inscription');

// grouper dans une route de ce style les chemins destinees aux personnes qui
// sont connectés et les users simple (peut etre les admins ne doivent pas passer ici aussi)
$routes->group('',['filter' => 'auth'] , function ($routes) {
    $routes->get('home','AuthController::testFilters');
});

// groupe de route accessible uniquement pour ceux qui on un role d'admin
$routes->group('admin', ['filter' => 'admin'] , function ($routes){
    $routes->get('dashboard','AdminController::dashboard');
});

$routes->get('/logout', 'AuthController::logout');
