<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::loginForm');
$routes->get('/inscription/contact' , 'AuthController::inscriptionFormContact');
$routes->post('/inscription/info' , 'AuthController::inscriptionFormInfoPerso');
$routes->post('/inscription' , 'AuthController::inscription');
