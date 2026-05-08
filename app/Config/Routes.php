<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\PaiementController;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/regime-sport', 'RegimeSportController::getRegimeSport', ['filter' => 'auth']);

$routes->get('/login', 'AuthController::loginForm');
$routes->post('/login', 'AuthController::login');
$routes->get('/inscription/contact', 'AuthController::inscriptionFormContact');
$routes->post('/inscription/info', 'AuthController::inscriptionFormInfoPerso');
$routes->post('/inscription', 'AuthController::inscription');



$routes->get('/test-payement', 'PaiementController::testPayement');
$routes->post('/acheter_regime_sport', 'PaiementController::acheterRegimeSport');
$routes->post('/options/souscrire-gold', 'PaiementController::souscrireGold');

// Routes Portefeuille
$routes->get('/portefeuille', 'PortefeuilleController::index');
$routes->post('/portefeuille/utiliser-code', 'PortefeuilleController::utiliserCode');

// Routes Debug (à supprimer après testing)
$routes->get('/debug_wallet', 'DebugController::wallet');
$routes->post('/debug_wallet_ajax', 'DebugController::walletAjax');
$routes->get('/test_ajax', function() { return view('test_ajax'); });

// grouper dans une route de ce style les chemins destinees aux personnes qui
// sont connectés et les users simple (peut etre les admins ne doivent pas passer ici aussi)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('home', 'AuthController::testFilters');
});

// groupe de route accessible uniquement pour ceux qui on un role d'admin
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
});

$routes->get('/logout', 'AuthController::logout');
