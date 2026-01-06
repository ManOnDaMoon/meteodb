<?php

use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\middlewares\LoginMiddleware;
use app\middlewares\WeatherstationUpdateMiddleware;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware

    // Home and Login routes
    $router->group('', function(Router $router) {
        $router->get('/', \app\controllers\HomeController::class . '->index')->setAlias('home');
        // Login
        $router->get('/login', \app\controllers\LoginController::class . '->index')->setAlias('login');;
        $router->post('/login', \app\controllers\LoginController::class . '->authenticate')->setAlias('login_authenticate');;
        $router->get('/logout', \app\controllers\LogoutController::class . '->index')->setAlias('logout');;
        
    }, [ SecurityHeadersMiddleware::class]);
	
    // Station
    $router->group('/station', function(Router $router) {    
        $router->get('', \app\controllers\StationController::class . '->index')->setAlias('station');
        $router->get('/@station_id', \app\controllers\StationController::class . '->show');
        $router->get('-create', \app\controllers\StationController::class . '->create')->setAlias('station_create')->addMiddleware(LoginMiddleware::class);
        $router->post('', \app\controllers\StationController::class . '->store')->addMiddleware(LoginMiddleware::class);
        $router->get('/@station_id/edit', \app\controllers\StationController::class . '->edit')->setAlias('station_edit')->addMiddleware(LoginMiddleware::class);
        $router->post('/@station_id/edit', \app\controllers\StationController::class . '->update')->addMiddleware(LoginMiddleware::class);
        $router->get('/@station_id/delete', \app\controllers\StationController::class . '->destroy')->setAlias('delete')->addMiddleware(LoginMiddleware::class);
    }, [ SecurityHeadersMiddleware::class]);
    
    // Weatherstation
    $router->group('/weatherstation', function(Router $router) {
        $router->get('/updateweatherstation.php', \app\controllers\StationController::class . '->updateweatherstation');
    }, [ SecurityHeadersMiddleware::class, WeatherstationUpdateMiddleware::class]);