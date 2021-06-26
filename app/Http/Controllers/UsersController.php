<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\UserAgents;
use App\Models\AgentInfo;
use App\Mail\SignupMail;
use Carbon\Carbon;

class UsersController extends Controller
{
    public function sellerSignUp(Request $request){
	    	$this->validate($request, [
	      		'first_name' => 'required',
	          'last_name' => 'required',
	          'phone' => 'required',
	      		'email' => 'required|email',
	      		'url' => 'nullable'
	      ]);

	    	if (Users::where('email', $request->email)->exists()) {
	        	return $this->sendResponse("Email already exists!", 200, false);
	      }

	      if (Users::where('phone', $request->phone)->exists()) {
	        	return $this->sendResponse("Phone no. already exists!", 200, false);
	      }

	      $time = strtotime(Carbon::now());
        $uuid = "usr".$time.rand(10,99)*rand(10,99);
	      $user = new Users;
        $user->uuid = $uuid;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->role = "USER";
        $user->sub_role = "SELLER";
        $user->phone_verified = "NO";
        $user->email_verified = "NO";
        $user->image = "default.png";
        $result = $user->save();

        if ($result) {
						$this->configSMTP();
						$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
      			Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

			      $data = [
			      		'name'=>$request->first_name.' '.$request->last_name, 
                'verification_token'=>$verification_token, 
                'email'=>$request->email,
                'url'=>$request->url
			      ];
			      
			      try{
			          Mail::to($request->email)->send(new SignupMail($data));  
			      }catch(\Exception $e){
			          $msg = $e->getMessage();
			          return $this->sendResponse($msg, 200, false);
			      }

					  return $this->sendResponse("Signup successfull!");
				}else{
						return $this->sendResponse("Sorry, Something went wrong!", 200, false);
				}
    }

    public function buyerSignUp(Request $request){
	    	$this->validate($request, [
	      		'first_name' => 'required',
	          'last_name' => 'required',
	          'phone' => 'required',
	      		'email' => 'required|email',
	      		'url' => 'nullable'
	      ]);

	    	if (Users::where('email', $request->email)->exists()) {
	        	return $this->sendResponse("Email already exists!", 200, false);
	      }

	      if (Users::where('phone', $request->phone)->exists()) {
	        	return $this->sendResponse("Phone no. already exists!", 200, false);
	      }

	      $time = strtotime(Carbon::now());
        $uuid = "usr".$time.rand(10,99)*rand(10,99);
	      $user = new Users;
        $user->uuid = $uuid;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->role = "USER";
        $user->sub_role = "BUYER";
        $user->phone_verified = "NO";
        $user->email_verified = "NO";
        $user->image = "default.png";
        $result = $user->save();

        if ($result) {
						$this->configSMTP();
						$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
      			Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

			      $data = [
			      		'name'=>$request->first_name.' '.$request->last_name, 
                'verification_token'=>$verification_token, 
                'email'=>$request->email,
                'url'=>$request->url
            ];

			      try{
			          Mail::to($request->email)->send(new SignupMail($data));  
			      }catch(\Exception $e){
			          $msg = $e->getMessage();
			          return $this->sendResponse($msg, 200, false);
			      }

					  return $this->sendResponse("Signup successfull!");
				}else{
						return $this->sendResponse("Sorry, Something went wrong!", 200, false);
				}
    }

    public function agentSignUp(Request $request){
	    	$this->validate($request, [
	      		'first_name' => 'required',
	          'last_name' => 'required',
	          'phone' => 'required',
	      		'email' => 'required|email',
	      		'url' => 'nullable',
	          'mls_id' => 'required',
	          'mls_name' => 'required',
	          'agent_info' => 'required'
	      ]);
	      
	    	if (Users::where('email', $request->email)->exists()) {
	        	return $this->sendResponse("Email already exists!", 200, false);
	      }

	      if (Users::where('phone', $request->phone)->exists()) {
	        	return $this->sendResponse("Phone no. already exists!", 200, false);
	      }

	      $time = strtotime(Carbon::now());
        $uuid = "usr".$time.rand(10,99)*rand(10,99);
	      $user = new Users;
        $user->uuid = $uuid;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->role = "AGENT";
        $user->mls_id = $request->mls_id;
        $user->mls_name = $request->mls_name;
        $user->phone_verified = "NO";
        $user->email_verified = "NO";
        if ($request->agent_info['hmdo_agent_photo_url'][1] == null || $request->agent_info['hmdo_agent_photo_url'][1] == '') {
        	$user->image = "default.png";
        }else{
        	$user->image = $request->agent_info['hmdo_agent_photo_url'][1];
        }
        $user->about = $request->agent_info['hmdo_agent_skills'][1];
        $user->website_url = $request->agent_info['hmdo_office_website'][1];
        $result = $user->save();

        if ($result) {

        		$agent_info = new AgentInfo;
        		$agent_info->agent_id = $uuid;
        		$agent_info->hmdo_lastupdated = $request->agent_info['hmdo_lastupdated'][1];
        		$agent_info->hmdo_mls_originator = $request->agent_info['hmdo_mls_originator'][1];
        		$agent_info->hmdo_agent_name = $request->agent_info['hmdo_agent_name'][1];
        		$agent_info->hmdo_agent_title = $request->agent_info['hmdo_agent_title'][1];
        		$agent_info->hmdo_agent_photo_url = $request->agent_info['hmdo_agent_photo_url'][1];
        		$agent_info->hmdo_agent_email = $request->agent_info['hmdo_agent_email'][1];
        		$agent_info->hmdo_office_main_phone = $request->agent_info['hmdo_office_main_phone'][1];
        		$agent_info->hmdo_office_direct_phone = $request->agent_info['hmdo_office_direct_phone'][1];
        		$agent_info->hmdo_office_mobile_phone = $request->agent_info['hmdo_office_mobile_phone'][1];
        		$agent_info->hmdo_agent_skills = $request->agent_info['hmdo_agent_skills'][1];
        		$agent_info->hmdo_office_id = $request->agent_info['hmdo_office_id'][1];
        		$agent_info->hmdo_office_name = $request->agent_info['hmdo_office_name'][1];
        		$agent_info->hmdo_office_photo = $request->agent_info['hmdo_office_photo'][1];
        		$agent_info->hmdo_office_street = $request->agent_info['hmdo_office_street'][1];
        		$agent_info->hmdo_office_city = $request->agent_info['hmdo_office_city'][1];
        		$agent_info->hmdo_office_zipcode = $request->agent_info['hmdo_office_zipcode'][1];
        		$agent_info->hmdo_office_state = $request->agent_info['hmdo_office_state'][1];
        		$agent_info->hmdo_office_phone = $request->agent_info['hmdo_office_phone'][1];
        		$agent_info->hmdo_office_website = $request->agent_info['hmdo_office_website'][1];
        		$agent_info->save();

						$this->configSMTP();
						$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
      			Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

			      $data = [
			      		'name'=>$request->first_name.' '.$request->last_name, 
                'verification_token'=>$verification_token, 
                'email'=>$request->email,
                'url'=>$request->url
            ];

			      try{
			          Mail::to($request->email)->send(new SignupMail($data));  
			      }catch(\Exception $e){
			          $msg = $e->getMessage();
			          return $this->sendResponse($msg, 200, false);
			      }

					  return $this->sendResponse("Signup successfull!");
				}else{
						return $this->sendResponse("Sorry, Something went wrong!", 200, false);
				}
    }

    public function getSingleUser(Request $request){
    		$this->validate($request, [
	      		'user_id' => 'required'
	      ]);

	      $user = Users::where('uuid', $request->user_id)->first();

	      if ($user) {
    				return $this->sendResponse($user);
    		}else{
    				return $this->sendResponse("Sorry, User not found!", 200, false);
    		}
    }

    public function addAgent(Request $request){
    		$this->validate($request, [
	      		'user_id' => 'required',
	          'agent_id' => 'required'
	      ]);

    		$user_agent = new UserAgents;
    		$user_agent->user_id = $request->user_id;
    		$user_agent->agent_id = $request->agent_id;
    		$result = $user_agent->save();

    		if ($result) {
    				return $this->sendResponse("Agent add successfully!");
    		}else{
    				return $this->sendResponse("Sorry, Something went wrong!", 200, false);
    		}
    }

    public function getUsers(Request $request){
    		$this->validate($request, [
	      		'filter' => 'required|in:ALL,SELLER,BUYER,AGENT'
	      ]);

	      if ($request->filter == 'ALL') {
	      		$users = Users::get();
	      }elseif ($request->filter == 'SELLER') {
	     			$users = Users::where('role', 'SELLER')->get();
	      }elseif ($request->filter == 'BUYER') {
	    			$users = Users::where('role', 'BUYER')->get();
	      }elseif ($request->filter == 'AGENT') {
	    			$users = Users::where('role', 'AGENT')->get();
	      }

	      if (sizeof($users) > 0) {
	  				return $this->sendResponse($users);
	      }else{
	      		return $this->sendResponse("Sorry, Users not found!", 200, false);
	      }
    }

    public function getAgents(Request $request){
    		$this->validate($request, [
	      		'user_id' => 'required'
	      ]);

	      $agents = UserAgents::with('agentProfile.agentInfo')->where('user_id', $request->user_id)->get();

	      if (sizeof($agents) > 0) {
	  				return $this->sendResponse($agents);
	      }else{
	      		return $this->sendResponse("Sorry, Agents not found!", 200, false);
	      }
    }

    public function updateProfile(Request $request){
    		$this->validate($request, [
	      		'user_id' => 'required',
	      		'first_name' => 'required',
	      		'last_name' => 'required',
	      		'phone' => 'required',
	      		'email' => 'required',
	      		'address' => 'nullable',
	      		'city' => 'nullable',
	      		'zipcode' => 'nullable',
	      		'state' => 'nullable',
	      		'country' => 'nullable',
	      		'about' => 'required',
	      		'website_url' => 'nullable',
	      		'image' => 'nullable'
	      ]);

    		$user = Users::where('uuid', $request->user_id)->first();

    		if ($request->email !== $user->email) {
    				$emailCheck = Users::where('email', $request->email)->first();
    				if (!empty($emailCheck)) {
    						return $this->sendResponse("Email already exist!", 200, false);
    				}else{
    						Users::where('uuid', $request->user_id)->update(['email_verified'=>'NO']);
    				}
    		}

    		if ($request->phone !== $user->phone) {
    				$phoneCheck = Users::where('phone', $request->phone)->first();
    				if (!empty($phoneCheck)) {
    						return $this->sendResponse("Phone already exist!", 200, false);
    				}else{
    						Users::where('uuid', $request->user_id)->update(['phone_verified'=>'NO']);
    				}
    		}

	      $update = Users::where('uuid', $request->user_id)->update([
	      		'first_name'=>$request->first_name,
	      		'last_name'=>$request->last_name,
	      		'phone'=>$request->phone,
	      		'email'=>$request->email,
	      		'address'=>$request->address,
	      		'city'=>$request->city,
	      		'zipcode'=>$request->zipcode,
	      		'state'=>$request->state,
	      		'country'=>$request->country,
	      		'about'=>$request->about,
	      		'website_url'=>$request->website_url
	      ]);

	      if($request->has('image')){
            if($request->image != '')
            {
                $path = app()->basePath('public/user-images/');
                $fileName = $this->singleImageUpload($path, $request->image);
                Users::where('uuid', $request->user_id)->update(['image'=>$fileName]);
            }
        }

        if ($update) {
        		return $this->sendResponse("Profile updated successfully!");
        }else{
        		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
        }
    }
}