<?php

use app\controllers\ApiExampleController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware


    $router->group('', function(Router $router) {
        $router->get('/', \app\controllers\HomeController::class . '->index');
    }, [ SecurityHeadersMiddleware::class ]);
	
    // Station
    $router->group('/station', function(Router $router) {    
        $router->get('', \app\controllers\StationController::class . '->index');
        $router->get('/create', \app\controllers\StationController::class . '->create');
        $router->post('', \app\controllers\StationController::class . '->store');
        $router->get('/@station_id', \app\controllers\StationController::class . '->show');
        $router->get('/@station_id/edit', \app\controllers\StationController::class . '->edit');
        $router->post('/@station_id/edit', \app\controllers\StationController::class . '->update');
        $router->get('/@station_id/delete', \app\controllers\StationController::class . '->destroy');
    }, [ SecurityHeadersMiddleware::class ]);
    
    // Weatherstation
    $router->group('/weatherstation', function(Router $router) {
        $router->get('/updateweatherstation.php', \app\controllers\StationController::class . '->updateweatherstation');
    }, [ SecurityHeadersMiddleware::class ]);