<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('new_page', 'Auth::index');
$routes->get('privacy_policy', 'Privacy::index');
$routes->get('strava/connectToStrava', 'Strava::connectToStrava');
$routes->get('strava/callback', 'Strava::callback');
$routes->get('leaderboard', 'Leaderboard::index'); 

