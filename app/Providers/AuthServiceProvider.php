<?php

namespace App\Providers;

use App\Models\ApiToken;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        /*$this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return Users::where('api_token', $request->input('api_token'))->first();
            }
        });*/

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('api-token') && $request->header('agent-id') || $request->header('user-id') || $request->header('super-admin-id')){
                if(!empty($request->header('agent-id'))){
                  $user_id = $request->header('agent-id');
                  $user_type = "AGENT";
                }elseif(!empty($request->header('user-id'))){
                  $user_id = $request->header('user-id');
                  $user_type = "USER";
                }elseif(!empty($request->header('super-admin-id'))){
                  $user_id = $request->header('super-admin-id');
                  $user_type = "SA";
                }
                $where_array = array(
                  "token" =>$request->header('api-token'),
                  "user_id" =>$user_id,
                  "user_type" =>$user_type,
                );
                $user = ApiToken::where($where_array)->first();
                return $user;
            }
        });
    }
}
