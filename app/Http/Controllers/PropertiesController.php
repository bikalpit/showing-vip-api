<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\PropertyOwners;
use App\Models\PropertyAgents;
use App\Models\Users;
use App\Mail\AssignAgent;
use App\Mail\SignupMail;
use App\Mail\AssignOwner;
use Carbon\Carbon;
use DB;

class PropertiesController extends Controller
{
		public function addProperty(Request $request){
				$this->validate($request, [
	      		'user_id' => 'required',
	      		'mls_id' => 'required',
	          'agent_id' => 'nullable',
	          'verified' => 'required|in:P,VS,V',
	          'title' => 'required',
	          'type' => 'required',
	          'size' => 'required',
	          'status' => 'required',
	          'year_built' => 'required',
	          'lat_area' => 'required',
	          'elementary' => 'required',
	          'middle' => 'required',
	          'high' => 'required',
	          'district' => 'required',
	          'phone' => 'required',
	          'office' => 'required',
	          'hoa' => 'required',
	          'taxes' => 'required',
	          'parking' => 'required',
	          'sources' => 'required',
	          'disclaimer' => 'required'
	      ]);
				//dd($request->all());
	      $time = strtotime(Carbon::now());
        $uuid = "prty".$time.rand(10,99)*rand(10,99);
	      $property = new Properties;
	      $property->uuid = $uuid;
	      $property->mls_id = $request->mls_id;
	      $property->agent_id = $request->agent_id;
	      $property->verified = $request->verified;
	      $property->title = $request->title;
	      $property->type = $request->type;
	      $property->size = $request->size;
	      $property->status = $request->status;
	      $property->year_built = $request->year_built;
	      $property->lat_area = $request->lat_area;
	      $property->elementary = $request->elementary;
	      $property->middle = $request->middle;
	      $property->high = $request->high;
	      $property->district = $request->district;
	      $property->phone = $request->phone;
	      $property->office = $request->office;
	      $property->hoa = $request->hoa;
	      $property->taxes = $request->taxes;
	      $property->parking = $request->parking;
	      $property->sources = $request->sources;
	      $property->disclaimer = $request->disclaimer;
	      $add_property = $property->save();

	      $owner = new PropertyOwners;
	      $owner->property_id = $property->uuid;
	      $owner->user_id = $request->user_id;
	      $property_owner = $owner->save();

	      if ($property_owner) {
	      		return $this->sendResponse("Property added successfully!");
	      }else{
	      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
	      }
		}

		public function userProperties(Request $request){
				$this->validate($request, [
	      		'user_id' => 'required'
	      ]);

	      $property_ids = PropertyOwners::where('user_id', $request->user_id)->pluck('property_id')->toArray();

	      if (sizeof($property_ids) > 0) {
	      		$properties = Properties::whereIn('uuid', $property_ids)->get();
	      		return $this->sendResponse($properties);
	      }else{
	      		return $this->sendResponse("Sorry, Property not found!", 200, false);
	      }
		}

		public function assignAgent(Request $request){
				$this->validate($request, [
	      		'property_id' => 'required',
	      		'agent_id' => 'required',
	      		'user_id' => 'required'
	      ]);

				$property = Properties::where('uuid', $request->property_id)->first();
				$agent = Users::where('uuid', $request->agent_id)->first();

				if (!empty($property)) {
						$property_agent = new PropertyAgents;
						$property_agent->property_id = $request->property_id;
						$property_agent->agent_id = $request->agent_id;
						$property_agent->user_id = $request->user_id;
						$result = $property_agent->save();
						if ($result) {
								$this->configSMTP();
								$data = ['name'=>$agent->first_name.' '.$agent->last_name, 
				                'property_id'=>$request->property_id,
				                'property_name'=>$property->title
			              ];
								try{
					          Mail::to($agent->email)->send(new AssignAgent($data));  
					      }catch(\Exception $e){
					          $msg = $e->getMessage();
					          return $this->sendResponse($msg, 200, false);
					      }
								return $this->sendResponse("Agent assigned successfully!");
						}else{
								return $this->sendResponse("Sorry, Something went wrong!", 200, false);
						}
				}else{
						return $this->sendResponse("Sorry, Property not found!", 200, false);
				}
		}

		public function removeAgent(Request $request){
				$this->validate($request, [
	      		'property_id' => 'required',
	      		'agent_id' => 'required',
	      		'user_id' => 'required'
	      ]);

	      $result = PropertyAgents::where(['property_id'=>$request->property_id, 'agent_id'=>$request->agent_id, 'user_id'=>$request->user_id])->delete();

	      if ($result) {
	      		return $this->sendResponse("Agent removed successfully!");
	      }else{
	      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
	      }
		}

		public function verifyProperty(Request $request){
				$this->validate($request, [
	      		'property_id' => 'required'
	      ]);

	      $property = Properties::where('uuid', $request->property_id)->first();

	      if (!empty($property)) {
	      		$update = Properties::where('uuid', $request->property_id)->update(['verified'=>'V']);
	      		if ($update) {
	      				return $this->sendResponse("Property verified successfully!");
	      		}else{
			      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
			      }
	      }else{
						return $this->sendResponse("Sorry, Property not found!", 200, false);
				}
		}

		public function verifiedProperties(Request $request){
				$this->validate($request, [
	      		'user_id' => 'required'
	      ]);

	      $property_ids = PropertyOwners::where('user_id', $request->user_id)->pluck('property_id')->toArray();

	      if (sizeof($property_ids) !== 0) {
	      		$properties = Properties::whereIn('uuid', $property_ids)->where('verified', 'V')->get();
	      		if (sizeof($properties) !== 0) {
	      				return $this->sendResponse($properties);
	      		}else{
								return $this->sendResponse("Sorry, Verified property not found!", 200, false);
						}
	      }else{
						return $this->sendResponse("Sorry, Property not found!", 200, false);
				}
		}

		public function addOwner(Request $request){
				$this->validate($request, [
	      		'first_name' => 'required',
	      		'last_name' => 'required',
	      		'email' => 'required',
	      		'user_id' => 'required',
	      		'property_id' => 'required',
	      		'url' => 'required'
	      ]);

	      $prop_owner = Users::where('uuid', $request->user_id)->first();
	      $check = Users::where('email', $request->email)->first();
	      $property = Properties::where('uuid', $request->property_id)->first();

	      if ($check !== null) {
	    			return $this->sendResponse("Sorry, Email already exist!", 200, false);
	      }else{
	      		\DB::beginTransaction();
	      		try{
								$time = strtotime(Carbon::now());
				        $uuid = "usr".$time.rand(10,99)*rand(10,99);
					      $user = new Users;
				        $user->uuid = $uuid;
				        $user->first_name = $request->first_name;
				        $user->last_name = $request->last_name;
				        $user->email = $request->email;
				        $user->role = "USER";
				        $user->sub_role = "SELLER";
				        $user->phone_verified = "NO";
				        $user->email_verified = "NO";
				        $user->image = "default.png";
				        $result = $user->save();

				        $owner = new PropertyOwners;
					      $owner->property_id = $property->uuid;
					      $owner->user_id = $user->uuid;
					      $property_owner = $owner->save();

								$this->configSMTP();
								$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
		      			Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

		      			$dataAssignOwner = ['name'=>$request->first_name.' '.$request->last_name,
		      									'owner_name'=>$prop_owner->first_name.' '.$prop_owner->last_name,
		      									'property_name'=>$property->title
		      							];

					      $dataSignupMail = ['name'=>$request->first_name.' '.$request->last_name,
						                'verification_token'=>$verification_token,
						                'email'=>$request->email,
						                'url'=>$request->url
					              ];
					      try{
					          Mail::to($request->email)->send(new AssignOwner($dataAssignOwner));
					          Mail::to($request->email)->send(new SignupMail($dataSignupMail));
					      }catch(\Exception $e){
					          $msg = $e->getMessage();
					          return $this->sendResponse($msg, 200, false);
					      }

							  return $this->sendResponse("Owner added successfully!");
						} catch(\Exception $e) {
			      		\DB::rollBack();
			      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
			      }
				}
		}

		public function agentProperties(Request $request){
				$this->validate($request, [
	      		'agent_id' => 'required'
	      ]);

	      $property_ids = PropertyAgents::where('agent_id', $request->agent_id)->pluck('property_id')->toArray();

	      if (sizeof($property_ids) > 0) {
	      		$properties = Properties::whereIn('uuid', $property_ids)->get();
	      		return $this->sendResponse($properties);
	      }else{
	      		return $this->sendResponse("Sorry, Property not found!", 200, false);
	      }
		}
}