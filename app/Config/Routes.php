<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->match(['get', 'post'], 'login', 'Auth::login');
$routes->match(['get', 'post'], 'registro', 'Auth::registro');
$routes->get('salir', 'Auth::salir');
$routes->match(['get', 'post'], 'recuperar', 'Auth::recuperar');
$routes->match(['get', 'post'], 'verificar', 'Auth::verificar');

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Arena::index');
    $routes->get('arena', 'Arena::index');
    $routes->post('arena/iniciar', 'Arena::iniciar');
    $routes->post('arena/jefe', 'Arena::iniciarJefe');
    $routes->get('arena/duelo/(:num)', 'Arena::duelo/$1');
    $routes->post('arena/jugar', 'Arena::jugar');
    $routes->get('mapa', 'Mapa::index');
    $routes->get('mantenedor', 'Mantenedor::index');
    $routes->match(['get', 'post'], 'mantenedor/editar/(:num)', 'Mantenedor::editar/$1');
    $routes->post('mantenedor/eliminar/(:num)', 'Mantenedor::eliminar/$1');
});
