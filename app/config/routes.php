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
        $router->get('/', \app\controllers\StationController::class . '->index')->setAlias('home')->addMiddleware(LoginMiddleware::class);
        // Login
        $router->get('/login', \app\controllers\LoginController::class . '->index')->setAlias('login');;
        $router->post('/login', \app\controllers\LoginController::class . '->authenticate')->setAlias('login_authenticate');;
        $router->get('/logout', \app\controllers\LogoutController::class . '->index')->setAlias('logout');;
        $router->get('/manifest.webmanifest', \app\controllers\PWAController::class . '->pwa')->setAlias('pwa');
        $router->get('/station/@station_id/manifest.webmanifest', \app\controllers\PWAController::class . '->stationpwa')->setAlias('stationpwa');
    }, [ SecurityHeadersMiddleware::class]);
	
    // Station
    $router->group('/station', function(Router $router) {    
        $router->get('', \app\controllers\StationController::class . '->index')->setAlias('station');
        $router->get('/@station_id', \app\controllers\StationController::class . '->show')->setAlias('station_show');
        $router->get('-create', \app\controllers\StationController::class . '->create')->setAlias('station_create');
        $router->post('', \app\controllers\StationController::class . '->store')->setAlias('station_store');
        $router->get('/@station_id/edit', \app\controllers\StationController::class . '->edit')->setAlias('station_edit');
        $router->post('/@station_id/edit', \app\controllers\StationController::class . '->update')->setAlias('station_update');
        $router->get('/@station_id/delete', \app\controllers\StationController::class . '->destroy')->setAlias('station_delete');
        $router->get('/@station_id/evolution', \app\controllers\StationController::class . '->evolution')->setAlias('station_evolution');
        $router->get('/@station_id/evolution-week', \app\controllers\StationController::class . '->evolution_week')->setAlias('station_evolution_week');
        $router->get('/@station_id/evolution-month', \app\controllers\StationController::class . '->evolution_month')->setAlias('station_evolution_month');
        
        // Datapoints JSON API
        $router->get('/@station_id/daily-temp', DataPointsController::class . '->dailytemp')->setAlias('data_daily_temperature');
        $router->get('/@station_id/daily-press', DataPointsController::class . '->dailypress')->setAlias('data_daily_pressure');
        $router->get('/@station_id/daily-humid', DataPointsController::class . '->dailyhumid')->setAlias('data_daily_humidity');
        $router->get('/@station_id/daily-indoortemp', DataPointsController::class . '->dailyindoortemp')->setAlias('data_daily_indoortemp');
        $router->get('/@station_id/daily-rain', DataPointsController::class . '->dailyrain')->setAlias('data_daily_rain');
        $router->get('/@station_id/weekly-temp', DataPointsController::class . '->weeklytemp')->setAlias('data_weekly_temperature');
        $router->get('/@station_id/weekly-press', DataPointsController::class . '->weeklypress')->setAlias('data_weekly_pressure');
        $router->get('/@station_id/weekly-humid', DataPointsController::class . '->weeklyhumid')->setAlias('data_weekly_humidity');
        $router->get('/@station_id/weekly-indoortemp', DataPointsController::class . '->weeklyindoortemp')->setAlias('data_weekly_indoortemp');
        $router->get('/@station_id/weekly-rain', DataPointsController::class . '->weeklyrain')->setAlias('data_weekly_rain');
        $router->get('/@station_id/monthly-temp', DataPointsController::class . '->monthlytemp')->setAlias('data_monthly_temperature');
        $router->get('/@station_id/monthly-press', DataPointsController::class . '->monthlypress')->setAlias('data_monthly_pressure');
        $router->get('/@station_id/monthly-humid', DataPointsController::class . '->monthlyhumid')->setAlias('data_monthly_humidity');
        $router->get('/@station_id/monthly-indoortemp', DataPointsController::class . '->monthlyindoortemp')->setAlias('data_monthly_indoortemp');
        $router->get('/@station_id/monthly-rain', DataPointsController::class . '->monthlyrain')->setAlias('data_monthly_rain');
        
    }, [SecurityHeadersMiddleware::class, LoginMiddleware::class]);
    
    // User
    $router->group('/user', function(Router $router) {
        $router->get('', \app\controllers\UserController::class . '->index')->setAlias('user');
        $router->get('/@id/edit', \app\controllers\UserController::class . '->edit')->setAlias('user_edit');
        $router->post('/@id/edit', \app\controllers\UserController::class . '->update')->setAlias('user_update');
        $router->get('/@id/delete', \app\controllers\UserController::class . '->destroy')->setAlias('user_delete');
        $router->get('/create', \app\controllers\UserController::class . '->create')->setAlias('user_create');
        $router->post('', \app\controllers\UserController::class . '->store')->setAlias('user_store');
    }, [ SecurityHeadersMiddleware::class, LoginMiddleware::class]);
    
    // Following routes do not require regular authentification:
    
    // Weatherstation
    $router->group('/weatherstation', function(Router $router) {
        $router->get('/updateweatherstation.php', \app\controllers\StationController::class . '->updateweatherstation');
    }, [ SecurityHeadersMiddleware::class, WeatherstationUpdateMiddleware::class]);
        
    // Install procedures
    $router->group('/install', function(Router $router) {
        $router->get('', \app\controllers\InstallController::class . '->index')->setAlias('install');
    }, [ SecurityHeadersMiddleware::class]);
    
