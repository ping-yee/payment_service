<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
// $routes->get('/', 'Home::index');

$routes->group(
    'api/v1',
    [
        'namespace' => 'App\Controllers\v1',
        'filter'    => 'user'
    ],
    function(\CodeIgniter\Router\RouteCollection $routes)
    {
        //Payment
        $routes->get('payments', 'PaymentController::index');
        $routes->get('payments/(:num)', 'PaymentController::show/$1');
        $routes->post('payments', 'PaymentController::create');
        $routes->put('payments', 'PaymentController::update');
        $routes->delete('payments/(:num)', 'PaymentController::delete/$1');

        //Wallet
        $routes->get('wallet', 'WalletController::show');
        $routes->post('wallet', 'WalletController::create');
        $routes->post('wallet/compensate', 'WalletController::compensate');
    }
);

$routes->group(
    'api/vDtm',
    [
        'namespace' => 'App\Controllers\Dtm',
        'filter'    => 'userDtm'
    ],
    function (\CodeIgniter\Router\RouteCollection $routes) {
        //Payment
        $routes->post('payments/list', 'PaymentController::index');
        $routes->post('payments/show', 'PaymentController::show');
        $routes->post('payments/create', 'PaymentController::create');
        $routes->post('payments/update', 'PaymentController::update');
        $routes->post('payments/delete', 'PaymentController::delete');
        $routes->post('payments/createOrderCompensate', 'PaymentController::createOrderCompensate');

        //Wallet
        $routes->post('wallet/show', 'WalletController::show');
        $routes->post('wallet/create', 'WalletController::create');
        $routes->post('wallet/compensate', 'WalletController::compensate');
    }
);


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
