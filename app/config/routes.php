<?php

use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\middlewares\LoginMiddleware;
use app\middlewares\WeatherstationUpdateMiddleware;
use app\records\DatapointRecord;
use app\controllers\DataPointsController;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware

    // Home and Login routes
    $router->group('', function(Router $router) {
        // Disable home while we find a use to it.
        //$router->get('/', \app\controllers\HomeController::class . '->index')->setAlias('home');
        $router->get('/', \app\controllers\StationController::class . '->index')->setAlias('home');
        // Login
        $router->get('/login', \app\controllers\LoginController::class . '->index')->setAlias('login');;
        $router->post('/login', \app\controllers\LoginController::class . '->authenticate')->setAlias('login_authenticate');;
        $router->get('/logout', \app\controllers\LogoutController::class . '->index')->setAlias('logout');;
        $router->get('/manifest.webmanifest', \app\controllers\PWAController::class . '->pwa');
    }, [ SecurityHeadersMiddleware::class]);
	
    // Station
    $router->group('/station', function(Router $router) {    
        $router->get('', \app\controllers\StationController::class . '->index')->setAlias('station');
        $router->get('/@station_id', \app\controllers\StationController::class . '->show')->setAlias('station_show');
        $router->get('-create', \app\controllers\StationController::class . '->create')->setAlias('station_create')->addMiddleware(LoginMiddleware::class);
        $router->post('', \app\controllers\StationController::class . '->store')->setAlias('station_store')->addMiddleware(LoginMiddleware::class);
        $router->get('/@station_id/edit', \app\controllers\StationController::class . '->edit')->setAlias('station_edit')->addMiddleware(LoginMiddleware::class);
        $router->post('/@station_id/edit', \app\controllers\StationController::class . '->update')->setAlias('station_update')->addMiddleware(LoginMiddleware::class);
        $router->get('/@station_id/delete', \app\controllers\StationController::class . '->destroy')->setAlias('station_delete')->addMiddleware(LoginMiddleware::class);
        $router->get('/@station_id/evolution', \app\controllers\StationController::class . '->evolution')->setAlias('station_evolution')->addMiddleware(LoginMiddleware::class);
        
        // Datapoints JSON API
        $router->get('/@station_id/daily-temp', DataPointsController::class . '->dailytemp')->setAlias('data_daily_temperature')->addMiddleware(LoginMiddleware::class);
        $router->get('/@station_id/daily-press', DataPointsController::class . '->dailypress')->setAlias('data_daily_pressure')->addMiddleware(LoginMiddleware::class);
        $router->get('/@station_id/daily-humid', DataPointsController::class . '->dailyhumid')->setAlias('data_daily_humidity')->addMiddleware(LoginMiddleware::class);
    }, [ SecurityHeadersMiddleware::class]);
    
    // Weatherstation
    $router->group('/weatherstation', function(Router $router) {
        $router->get('/updateweatherstation.php', \app\controllers\StationController::class . '->updateweatherstation');
    }, [ SecurityHeadersMiddleware::class, WeatherstationUpdateMiddleware::class]);
    
    // User
    $router->group('/user', function(Router $router) {
        $router->get('', \app\controllers\UserController::class . '->index')->setAlias('user');
        $router->get('/@id/edit', \app\controllers\UserController::class . '->edit')->setAlias('user_edit');
        $router->post('/@id/edit', \app\controllers\UserController::class . '->update')->setAlias('user_update');
        $router->get('/@id/delete', \app\controllers\UserController::class . '->destroy')->setAlias('user_delete');
        $router->get('/create', \app\controllers\UserController::class . '->create')->setAlias('user_create');
        $router->post('', \app\controllers\UserController::class . '->store')->setAlias('user_store');
    }, [ SecurityHeadersMiddleware::class, LoginMiddleware::class]);

    
    // Install procedures
    $router->group('/install', function(Router $router) {
        $router->get('', \app\controllers\InstallController::class . '->index')->setAlias('install');
    }, [ SecurityHeadersMiddleware::class]);
    
