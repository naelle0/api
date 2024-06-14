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
$router->get('/stuffs', 'StuffController@index');

$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/profile', 'AuthController@me');
// $router->get('/stuffs', 'StuffController@index');

$router->group(['middleware' => 'cors'], function ($router) {

$router->group(['prefix' => 'stuff/'],function() use ($router){
   $router->get('/data', 'StuffController@index');
   $router->post('/store', 'StuffController@store');
   $router->get('/trash', 'StuffController@trash' );
   
   $router->get('{id}', 'StuffController@show' );
   $router->patch('{id}', 'StuffController@update' );
   $router->delete('{id}', 'StuffController@destroy' );
   $router->get('/restore/{id}', 'StuffController@restore' );
   $router->delete('/permanent/{id}', 'StuffController@deletePermanent' );


});

$router->group(['prefix' => 'stuff-stock/' , 'middleware' => 'auth'],function() use ($router){
    $router->get('/', 'StuffStockController@index');
    $router->post('store', 'StuffStockController@store');
    $router->get('detail/{id}', 'StuffStockController@show');
    $router->patch('update/{id}', 'StuffStockController@update');
    $router->post('add-stock/{id}', 'StuffStockController@addStock');
  
});


$router->group(['prefix' => 'inbound-stuff/'],function() use ($router){
    $router->post('/store', 'InboundStuffController@store');
    $router->get('/', 'InboundStuffController@index');
    $router->get('/detail/{id}', 'InboundStuffController@show');
    $router->get('recycle-bin', 'InboundStuffController@recycleBin' );
    $router->patch('update/{id}', 'InboundStuffController@update' );
    $router->delete('delete/{id}', 'InboundStuffController@destroy' );
    $router->get('restore/{id}', 'InboundStuffController@restore' );
    $router->get('force-delete{id}','InboundStuffController@forceDestroy');
    $router->get('trash', 'InboundStuffController@trash');
 
 
 });


$router->get('/users', 'UseController@index');

$router->group(['prefix' => 'User'],function() use ($router){
    $router->get('/', 'UseController@index');
    $router->post('/store', 'UseController@store');
    $router->get('detail/{id}', 'UseController@trash' );
    $router->get('update/{id}', 'UseController@trash' );
   
    $router->get('{id}', 'StuffController@show' );
    $router->patch('{id}', 'StuffController@update' );
    $router->delete('{id}', 'StuffController@destroy' );
    $router->get('/restore/{id}', 'StuffController@restore' );
    $router->delete('/permanent/{id}', 'StuffController@deletePermanent' );
    $router->post('/login', 'UseController@login');
    //$router->get('/logout', 'UseController@logout');
});


$router->group(['prefix' => 'Lending/' , 'middleware' => 'auth'],function() use ($router){
    $router->post('/store', 'lendingController@store');
    $router->get('/', 'lendingController@index');
    $router->get('/detail/{id}', 'lendingController@show');
    $router->get('recycle-bin', 'lendingController@recycleBin' );
    $router->patch('update/{id}', 'lendingController@update' );
    $router->delete('delete/{id}', 'lendingController@destroy' );
    $router->get('restore/{id}', 'lendingController@restore' );
    $router->get('force-delete{id}','lendingController@forceDestroy');
    $router->get('trash', 'lendingController@trash');
});
}); 