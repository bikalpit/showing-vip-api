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
use Carbon\Carbon;

class PropertiesController extends Controller
{
		public function addProperty(Request $request){
				$this->validate($request, [
	      		'user_id' => 'required',
	      		'mls_id' => 'required',
	          'agent_id' => 'nullable',
	          'property_verified' => 'required|in:P,VS,V',
	          'property_title' => 'required',
	          'property_type' => 'required',
	          'property_size' => 'required',
	          'property_status' => 'required',
	          'property_year_built' => 'required',
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
	      $property->property_verified = $request->property_verified;
	      $property->property_title = $request->property_title;
	      $property->property_type = $request->property_type;
	      $property->property_size = $request->property_size;
	      $property->property_status = $request->property_status;
	      $property->property_year_built = $request->property_year_built;
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
				                'property_name'=>$property->property_title
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
	      		$update = Properties::where('uuid', $request->property_id)->update(['property_verified'=>'V']);
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
	      		$properties = Properties::whereIn('uuid', $property_ids)->where('property_verified', 'V')->get();
	      		if (sizeof($properties) !== 0) {
	      				return $this->sendResponse($properties);
	      		}else{
								return $this->sendResponse("Sorry, Verified property not found!", 200, false);
						}
	      }else{
						return $this->sendResponse("Sorry, Property not found!", 200, false);
				}
		}
}