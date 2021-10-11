<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\UserAgents;
use App\Models\Messages;
use App\Models\AgentInfo;
use App\Models\ApiToken;
use App\Models\PropertyAgents;
use App\Models\PropertyOwners;
use App\Models\PropertyShowingSetup;
use App\Models\PropertyVerification;
use App\Mail\SignupMail;
use App\Mail\OwnerVerificationMail;
use Carbon\Carbon;
use DB;

class UsersController extends Controller
{
    public function sellerSignUp(Request $request){
	    	$this->validate($request, [
	      		'first_name' => 'required',
	          'last_name' => 'required',
	          'phone' => 'required',
	      		'email' => 'required|email',
	      		'ip_address' => 'required',
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
        $user->ip_address = $request->ip_address;
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
	      		'ip_address' => 'required',
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
        $user->ip_address = $request->ip_address;
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
	      		'ip_address' => 'required',
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
        $user->ip_address = $request->ip_address;
        if ($request->agent_info['hmdo_agent_photo_url'][1] == null || $request->agent_info['hmdo_agent_photo_url'][1] == '') {
        	$user->image = env('APP_URL').'public/user-images/default.png';
        }else{
        	$user->image = $request->agent_info['hmdo_agent_photo_url'][1];
        }
        $user->about = $request->agent_info['hmdo_agent_skills'][1];
        $user->website_url = $request->agent_info['hmdo_agent_website'][1];
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
        		$agent_info->hmdo_agent_mobile_phone = $request->agent_info['hmdo_agent_mobile_phone'][1];
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
        		$agent_info->hmdo_agent_website = $request->agent_info['hmdo_agent_website'][1];
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

		public function getSenders(Request $request){
				$this->validate($request, [
						'user_id' => 'required'
				]);

				$messages = Messages::with('sender.senderInfo')->where('receiver_id', $request->user_id)->orderBy('id', 'DESC')->get()->unique('receiver_id');

				if (sizeof($messages) > 0) {
						return $this->sendResponse($messages);
				} else {
						return $this->sendResponse("Sorry, Senders not found!", 200, false);
				}
		}

		public function getMessages(Request $request){
				$this->validate($request, [
						'user_id' => 'required'
				]);

				$sort = $request->order_by ?? 'ASC';

				$messages = Messages::where('sender_id', $request->user_id)->orWhere('receiver_id', $request->user_id)->orderBy('id', $sort)->get();

				if (sizeof($messages) > 0) {
						return $this->sendResponse($messages);
				}else{
						return $this->sendResponse("Sorry, Messages not found!", 200, false);
				}
		}

    public function updateProfile(Request $request){
    		$this->validate($request, [
	      		'user_id' => 'required',
	      		'first_name' => 'nullable',
	      		'last_name' => 'nullable',
	      		'phone' => 'nullable',
	      		'email' => 'nullable',
	      		'address' => 'nullable',
	      		'city' => 'nullable',
	      		'zipcode' => 'nullable',
	      		'state' => 'nullable',
	      		'country' => 'nullable',
	      		'about' => 'nullable',
	      		'website_url' => 'nullable',
	      		'image' => 'nullable'
	      ]);

    		if ($request->email == '' || $request->email == null) {
    				return $this->sendResponse("Email is required.", 200, false);
    		}

    		if ($request->phone == '' || $request->phone == null) {
    				return $this->sendResponse("Phone is required.", 200, false);
    		}

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

	      if ($request->has('image')) {
            if ($request->image != '') {
                $path = app()->basePath('public/user-images/');
								$fileName = $this->singleImageUpload($path, $request->image);
								if($user->role == 'AGENT'){
										$fileName = env('APP_URL').'public/user-images/'.$fileName;
								}
                Users::where('uuid', $request->user_id)->update(['image'=>$fileName]);
            }
        }

        if ($update) {
        		$updatedUser = Users::where('uuid', $request->user_id)->first();
        		$authentication = ApiToken::where('user_id', $request->user_id)->first();
        		$authentication['first_name'] = $updatedUser->first_name;
        		$authentication['last_name'] = $updatedUser->last_name;
        		$authentication['email']   = $updatedUser->email;
        		$authentication['phone'] = $updatedUser->phone;
    				$authentication['image'] = $updatedUser->image;
    				$authentication['sub_role'] = $updatedUser->sub_role;
    				
    				if ($updatedUser->role == 'AGENT') {
      					$agent_info = AgentInfo::where('agent_id', $updatedUser->uuid)->first();
      					$authentication['agent_info'] = $agent_info;
    				}
        		return $this->sendResponse($authentication);
        }else{
        		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
        }
    }

    public function getStates(Request $request){
    		$this->validate($request, [
						'country_id' => 'required'
				]);

    		$states = DB::table('states')->where('country_id', $request->country_id)->get();

    		return $this->sendResponse($states);
    }

    public function getCities(Request $request){
    		$this->validate($request, [
						'state_id' => 'required'
				]);

    		$states = DB::table('cities')->where('state_id', $request->state_id)->get();

    		return $this->sendResponse($states);
    }

    public function verifyOwner(Request $request){
				$this->validate($request, [
	      		'user_id' => 'required',
	      		'agent_id' => 'required',
	      		'property_id' => 'required',
	      ]);
				
	      $owner = Users::where('uuid', $request->user_id)->first();
	      $property = PropertyAgents::where(['property_id'=>$request->property_id, 'agent_type'=>'seller'])->first();
	      $agent = Users::where('uuid', $request->agent_id)->first();
	      
	      if (!empty($agent)) {
	      		$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );

	      		$update_token = PropertyOwners::where(['user_id'=>$request->user_id, 'property_id'=>$request->property_id])->update(['verification_token'=>$verification_token]);

	      		if ($update_token) {
	      				$this->configSMTP();	
								$data = [
										'owner_name'=>$owner->first_name.' '.$owner->last_name,
										'agent_name'=>$agent->first_name.' '.$agent->last_name,
		                'user_id'=>base64_encode($owner->uuid),
		                'property_id'=>base64_encode($request->property_id),
		                'token'=>base64_encode($verification_token)
		            ];

		            try{
					          Mail::to($agent->email)->send(new OwnerVerificationMail($data));
					      }catch(\Exception $e){
					          $msg = $e->getMessage();
					          return $this->sendResponse($msg, 200, false);
					      }

					      return $this->sendResponse("Email sent for verification to agent!");
	      		}
	      }else{
						return $this->sendResponse("Sorry, User not found!", 200, false);
				}
		}

		public function verifiedOwner(Request $request){
				$check = PropertyOwners::where(['verification_token'=>base64_decode($request->auth), 'user_id'=>base64_decode($request->user)])->first();
				if (!empty($check)) {
						$status = 'verified';

						$check_setup = PropertyShowingSetup::where('property_id', base64_decode($request->property))->first();
						if (empty($check_setup)) {
							$time = strtotime(Carbon::now());
			    		$setup_uuid = "show".$time.rand(10,99)*rand(10,99);
			    		
				      $setup = new PropertyShowingSetup;
				      $setup->uuid = $setup_uuid;
				      $setup->property_id = base64_decode($request->property);
				      $setup->notification_email = 'YES';
				      $setup->notification_text = 'YES';
				      $setup->type = 'VALID';
				      $setup->validator = null;
				      $setup->presence = null;
				      $setup->instructions = null;
				      $setup->lockbox_type = null;
				      $setup->lockbox_location = null;
		      		$setup->start_date = null;
		      		$setup->end_date = null;
				      $setup->timeframe = '30';
				      $setup->overlap = 'NO';
				      $save_setup = $setup->save();
						}
						
						PropertyOwners::where(['verification_token'=>base64_decode($request->auth), 'user_id', base64_decode($request->user)])->update(['verify_status'=>'YES']);
				}else{
						$status = 'expired';
				}

				return view('verified-owner', ["status"=>$status]);
		}

		public function checkAgent(){
				$curl = curl_init();

				curl_setopt_array($curl, array(
					  CURLOPT_URL => 'https://api.homendo.com/v9/hmdo-agent-check.php',
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'POST',
					  CURLOPT_POSTFIELDS => array(
					  	'login' => '@*8Dom0sH0Ag3#DI',
					  	'token' => md5(strtotime('now')),
					  	'agentid' => 'rxm_999991',
					  	'email' => 'kalpit@broadview-innovations.com',
					  	'originator' => 'RECOLORADO',
					  	'deviceid' => $_SERVER['HTTP_USER_AGENT'],
					  	'hmdoapp' => 'Showing.VIP-1.0'
					  ),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				
				return $response;
		}
}