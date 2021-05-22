<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\ApiToken;
use App\Models\UserPasswordReset;
use App\Mail\ForgetPasswordMail;
use App\Mail\VerifyEmail;
use Twilio\Rest\Client as TwilioClient;
use Auth;

class UserAuthController extends Controller
{
    public function userLogin(Request $request){
    		$this->validate($request, [
	      		'email' => 'required|email',
	          'password' => 'required'
	      ]);

    		$user = Users::where(['email' => $request->email])->first();
    		if (!empty($user)) {
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
	      				$authentication['sub_role'] = $user->sub_role;
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

	      if (!empty($user)) {
						$this->configSMTP();
						$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
      			$authentication = UserPasswordReset::updateOrCreate(['email' => $request->email],[
									              'token' => $verification_token
								            ]);
			      $data = [
			      		'name'=>$user->first_name.' '.$user->last_name,
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
			   				UserPasswordReset::where('token', $request->token)->delete();
			   				return $this->sendResponse("Password reset successfully!");
			   		}else{
			   				return $this->sendResponse("Sorry, Something went wrong!", 200, false);
			   		}
	      }else{
	      		return $this->sendResponse("Sorry, Invalid token!", 200, false);
	      }
    }

    public function verifyPhone(Request $request){
    		$this->validate($request, [
    				'phone' => 'required'
	      ]);

        $phone = $request->phone;
        $otp = rand(1111,9999);

        $user = Users::where('phone', $phone)->first();

        if (!empty($user)) {
        		try {
				        $this->twilioClient = new TwilioClient('AC77bf6fe8f1ff8ee95bad95276ffaa586', '94666fdb4b4f3090f7b26be77e67a819');
				        $message =  $this->twilioClient->messages->create(
						        $phone,
						        array(
						            "from" => '+14243918787',
						            "body" => 'your phone number OTP verification is '.$otp
						        )
				        );
				    } catch(\Exception $e) {
				    		Users::where('phone', $phone)->update(['phone_verification_token'=>md5($otp)]);
		          	return $this->sendResponse(['otp' => $otp]);
		        }
		        Users::where('phone', $phone)->update(['phone_verification_token'=>md5($otp)]);
		        return $this->sendResponse(['otp' => $otp]);
        }else{
        		return $this->sendResponse("Sorry, User not found!", 200, false);
        }
		        
    }

    public function verifyPhoneOtp(Request $request){
    		$this->validate($request, [
    				'phone' => 'required',
    				'otp' => 'required'
	      ]);

	      $phone = $request->phone;
        $otp = $request->otp;

        $user = Users::where('phone', $phone)->first();

        if (!empty($user)) {
        		$check = Users::where(['phone' => $phone, 'phone_verification_token' => md5($otp)])->first();
        		if (!empty($check)) {
        				$updateStatus = Users::where(['phone' => $phone, 'phone_verification_token' => md5($otp)])->update(['phone_verified' => "YES"]);
        				if ($updateStatus) {
        						return $this->sendResponse("Phone no. verified successfully!");
        				}else{
        						return $this->sendResponse("Sorry, Something went wrong!", 200, false);
        				}
        		}else{
        				return $this->sendResponse("OTP is wrong!", 200, false);
        		}
        }else{
        		return $this->sendResponse("Sorry, User not found!", 200, false);
        }
    }

    public function userLogout(Request $request){
        $loginUser = Auth::user();
        
        if (!empty($loginUser)) {
        		$result = ApiToken::where('user_id', $loginUser->user_id)->delete();
		        if ($result) {
		            return $this->sendResponse("Logout successfully!");
		        }else{
		            return $this->sendResponse("Sorry, Something went wrong!.",200,false);
		        }
        }else{
        		return $this->sendResponse("Sorry, User not found!", 200, false);
        }
    }

    public function sendVerifyEmail(Request $request){
    		$this->validate($request, [
    				'email' => 'required',
    				'url' => 'required'
	      ]);

    		$user = Users::where('email', $request->email)->first();

        if (!empty($user)) {
        		$this->configSMTP();
        		$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
			      $data = [
			      		'name'=>$user->first_name.' '.$user->last_name, 
		            'verification_token'=>$verification_token,
		            'url'=>$request->url
		        ];

		        try{
			          Mail::to($request->email)->send(new VerifyEmail($data));
			          Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);
			          return $this->sendResponse("Verification email sent successfully!");
			      }catch(\Exception $e){
			          $msg = $e->getMessage();
			          return $this->sendResponse($msg, 200, false);
			      }
        }else{
        		return $this->sendResponse("Sorry, User not found!", 200, false);
        }
    }

    public function verifyEmail(Request $request){
    		$this->validate($request, [
    				'token' => 'required'
	      ]);

    		$token = Users::where('email_verification_token', $request->token)->first();

    		if (!empty($token)) {
    				Users::where('email_verification_token', $request->token)->update(['email_verified'=>'YES']);
    				return $this->sendResponse("Email verified successfully!");
    		}else{
        		return $this->sendResponse("Sorry, Invalid token!", 200, false);
        }
    }
}