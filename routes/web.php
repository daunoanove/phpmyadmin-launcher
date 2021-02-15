<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return redirect('app');
});

$router->group(['middleware' => [\App\Http\Middleware\AllowedIpAddressesMiddleware::class, \App\Http\Middleware\LauncherMiddleware::class]], function () use ($router) {
    $router->post('/prepare-phpmyadmin-config', 'LauncherController@preparePhpMyAdminConfig');
    $router->get('/instances', 'LauncherController@instances');
    $router->post('/check-instance-connection', 'LauncherController@checkInstanceConnection');
    $router->post('/launch-instance', 'LauncherController@launchInstance');
    $router->get('/logout', 'LauncherController@logout');
});