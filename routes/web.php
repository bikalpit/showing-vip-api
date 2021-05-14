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
		/* UserAuthController APIs Start */
		$router->post('user-login',  ['uses'=>'UserAuthController@userLogin']);
		$router->post('create-password',  ['uses'=>'UserAuthController@createPassword']);
		$router->post('reset-password',  ['uses'=>'UserAuthController@resetPassword']);
		$router->post('forget-password',  ['uses'=>'UserAuthController@forgetPassword']);
		$router->post('reset-forget-password',  ['uses'=>'UserAuthController@resetForgetPassword']);
		$router->post('verify-phone',  ['uses'=>'UserAuthController@verifyPhone']);
		$router->post('verify-phone-otp',  ['uses'=>'UserAuthController@verifyPhoneOtp']);
		$router->post('user-logout',  ['uses'=>'UserAuthController@userLogout']);
		/* UserAuthController APIs End */

		/* UsersController APIs Start */
		$router->post('seller-signup',  ['uses'=>'UsersController@sellerSignUp']);
		$router->post('buyer-signup',  ['uses'=>'UsersController@buyerSignUp']);
		$router->post('agent-signup',  ['uses'=>'UsersController@agentSignUp']);
		$router->post('get-single-user',  ['uses'=>'UsersController@getSingleUser']);
		$router->post('add-agent',  ['uses'=>'UsersController@addAgent']);
		/* UsersController APIs End */

		/* PropertiesController APIs Start */
		$router->post('add-property',  ['uses'=>'PropertiesController@addProperty']);
		/* PropertiesController APIs End */
});