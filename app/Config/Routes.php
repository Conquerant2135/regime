<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\PaiementController;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->post('/imc/calculer', 'Home::calculerImc');
$routes->get('/regime-sport', 'RegimeSportController::getRegimeSport');
$routes->post('/regime-sport/set-objectif', 'RegimeSportController::setObjectif', ['filter' => 'auth']);
$routes->get('/mon-regime', 'RegimeSportController::monRegime', ['filter' => 'auth']);
$routes->get('/mon-compte', 'PortefeuilleController::compte', ['filter' => 'auth']);

$routes->get('/login', 'AuthController::loginForm');
$routes->post('/login', 'AuthController::login');
$routes->get('/inscription/contact', 'AuthController::inscriptionFormContact');
$routes->post('/inscription/info', 'AuthController::inscriptionFormInfoPerso');
$routes->post('/inscription', 'AuthController::inscription');

$routes->get('/test-payement', 'PaiementController::testPayement');
$routes->post('/acheter_regime_sport', 'PaiementController::acheterRegimeSport');
$routes->post('/options/souscrire-gold', 'PaiementController::souscrireGold');

// Routes Portefeuille
$routes->get('/portefeuille', 'PortefeuilleController::index', ['filter' => 'auth']);
$routes->post('/portefeuille/utiliser-code', 'PortefeuilleController::utiliserCode');

// Routes Debug (à supprimer après testing)
$routes->get('/debug_wallet', 'DebugController::wallet');
$routes->post('/debug_wallet_ajax', 'DebugController::walletAjax');
$routes->get('/test_ajax', function () {
    return view('test_ajax');
});
$routes->get('/debug_objectives', 'DebugController::objectives');

// grouper dans une route de ce style les chemins destinees aux personnes qui
// sont connectés et les users simple (peut etre les admins ne doivent pas passer ici aussi)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('home', 'AuthController::testFilters');
});

// groupe de route accessible uniquement pour ceux qui on un role d'admin
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('regimes', 'AdminController::regimes');
    $routes->post('regimes', 'AdminController::storeRegime');
    $routes->get('regimes/update/(:num)', 'AdminController::editRegime/$1');
    $routes->post('regimes/update/(:num)', 'AdminController::updateRegime/$1');
    $routes->post('regimes/delete/(:num)', 'AdminController::deleteRegime/$1');
    $routes->get('sports', 'AdminController::sports');
    $routes->post('sports', 'AdminController::storeSport');
    $routes->get('sports/update/(:num)', 'AdminController::editSport/$1');
    $routes->post('sports/update/(:num)', 'AdminController::updateSport/$1');
    $routes->post('sports/delete/(:num)', 'AdminController::deleteSport/$1');
});

// l'api admin , verouillage par le filtre
$routes->group('api', ['filter' => 'admin'], function ($routes) {

    $routes->get('userInscription', 'UsersController::countUserByInscriptionApi');
    $routes->get('userDepenses', 'UsersController::depensesParMoisEtAnneeApi');
    $routes->get('annee', 'UsersController::getAnneePresente');

    $routes->group('userRepartition', function ($routes) {
        $routes->get('typeCompte', 'UsersController::countUserByAccoutType');
        $routes->get('imc', 'UsersController::getRepartitionClientByIMC');
        $routes->group('objectif', function ($routes) {
            $routes->get('(:num)', 'UsersController::getRepatitionObjectifClient/$1');
        });
    });

});

$routes->get('/logout', 'AuthController::logout');
