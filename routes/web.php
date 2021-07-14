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
		$router->post('send-verify-email',  ['uses'=>'UserAuthController@sendVerifyEmail']);
		$router->post('verify-email',  ['uses'=>'UserAuthController@verifyEmail']);
		/* UserAuthController APIs End */

		/* UsersController APIs Start */
		$router->post('seller-signup',  ['uses'=>'UsersController@sellerSignUp']);
		$router->post('buyer-signup',  ['uses'=>'UsersController@buyerSignUp']);
		$router->post('agent-signup',  ['uses'=>'UsersController@agentSignUp']);
		$router->post('get-single-user',  ['middleware'=>'auth','uses'=>'UsersController@getSingleUser']);
		$router->post('add-agent',  ['middleware'=>'auth','uses'=>'UsersController@addAgent']);
		$router->post('get-users',  ['middleware'=>'auth','uses'=>'UsersController@getUsers']);
		$router->post('get-agents',  ['middleware'=>'auth','uses'=>'UsersController@getAgents']);
		$router->post('get-messages',  ['middleware'=>'auth','uses'=>'UsersController@getMessages']);
		$router->post('get-senders',  ['middleware'=>'auth','uses'=>'UsersController@getSenders']);
		$router->post('update-profile',  ['uses'=>'UsersController@updateProfile']);
		/* UsersController APIs End */

		/* PropertiesController APIs Start */
		$router->post('update-property',  ['uses'=>'PropertiesController@updateProperty']);
		$router->post('get-property',  ['uses'=>'PropertiesController@getProperty']);
		$router->post('add-owner',  ['uses'=>'PropertiesController@addOwner']);
		$router->post('agent-properties',  ['uses'=>'PropertiesController@agentProperties']);
		$router->post('add-property',  ['middleware'=>'auth','uses'=>'PropertiesController@addProperty']);
		$router->post('user-properties',  ['middleware'=>'auth','uses'=>'PropertiesController@userProperties']);
		$router->post('assign-agent',  ['middleware'=>'auth','uses'=>'PropertiesController@assignAgent']);
		$router->post('remove-agent',  ['middleware'=>'auth','uses'=>'PropertiesController@removeAgent']);
		$router->post('verify-property',  ['middleware'=>'auth','uses'=>'PropertiesController@verifyProperty']);
		$router->post('verify-property-owner',  ['middleware'=>'auth','uses'=>'PropertiesController@verifyPropertyOwner']);
		$router->post('get-verified-properties',  ['middleware'=>'auth','uses'=>'PropertiesController@verifiedProperties']);
		$router->get('verified-property',  ['uses'=>'PropertiesController@verifiedProperty']);
		$router->get('verified-property-owner',  ['uses'=>'PropertiesController@verifiedPropertyOwner']);
		/* PropertiesController APIs End */

		/* ShowingController APIs Start */
		$router->post('create-slots',  ['uses'=>'ShowingController@createSlots']);
		$router->post('update-showing-setup',  ['middleware'=>'auth','uses'=>'ShowingController@updateShowingSetup']);
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
		$router->post('create-showing-availability',  ['middleware'=>'auth','uses'=>'ShowingController@createShowingAvailability']);
		$router->post('create-showing-survey',  ['middleware'=>'auth','uses'=>'ShowingController@createShowingSurvey']);
		$router->post('update-showing-availability',  ['middleware'=>'auth','uses'=>'ShowingController@updateShowingAvailability']);
		$router->post('update-showing-survey',  ['middleware'=>'auth','uses'=>'ShowingController@updateShowingSurvey']);
		$router->post('get-single-showing-setup-non-auth',  ['uses'=>'ShowingController@getSingleShowingSetupNonAuth']);
		/* ShowingController APIs End */

		/* BookingScheduleController API Start*/
		$router->post('create-schedule-booking',  ['uses'=>'BookingScheduleController@createBooking']);
		$router->post('update-schedule-booking',  ['middleware'=>'auth','uses'=>'BookingScheduleController@updateBooking']);
		$router->post('get-showing-bookings',  ['middleware'=>'auth','uses'=>'BookingScheduleController@getShowingBookings']);
		$router->post('all-showing-bookings',  ['middleware'=>'auth','uses'=>'BookingScheduleController@allShowingBookings']);
		$router->post('submit-feedback',  ['middleware'=>'auth','uses'=>'BookingScheduleController@submitFeedback']);
		$router->post('get-feedback',  ['middleware'=>'auth','uses'=>'BookingScheduleController@getFeedback']);
		$router->get('update-showing-status',  ['uses'=>'BookingScheduleController@updateShowingStatus']);
		/* BookingScheduleController API End*/

		/*AgentController APIs Start*/
		$router->post('get-clients',  ['middleware'=>'auth','uses'=>'AgentController@getClientWithProperty']);
		$router->post('get-single-client',  ['middleware'=>'auth','uses'=>'AgentController@getSingleClient']);
		$router->post('get-random-agents', ['middleware'=>'auth','uses'=>'AgentController@GetRandomAgents']);
		$router->post('get-user-agents', ['middleware'=>'auth','uses'=>'AgentController@getUserAgents']);
		$router->post('add-agent-properties', ['middleware'=>'auth','uses'=>'AgentController@addAgentProperties']);
		$router->post('add-client', ['middleware'=>'auth','uses'=>'AgentController@addClient']);
		$router->post('get-client-properties', ['middleware'=>'auth','uses'=>'AgentController@getClientProperties']);
		$router->post('add-agent-property', ['middleware'=>'auth','uses'=>'AgentController@addAgentProperty']);
		$router->post('test-mail', ['uses'=>'AgentController@testMail']);
		$router->post('all-agent-users', ['uses'=>'AgentController@allAgentUsers']);
		/*AgentController APIs End*/

		/*SuperAdminController APIs Start*/
		$router->post('all-agents',  ['uses'=>'SuperAdminController@allAgents']);
		$router->post('all-users',  ['uses'=>'SuperAdminController@allUsers']);
		$router->post('all-properties',  ['uses'=>'SuperAdminController@allProperties']);
		$router->post('all-showings',  ['uses'=>'SuperAdminController@allShowings']);
		$router->post('all-surveys',  ['uses'=>'SuperAdminController@allSurveys']);
		/*SuperAdminController APIs End*/

		/*SettingsController APIs Start*/
		$router->post('set-setting',  ['middleware'=>'auth','uses'=>'SettingsController@createSetting']);
		$router->post('get-single-setting',  ['uses'=>'SettingsController@getSingleSetting']);
		$router->post('get-all-setting',  ['uses'=>'SettingsController@getAllSetting']);
		$router->post('update-setting',  ['middleware'=>'auth','uses'=>'SettingsController@updateSetting']);
		/*SettingsController APIs End*/

		/*LockboxTypeController APIs Start*/
		$router->post('create-lockbox-type',  ['uses'=>'LockboxTypeController@createLockboxType']);
		$router->post('update-lockbox-type',  ['uses'=>'LockboxTypeController@updateLockboxType']);
		$router->post('get-all-lockbox-type',  ['uses'=>'LockboxTypeController@getAllLockboxType']);
		$router->post('get-single-lockbox-type',  ['uses'=>'LockboxTypeController@getSingleLockboxType']);
		$router->post('delete-lockbox-type',  ['uses'=>'LockboxTypeController@deleteLockboxType']);
		/*LockboxTypeController APIs End*/
});