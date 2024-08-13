<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('new_page', 'Auth::index');
$routes->get('privacy', 'Privacy::index');
$routes->get('strava/connectToStrava', 'Strava::connectToStrava');
$routes->get('strava/callback', 'Strava::callback');
$routes->get('analytics', 'Analytics::index'); 
$routes->get('chart_analytics', 'ChartAnalytics::index');
$routes->get('leaderboard', 'LeaderboardHdr::index');
$routes->get('leaderboardstage', 'Leaderboard::index');
$routes->get('individualboard', 'IndividualStatistics::index');
$routes->get('stage', 'LeaderboardController::index');
$routes->get('config', 'ChallengeConfig::index'); 
$routes->get('faqs', 'Privacy::faqs');
$routes->get('activities', 'ActivitiesController::fullActivities');
