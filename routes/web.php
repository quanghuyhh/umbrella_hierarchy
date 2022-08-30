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
    return $router->app->version();
});

$router->get('/swagger', function () {
    return view('swagger.index');
});

$router->group(['prefix' => 'employee'], function () use ($router) {
    $router->get('/', 'EmployeeController@index');
    $router->get('/search', 'EmployeeController@search');
    $router->post('/import', 'EmployeeController@import');
});
