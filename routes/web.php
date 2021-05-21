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
		$router->post('user-logout',  ['middleware'=>'auth', 'uses'=>'UserAuthController@userLogout']);
		/* UserAuthController APIs End */

		/* UsersController APIs Start */
		$router->post('seller-signup',  ['uses'=>'UsersController@sellerSignUp']);
		$router->post('buyer-signup',  ['uses'=>'UsersController@buyerSignUp']);
		$router->post('agent-signup',  ['uses'=>'UsersController@agentSignUp']);
		$router->post('get-single-user',  ['middleware'=>'auth','uses'=>'UsersController@getSingleUser']);
		$router->post('add-agent',  ['middleware'=>'auth','uses'=>'UsersController@addAgent']);
		$router->post('get-users',  ['middleware'=>'auth','uses'=>'UsersController@getUsers']);
		$router->post('get-agents',  ['middleware'=>'auth','uses'=>'UsersController@getAgents']);
		$router->post('update-profile',  ['uses'=>'UsersController@updateProfile']);
		/* UsersController APIs End */

		/* PropertiesController APIs Start */
		$router->post('add-property',  ['uses'=>'PropertiesController@addProperty']);
		$router->post('user-properties',  ['uses'=>'PropertiesController@userProperties']);
		$router->post('assign-agent',  ['uses'=>'PropertiesController@assignAgent']);
		$router->post('remove-agent',  ['uses'=>'PropertiesController@removeAgent']);
		$router->post('verify-property',  ['uses'=>'PropertiesController@verifyProperty']);
		$router->post('get-verified-properties',  ['uses'=>'PropertiesController@verifiedProperties']);
		$router->post('add-owner',  ['uses'=>'PropertiesController@addOwner']);
		$router->post('agent-properties',  ['uses'=>'PropertiesController@agentProperties']);
		$router->post('add-property',  ['middleware'=>'auth','uses'=>'PropertiesController@addProperty']);
		$router->post('user-properties',  ['middleware'=>'auth','uses'=>'PropertiesController@userProperties']);
		$router->post('assign-agent',  ['middleware'=>'auth','uses'=>'PropertiesController@assignAgent']);
		$router->post('remove-agent',  ['middleware'=>'auth','uses'=>'PropertiesController@removeAgent']);
		$router->post('verify-property',  ['middleware'=>'auth','uses'=>'PropertiesController@verifyProperty']);
		$router->post('get-verified-properties',  ['middleware'=>'auth','uses'=>'PropertiesController@verifiedProperties']);
		/* PropertiesController APIs End */

		/* ShowingController APIs Start */
		$router->post('create-slots',  ['uses'=>'ShowingController@createSlots']);
		$router->post('create-survey-category',  ['uses'=>'ShowingController@createSurveyCategory']);
		$router->post('update-survey-category',  ['uses'=>'ShowingController@updateSurveyCategory']);
		$router->post('get-all-categories',  ['uses'=>'ShowingController@getAllCategories']);
		$router->post('get-single-category',  ['uses'=>'ShowingController@getSingleCategory']);
		$router->post('delete-category',  ['uses'=>'ShowingController@deleteCategory']);
		$router->post('create-survey-sub-category',  ['uses'=>'ShowingController@createSurveySubCategory']);
		$router->post('update-survey-sub-category',  ['uses'=>'ShowingController@updateSurveySubCategory']);
		$router->post('delete-sub-category',  ['uses'=>'ShowingController@deleteSubCategory']);
		$router->post('create-showing-setup',  ['uses'=>'ShowingController@createShowingSetup']);
		$router->post('update-showing-setup',  ['uses'=>'ShowingController@updateShowingSetup']);
		$router->post('get-single-showing-setup',  ['uses'=>'ShowingController@getSingleShowingSetup']);
		$router->post('create-survey-category',  ['middleware'=>'auth','uses'=>'ShowingController@createSurveyCategory']);
		$router->post('update-survey-category',  ['middleware'=>'auth','uses'=>'ShowingController@updateSurveyCategory']);
		$router->post('get-all-categories',  ['middleware'=>'auth','uses'=>'ShowingController@getAllCategories']);
		$router->post('get-single-category',  ['middleware'=>'auth','uses'=>'ShowingController@getSingleCategory']);
		$router->post('delete-category',  ['middleware'=>'auth','uses'=>'ShowingController@deleteCategory']);
		$router->post('create-survey-sub-category',  ['middleware'=>'auth','uses'=>'ShowingController@createSurveySubCategory']);
		$router->post('update-survey-sub-category',  ['middleware'=>'auth','uses'=>'ShowingController@updateSurveySubCategory']);
		$router->post('delete-sub-category',  ['middleware'=>'auth','uses'=>'ShowingController@deleteSubCategory']);
		$router->post('create-showing-setup',  ['middleware'=>'auth','uses'=>'ShowingController@createShowingSetup']);
		$router->post('get-single-showing-setup',  ['middleware'=>'auth','uses'=>'ShowingController@getSingleShowingSetup']);
		/* ShowingController APIs End */

		/* BookingScheduleController API Start*/
		$router->post('create-schedule-booking',  ['middleware'=>'auth','uses'=>'BookingScheduleController@createBooking']);
		$router->post('update-schedule-booking',  ['middleware'=>'auth','uses'=>'BookingScheduleController@updateBooking']);
		/* BookingScheduleController API End*/

		/*AgentController APIs Start*/
		$router->post('get-clients',  ['middleware'=>'auth','uses'=>'AgentController@getClientWithProperty']);
		$router->post('get-single-client',  ['middleware'=>'auth','uses'=>'AgentController@getSingleClient']);
		/*AgentController APIs End*/
});