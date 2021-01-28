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


$router->get('/deposit/{seller_id}', 'DepositController@depositBalance');

$router->get('/deposit/log/{seller_id}', 'DepositController@depositLog');

$router->post('/deposit/register', 'DepositController@depositRegister');

$router->post('/seller/register', 'SellerController@sellerRegister');

$router->post('/disburse/request', 'DisburseController@disburseSubmit');

$router->get('/disburse/status/{transaction_id}', 'DisburseController@disburseStatus');

$router->get('/disburse/log/{transaction_id}', 'DisburseController@disburseLog');

?>