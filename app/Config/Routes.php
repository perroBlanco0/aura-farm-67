<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Arena::index');
$routes->get('arena', 'Arena::index');
$routes->post('arena/iniciar', 'Arena::iniciar');
$routes->get('arena/duelo/(:num)', 'Arena::duelo/$1');
$routes->post('arena/jugar', 'Arena::jugar');
