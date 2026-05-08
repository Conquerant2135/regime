<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\PaiementController;

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



$routes->get('/test-payement', 'PaiementController::testPayement');
$routes->post('/acheter_regime_sport', 'PaiementController::acheterRegimeSport');