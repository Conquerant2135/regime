<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::loginForm');
$routes->get('/inscription/page1' , 'AuthController::inscriptionForm');
$routes->get('/inscription/page2' , 'AuthController::inscriptionForm');
