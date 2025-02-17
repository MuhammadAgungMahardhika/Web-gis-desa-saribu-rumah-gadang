<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
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
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/api/dbCheck', 'Home::dbCheck');
$routes->get('/', 'LandingPage::index');
$routes->get('/403', 'Home::error403');
$routes->get('/login', 'Web\Profile::login');
$routes->get('/register', 'Web\Profile::register');

// Upload files
$routes->group('upload', ['namespace' => 'App\Controllers\Web'], function ($routes) {
    $routes->post('photo', 'Upload::photo');
    $routes->post('video', 'Upload::video');
    $routes->post('avatar', 'Upload::avatar');
    $routes->delete('avatar', 'Upload::remove');
    $routes->delete('photo', 'Upload::remove');
    $routes->delete('video', 'Upload::remove');
});

// App
$routes->group('web', ['namespace' => 'App\Controllers\Web'], function ($routes) {
    $routes->get('homeStay/', 'RumahGadang::index2');

    $routes->get('rumahGadang/maps', 'RumahGadang::maps');
    $routes->get('rumahGadang/detail/(:segment)', 'RumahGadang::detail/$1');
    $routes->presenter('rumahGadang');
    $routes->get('/', 'RumahGadang::recommendation');
    $routes->get('event/maps', 'Event::maps');
    $routes->get('event/detail/(:segment)', 'Event::detail/$1');
    $routes->presenter('event');
    $routes->get('package/maps', 'Package::maps');
    $routes->get('package/detail/(:segment)', 'Package::detail/$1');
    $routes->get('package/costum/new', 'Package::newCostum/$1');
    $routes->post('package/costum/saveCostum', 'Package::saveCostum');
    $routes->get('package/costumExisting/(:segment)', 'Package::costumExisting/$1');
    $routes->post('package/saveCostumExisting', 'Package::saveCostumExisting');
    $routes->presenter('package');
    $routes->presenter('uniquePlace');
    $routes->get('visitHistory', 'VisitHistory::visitHistory', ['filter' => 'role:user']);
    $routes->get('visitHistory/add', 'VisitHistory::addVisitHistory', ['filter' => 'role:user']);
    $routes->post('visitHistory', 'VisitHistory::visitHistory', ['filter' => 'role:user']);
    $routes->post('review', 'Review::add', [['filter' => 'role:user']]);
    $routes->post('reviewPackage', 'Review::ratingCommentPackage', [['filter' => 'role:user']]);
    // $routes->post('reviewPackage', 'Review::addPackage', [['filter' => 'role:user']]);
    $routes->get('reservation/check/(:segment)/(:segment)', 'Reservation::check/$1/$2', ['filter' => 'role:user,admin']);
    $routes->get('reservation/checkHomestay/(:segment)/(:segment)/(:segment)', 'Reservation::checkHomestay/$1/$2/$3', ['filter' => 'role:user,admin']);
    $routes->get('reservation/checkHomestay/(:segment)/(:segment)', 'Reservation::checkHomestay/$1/$2', ['filter' => 'role:user,admin']);
    $routes->presenter('reservation');
    $routes->post('reservation/create', 'Reservation:create', ['filter' => 'role:user,admin']);

    // Profile
    $routes->group('profile', function ($routes) {
        $routes->get('/', 'Profile::profile', ['filter' => 'login']);
        $routes->get('changePassword', 'Profile::changePassword', ['filter' => 'login']);
        $routes->post('changePassword', 'Profile::changePassword', ['filter' => 'login']);
        $routes->get('update', 'Profile::updateProfile', ['filter' => 'login']);
        $routes->post('update', 'Profile::update', ['filter' => 'login']);
    });

    // invoice
    $routes->group('pdf', function ($routes) {
        $routes->post('invoice-data', 'PdfGenerator::getInvoiceData', ['filter' => 'role:user,admin']);
        $routes->get('invoice/(:segment)', 'PdfGenerator::invoice/$1', ['filter' => 'role:user,admin']);
        $routes->post('ticket-data', 'PdfGenerator::getTicketData', ['filter' => 'role:user,admin']);
        $routes->get('ticket/(:segment)', 'PdfGenerator::ticket/$1', ['filter' => 'role:user,admin']);
    });
});


// Dashboard
$routes->group('dashboard', ['namespace' => 'App\Controllers\Web', 'filter' => 'role:admin'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::dashboard',  ['filter' => 'role:admin']);
    $routes->get('reservation', 'Dashboard::reservation',  ['filter' => 'role:admin']);
    $routes->get('rumahGadang', 'Dashboard::rumahGadang',  ['filter' => 'role:admin']);
    $routes->get('atraction', 'Dashboard::atraction',  ['filter' => 'role:admin']);
    $routes->get('homestay', 'Dashboard::homestay',  ['filter' => 'role:admin']);
    $routes->get('package', 'Dashboard::package',  ['filter' => 'role:admin']);
    $routes->get('service', 'Dashboard::service',  ['filter' => 'role:admin']);
    $routes->get('event', 'Dashboard::event',  ['filter' => 'role:admin']);
    $routes->get('event', 'Dashboard::event',  ['filter' => 'role:admin']);
    $routes->get('uniquePlace', 'Dashboard::uniquePlace',  ['filter' => 'role:admin']);
    $routes->get('facility', 'Dashboard::facility', ['filter' => 'role:admin']);
    $routes->get('atractionFacility', 'Dashboard::atractionFacility', ['filter' => 'role:admin']);
    $routes->get('homestayFacility', 'Dashboard::homestayFacility', ['filter' => 'role:admin']);
    $routes->get('homestayUnitFacility', 'Dashboard::homestayUnitFacility', ['filter' => 'role:admin']);
    $routes->get('homestayUnit', 'Dashboard::homestayUnit', ['filter' => 'role:admin']);
    $routes->get('packageType', 'Dashboard::packageType', ['filter' => 'role:admin']);

    $routes->get('recommendation', 'Dashboard::recommendation',  ['filter' => 'role:admin']);
    $routes->get('users', 'Dashboard::users', ['filter' => 'role:admin']);

    $routes->presenter('rumahGadang',  ['filter' => 'role:admin']);
    $routes->presenter('atraction',  ['filter' => 'role:admin']);
    $routes->presenter('atractionFacility', ['filter' => 'role:admin']);
    $routes->presenter('packageType', ['filter' => 'role:admin']);
    $routes->presenter('homestayUnitFacility', ['filter' => 'role:admin']);
    $routes->presenter('homestayUnit', ['filter' => 'role:admin']);
    $routes->presenter('homestay',  ['filter' => 'role:admin']);
    $routes->presenter('homestayFacility',  ['filter' => 'role:admin']);
    $routes->presenter('dashboard',  ['filter' => 'role:admin']);
    $routes->presenter('reservation',  ['filter' => 'role:admin']);
    $routes->presenter('package',  ['filter' => 'role:admin']);
    $routes->presenter('service',  ['filter' => 'role:admin']);

    $routes->presenter('event',  ['filter' => 'role:admin']);
    $routes->presenter('uniquePlace',  ['filter' => 'role:admin']);
    $routes->presenter('facility', ['filter' => 'role:admin']);
    $routes->presenter('users', ['filter' => 'role:admin']);
});

// API
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->resource('rumahGadang');
    $routes->get('recommendation', 'RumahGadang::recommendation');
    $routes->post('recommendationOwner', 'RumahGadang::recommendationByOwner');
    $routes->get('recommendationList', 'RumahGadang::recommendationList');
    $routes->post('recommendation', 'RumahGadang::updateRecommendation');
    $routes->post('rumahGadangOwner', 'RumahGadang::listByOwner');
    $routes->post('rumahGadang/findByName', 'RumahGadang::findByName');
    $routes->post('rumahGadang/findByRadius', 'RumahGadang::findByRadius');
    $routes->post('rumahGadang/findByFacility', 'RumahGadang::findByFacility');
    $routes->post('rumahGadang/findByRating', 'RumahGadang::findByRating');
    $routes->post('rumahGadang/findByCategory', 'RumahGadang::findByCategory');
    $routes->get('rumahGadang/maps', 'RumahGadang::maps');
    $routes->get('event/category', 'Event::category');
    $routes->resource('atraction');
    $routes->post('atraction/findByRadius', 'Atraction::findByRadius');
    $routes->resource('homestay');
    $routes->resource('event');
    $routes->post('eventOwner', 'Event::listByOwner');
    $routes->post('event/findByName', 'Event::findByName');
    $routes->post('event/findByRadius', 'Event::findByRadius');
    $routes->post('event/findByRating', 'Event::findByRating');
    $routes->post('event/findByCategory', 'Event::findByCategory');
    $routes->post('event/findByDate', 'Event::findByDate');
    $routes->resource('uniquePlace');
    $routes->post('uniquePlaceOwner', 'UniquePlace::listByOwner');
    $routes->post('uniquePlace/findByName', 'UniquePlace::findByName');
    $routes->post('uniquePlace/findByRadius', 'UniquePlace::findByRadius');
    $routes->post('uniquePlace/findByRating', 'UniquePlace::findByRating');
    $routes->resource('culinaryPlace');
    $routes->post('culinaryPlaceOwner', 'CulinaryPlace::listByOwner');
    $routes->post('culinaryPlace/findByRadius', 'CulinaryPlace::findByRadius');
    $routes->resource('worshipPlace');
    $routes->post('worshipPlaceOwner', 'WorshipPlace::listByOwner');
    $routes->post('worshipPlace/findByRadius', 'WorshipPlace::findByRadius');
    $routes->resource('souvenirPlace');
    $routes->post('souvenirPlaceOwner', 'SouvenirPlace::listByOwner');
    $routes->post('souvenirPlace/findByRadius', 'SouvenirPlace::findByRadius');
    $routes->resource('account');
    $routes->post('account/profile', 'Account::profile');
    $routes->post('account/changePassword', 'Account::changePassword');
    $routes->post('account/visitHistory', 'Account::visitHistory');
    $routes->post('account/newVisitHistory', 'Account::newVisitHistory');
    $routes->post('account/(:num)', 'Account::update/$1');
    $routes->resource('review');
    $routes->resource('user');
    $routes->get('owner', 'User::owner');
    $routes->resource('facility');
    $routes->resource('atractionFacility');
    $routes->resource('homestayFacility');
    $routes->resource('homestayUnitFacility');
    $routes->resource('homestayUnit');
    $routes->resource('package');
    $routes->resource('packageType');
    $routes->resource('packageDay');
    $routes->get('objects/package_day/(:segment)', 'PackageObject::getObjectsByPackageDayId/$1');
    $routes->resource('service');

    $routes->resource('reservation');
    $routes->post('reservation', 'Reservation:create');
    $routes->post('village', 'Village::getData');
    $routes->post('login', 'Profile::attemptLogin');
    $routes->post('profile', 'Profile::profile');
    $routes->get('logout', 'Profile::logout');
});

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
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
