<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Users;
use App\Models\ApiToken;
use App\Models\UserPasswordReset;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Mail\SignupMail;
use App\Mail\ForgetPasswordMail;

class UserAuthController extends Controller
{
    public function userSignUp(Request $request){
	    	$this->validate($request, [
	      		'first_name' => 'required',
	          'last_name' => 'required',
	          'phone' => 'required',
	      		'email' => 'required|email',
	          'role' => 'required|in:SELLER,BUYER,AGENT'
	      ]);

	    	if (Users::where('email', $request->email)->exists()) {
	        	return $this->sendResponse("Email already exists!",200,false);
	      }

	      $time = strtotime(Carbon::now());
        $uuid = "usr".$time.rand(10,99)*rand(10,99);
	      $user = new Users;
        $user->uuid = $uuid;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->phone_verified = "NO";
        $user->email_verified = "NO";
        $user->image = "default.png";
        $result = $user->save();

        if ($result) {
						$this->configSMTP();
						$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
      			Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);
			      $data = ['name'=>$request->first_name.' '.$request->last_name, 
				                'verification_token'=>$verification_token, 
				                'email'=>$request->email,
				                'app_url'=>env('APP_URL')
			              ];
			      try{
			          Mail::to($request->email)->send(new SignupMail($data));  
			      }catch(\Exception $e){
			          $msg = $e->getMessage();
			          return $this->sendResponse($msg,200,false);
			      }

					  return $this->sendResponse("Signup successfull!");
				}
    }

    public function userLogin(Request $request){
    		$this->validate($request, [
	      		'email' => 'required|email',
	          'password' => 'required'
	      ]);

    		$user = Users::where(['email' => $request->email])->first();
    		if (isset($user)) {
	    			if (Hash::check($request->password, $user->password)) {
	    					$token_string = hash("sha256", rand());
				        $where = ['user_id'=>$user->uuid,'user_type'=>$user->role];
				        $authentication = ApiToken::where($where)->first();
				        if (empty($authentication)) {
				            $authentication = ApiToken::updateOrCreate(['user_id' => $user->uuid],[
				              'user_id' => $user->uuid,
				              'token' => $token_string,
				              'user_type' => $user->role,
				            ]);  
				        }
				        $authentication['first_name'] = $user->first_name;
	          		$authentication['last_name'] = $user->last_name;
	          		$authentication['email']   = $user->email;
	          		$authentication['phone'] = $user->phone;
	      				$authentication['image'] = $user->image;
					      return $this->sendResponse($authentication);
	    			}else{
	    				  return $this->sendResponse("Email or Password is wrong!", 200, false);
	    			}
    		}else{
    				return $this->sendResponse("Email or Password is wrong!", 200, false);
    		}
    }

    public function createPassword(Request $request){
    		$this->validate($request, [
	          'password' => 'required',
	          'token' => 'required'
	      ]);

    		$user = Users::where(['email_verification_token' => $request->token])->first();

    		if (!empty($user)) {
    				$password = Hash::make($request->password);
    				$updatePassword = Users::where(['email_verification_token' => $request->token])->update(['password'=>$password, 'email_verified'=>'YES']);
    				if ($updatePassword) {
    						return $this->sendResponse("Password created successfully!");
    				}else{
    						return $this->sendResponse("Sorry, Something went wrong!", 200, false);
    				}
    		}else{
    				return $this->sendResponse("Sorry, Invalid token!", 200, false);
    		}
    }

    public function resetPassword(Request $request){
    		$this->validate($request, [
    				'user_id' => 'required',
	          'old_password' => 'required',
	          'new_password' => 'required'
	      ]);

	      $user = Users::where(['uuid' => $request->user_id])->first();
	      
	      if (!empty($user)) {
		      	if (Hash::check($request->old_password, $user->password)) {
			   				$new_password = Hash::make($request->new_password);
			   				$resetPassword = Users::where(['uuid' => $request->user_id])->update(['password'=>$new_password]);
			   				if ($resetPassword) {
					   				return $this->sendResponse("Password reset successfully!");
					   		}else{
					   				return $this->sendResponse("Sorry, Something went wrong!", 200, false);
					   		}
			      }else{
			      		return $this->sendResponse("Old password is wrong!", 200, false);
			      }
			  }else{
			  		return $this->sendResponse("Sorry, User not found!", 200, false);
			  }
    }

    public function forgetPassword(Request $request){
    		$this->validate($request, [
    				'email' => 'required'
	      ]);

    		$user = Users::where(['email' => $request->email])->first();

	      if ($user) {
						$this->configSMTP();
						$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
      			$authentication = UserPasswordReset::updateOrCreate(['email' => $request->email],[
									              'token' => $verification_token
								            ]);
			      $data = ['name'=>$user->first_name.' '.$user->last_name,
			      						'verification_token'=>$verification_token,
			      						'email'=>$request->email,
			      						'app_url'=>env('APP_URL')
			      				];

			      try{
			          Mail::to($request->email)->send(new ForgetPasswordMail($data));  
			      }catch(\Exception $e){
			          $msg = $e->getMessage();
			          return $this->sendResponse($msg,200,false);
			      }

					  return $this->sendResponse("Email send successfully for reset password!");
				}
    }

    public function resetForgetPassword(Request $request){
    		$this->validate($request, [
    				'token' => 'required',
    				'new_password' => 'required'
	      ]);

	      $token = UserPasswordReset::where('token', $request->token)->first();

	      if (!empty($token)) {
	      		$new_password = Hash::make($request->new_password);
			   		$resetPassword = Users::where(['email' => $token->email])->update(['password'=>$new_password]);
			   		if ($resetPassword) {
			   				return $this->sendResponse("Password reset successfully!");
			   		}else{
			   				return $this->sendResponse("Sorry, Something went wrong!", 200, false);
			   		}
	      }else{
	      		return $this->sendResponse("Sorry, Invalid token!", 200, false);
	      }
    }
}