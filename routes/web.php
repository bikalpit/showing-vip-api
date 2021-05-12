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

$router->group(['prefix' => 'api'], function () use ($router) {
		/* User Auth APIs Start */
		$router->post('user-signup',  ['uses'=>'UserAuthController@userSignUp']);
		$router->post('user-login',  ['uses'=>'UserAuthController@userLogin']);
		$router->post('create-password',  ['uses'=>'UserAuthController@createPassword']);
		$router->post('reset-password',  ['uses'=>'UserAuthController@resetPassword']);
		$router->post('forget-password',  ['uses'=>'UserAuthController@forgetPassword']);
		$router->post('reset-forget-password',  ['uses'=>'UserAuthController@resetForgetPassword']);
		/* User Auth APIs End */
});