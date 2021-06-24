<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\PropertyOwners;
use App\Models\PropertyAgents;
use App\Models\Users;
use App\Models\PropertyVerification;
use App\Models\PropertyShowingSetup;
use App\Models\PropertyValuecheck;
use App\Models\PropertyHomendo;
use App\Models\PropertyZillow;
use App\Mail\AssignAgent;
use App\Mail\SignupMail;
use App\Mail\AssignOwner;
use App\Mail\PropertyVerificationMail;
use Carbon\Carbon;
use DB;

class PropertiesController extends Controller
{
		public function addProperty(Request $request){
				$this->validate($request, [
	      		'user_id' => 'required',
	          'data' => 'required'
	      ]);
				/*dd($request->data['property'][1][1]['z_zpid'][1]);*/
				$mls_id = $request->data['property'][2][1]['hmdo_mls_id'][1];

	      $time = strtotime(Carbon::now());
        $uuid = "prty".$time.rand(10,99)*rand(10,99);
	      $property = new Properties;
	      $property->uuid = $uuid;
	      $property->mls_id = $mls_id;
	      $property->data = json_encode($request->data);
	      $property->verified = 'NO';
	      $add_property = $property->save();

	      $valuecheck = new PropertyValuecheck;
	      $valuecheck->uuid = "vlck".$time.rand(10,99)*rand(10,99);
	      $valuecheck->property_id = $uuid;
	      $valuecheck->vs_streetnumber = $request->data['property'][0][1]['vs_streetnumber'][1];
	      $valuecheck->vs_streetdirection = $request->data['property'][0][1]['vs_streetdirection'][1];
	      $valuecheck->vs_streetname = $request->data['property'][0][1]['vs_streetname'][1];
	      $valuecheck->vs_streettype = $request->data['property'][0][1]['vs_streettype'][1];
	      $valuecheck->vs_unitnumber = $request->data['property'][0][1]['vs_unitnumber'][1];
	      $valuecheck->vs_city = $request->data['property'][0][1]['vs_city'][1];
	      $valuecheck->vs_state = $request->data['property'][0][1]['vs_state'][1];
	      $valuecheck->vs_zipcode = $request->data['property'][0][1]['vs_zipcode'][1];
	      $valuecheck->vs_county = $request->data['property'][0][1]['vs_county'][1];
	      $valuecheck->vs_countyname = $request->data['property'][0][1]['vs_countyname'][1];
	      $valuecheck->vs_country = $request->data['property'][0][1]['vs_country'][1];
	      $valuecheck->vs_apn = $request->data['property'][0][1]['vs_apn'][1];
	      $valuecheck->vs_assessyr = $request->data['property'][0][1]['vs_assessyr'][1];
	      $valuecheck->vs_assesmkt = $request->data['property'][0][1]['vs_assesmkt'][1];
	      $valuecheck->vs_landmktval = $request->data['property'][0][1]['vs_landmktval'][1];
	      $valuecheck->vs_taxyr = $request->data['property'][0][1]['vs_taxyr'][1];
	      $valuecheck->vs_taxdue = $request->data['property'][0][1]['vs_taxdue'][1];
	      $valuecheck->vs_esttaxes = $request->data['property'][0][1]['vs_esttaxes'][1];
	      $valuecheck->vs_ownername = $request->data['property'][0][1]['vs_ownername'][1];
	      $valuecheck->vs_ownername2 = $request->data['property'][0][1]['vs_ownername2'][1];
	      $valuecheck->vs_formallegal = $request->data['property'][0][1]['vs_formallegal'][1];
	      $valuecheck->vs_saledate = $request->data['property'][0][1]['vs_saledate'][1];
	      $valuecheck->vs_docdate = $request->data['property'][0][1]['vs_docdate'][1];
	      $valuecheck->vs_saleamt = $request->data['property'][0][1]['vs_saleamt'][1];
	      $valuecheck->vs_pricesqft = $request->data['property'][0][1]['vs_pricesqft'][1];
	      $valuecheck->vs_longitude = $request->data['property'][0][1]['vs_longitude'][1];
	      $valuecheck->vs_latitude = $request->data['property'][0][1]['vs_latitude'][1];
	      $valuecheck->vs_proptype = $request->data['property'][0][1]['vs_proptype'][1];
	      $valuecheck->vs_stories = $request->data['property'][0][1]['vs_stories'][1];
	      $valuecheck->vs_housestyle = $request->data['property'][0][1]['vs_housestyle'][1];
	      $valuecheck->vs_squarefeet = $request->data['property'][0][1]['vs_squarefeet'][1];
	      $valuecheck->vs_bsmtsf = $request->data['property'][0][1]['vs_bsmtsf'][1];
	      $valuecheck->vs_finbsmtsf = $request->data['property'][0][1]['vs_finbsmtsf'][1];
	      $valuecheck->vs_bsmttype = $request->data['property'][0][1]['vs_bsmttype'][1];
	      $valuecheck->vs_bedrooms = $request->data['property'][0][1]['vs_bedrooms'][1];
	      $valuecheck->vs_bathrooms = $request->data['property'][0][1]['vs_bathrooms'][1];
	      $valuecheck->vs_garagetype = $request->data['property'][0][1]['vs_garagetype'][1];
	      $valuecheck->vs_garagesqft = $request->data['property'][0][1]['vs_garagesqft'][1];
	      $valuecheck->vs_carspaces = $request->data['property'][0][1]['vs_carspaces'][1];
	      $valuecheck->vs_fireplaces = $request->data['property'][0][1]['vs_fireplaces'][1];
	      $valuecheck->vs_heat = $request->data['property'][0][1]['vs_heat'][1];
	      $valuecheck->vs_cool = $request->data['property'][0][1]['vs_cool'][1];
	      $valuecheck->vs_extwall = $request->data['property'][0][1]['vs_extwall'][1];
	      $valuecheck->vs_roofcover = $request->data['property'][0][1]['vs_roofcover'][1];
	      $valuecheck->vs_roofstyle = $request->data['property'][0][1]['vs_roofstyle'][1];
	      $valuecheck->vs_yearblt = $request->data['property'][0][1]['vs_yearblt'][1];
	      $valuecheck->vs_lotsizec = $request->data['property'][0][1]['vs_lotsizec'][1];
	      $valuecheck->vs_lotsize = $request->data['property'][0][1]['vs_lotsize'][1];
	      $valuecheck->vs_acre = $request->data['property'][0][1]['vs_acre'][1];
	      $valuecheck->vs_pool = $request->data['property'][0][1]['vs_pool'][1];
	      $valuecheck->vs_spa = $request->data['property'][0][1]['vs_spa'][1];
	      $valuecheck->vs_foundation = $request->data['property'][0][1]['vs_foundation'][1];
	      $valuecheck->vs_golf = $request->data['property'][0][1]['vs_golf'][1];
	      $valuecheck->vs_lotwidth = $request->data['property'][0][1]['vs_lotwidth'][1];
	      $valuecheck->vs_lotlength = $request->data['property'][0][1]['vs_lotlength'][1];
	      $valuecheck->vs_waterfront = $request->data['property'][0][1]['vs_waterfront'][1];
	      $valuecheck->vs_extwallcover = $request->data['property'][0][1]['vs_extwallcover'][1];
	      $valuecheck->vs_intwall = $request->data['property'][0][1]['vs_intwall'][1];
	      $valuecheck->vs_decksqft = $request->data['property'][0][1]['vs_decksqft'][1];
	      $valuecheck->vs_deckdesc = $request->data['property'][0][1]['vs_deckdesc'][1];
	      $valuecheck->vs_patiosqft = $request->data['property'][0][1]['vs_patiosqft'][1];
	      $valuecheck->vs_patiodesc = $request->data['property'][0][1]['vs_patiodesc'][1];
	      $valuecheck->vs_waterservice = $request->data['property'][0][1]['vs_waterservice'][1];
	      $valuecheck->vs_sewerservice = $request->data['property'][0][1]['vs_sewerservice'][1];
	      $valuecheck->vs_electricservice = $request->data['property'][0][1]['vs_electricservice'][1];
	      $add_valuecheck = $valuecheck->save();

	      $zillow = new PropertyZillow;
	      $zillow->uuid = "zilw".$time.rand(10,99)*rand(10,99);
	      $zillow->property_id = $uuid;
	      $zillow->z_zpid = $request->data['property'][1][1]['z_zpid'][1];
	      $zillow->z_sale_amount = $request->data['property'][1][1]['z_sale_amount'][1];
	      $zillow->z_sale_lowrange = $request->data['property'][1][1]['z_sale_lowrange'][1];
	      $zillow->z_sale_highrange = $request->data['property'][1][1]['z_sale_highrange'][1];
	      $zillow->z_sale_lastupdated = $request->data['property'][1][1]['z_sale_lastupdated'][1];
	      $zillow->z_rental_amount = $request->data['property'][1][1]['z_rental_amount'][1];
	      $zillow->z_rental_lowrange = $request->data['property'][1][1]['z_rental_lowrange'][1];
	      $zillow->z_rental_highrange = $request->data['property'][1][1]['z_rental_highrange'][1];
	      $zillow->z_rental_lastupdated = $request->data['property'][1][1]['z_rental_lastupdated'][1];
	      $zillow->z_prop_url = $request->data['property'][1][1]['z_prop_url'][1];
	      $add_zillow = $zillow->save();

	      $homendo = new PropertyHomendo;
	      $homendo->uuid = "hmdo".$time.rand(10,99)*rand(10,99);
	      $homendo->property_id = $uuid;
	      $homendo->hmdo_listed = $request->data['property'][2][1]['hmdo_listed'][1];
	      $homendo->hmdo_lastupdated = $request->data['property'][2][1]['hmdo_lastupdated'][1];
	      $homendo->hmdo_mls_id = $request->data['property'][2][1]['hmdo_mls_id'][1];
	      $homendo->hmdo_mls_originator = $request->data['property'][2][1]['hmdo_mls_originator'][1];
	      $homendo->hmdo_mls_proptype = $request->data['property'][2][1]['hmdo_mls_proptype'][1];
	      $homendo->hmdo_mls_propname = $request->data['property'][2][1]['hmdo_mls_propname'][1];
	      $homendo->hmdo_mls_status = $request->data['property'][2][1]['hmdo_mls_status'][1];
	      $homendo->hmdo_mls_price = $request->data['property'][2][1]['hmdo_mls_price'][1];
	      $homendo->hmdo_mls_url = $request->data['property'][2][1]['hmdo_mls_url'][1];
	      $homendo->hmdo_mls_thumbnail = $request->data['property'][2][1]['hmdo_mls_thumbnail'][1];
	      $add_homendo = $homendo->save();

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

		public function updateProperty(Request $request){
				$this->validate($request, [
	      		'property_id' => 'required',
	      		'verified' => 'required|in:YES,NO',
	          'data' => 'required'
	      ]);

	      $property = Properties::where('uuid', $request->property_id)->first();

	      if (!empty($property)) {
	      		$update_property = Properties::where('uuid', $request->property_id)->update(['data'=>json_encode($request->data), 'verified'=>$request->verified]);

	      		if ($update_property) {
	      				return $this->sendResponse("Property updated successfully!");
	      		}else{
	      				return $this->sendResponse("Sorry, Something went wrong!", 200, false);
	      		}
	      }else{
	      		return $this->sendResponse("Sorry, Property not found!", 200, false);
	      }
		}

		public function getProperty(Request $request){
				$this->validate($request, [
	      		'property_id' => 'required'
	      ]);

	      $property = Properties::with('Valuecheck', 'Zillow', 'Homendo')->where('uuid', $request->property_id)->first();

	      if (!empty($property)) {
	      		return $this->sendResponse($property);
	      }else{
	      		return $this->sendResponse("Sorry, Property not found!", 200, false);
	      }
		}

		public function userProperties(Request $request){
				$this->validate($request, [
	      		'user_id' => 'required'
	      ]);

	      $property_ids = PropertyOwners::where('user_id', $request->user_id)->pluck('property_id')->toArray();

	      if (sizeof($property_ids) > 0) {
	      		$all_properties = [];
	      		$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->get();
	      		foreach ($properties as $property) {
		      			$user_ids = PropertyOwners::where('property_id', $property->uuid)->pluck('user_id')->toArray();
		      			if (sizeof($user_ids) < 0) {
		      					$property['owners'] = null;
		      			}else{
		      					$property['owners'] = Users::whereIn('uuid', array_unique($user_ids))->get();
		      			}
		      			$all_properties[] = $property;
	      		}
	      		return $this->sendResponse($all_properties);
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
								$data = [
										'name'=>$agent->first_name.' '.$agent->last_name, 
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
	      		'property_id' => 'required',
	      		'agent_id' => 'required',
	      		'user_id' => 'required',
	      		'property_link' => 'required'	
	      ]);

	      $property = Properties::where('uuid', $request->property_id)->first();
	      $agent = Users::where('uuid', $request->agent_id)->first();
	      $owner = Users::where('uuid', $request->user_id)->first();
	      
	      if (!empty($property)) {
	      		$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );

	      		$this->configSMTP();
						$data = [
								'name'=>$agent->first_name.' '.$agent->last_name,
								'owner_name'=>$owner->first_name.' '.$owner->last_name,
                'property_id'=>$request->property_id,
                'property_link'=>$request->property_link,
                'site_url'=>env('APP_URL'),
                'token'=>$verification_token
            ];

						try{
			          Mail::to($agent->email)->send(new PropertyVerificationMail($data));

			          PropertyVerification::where('property_id', $request->property_id)->delete();

			          $property_varification = new PropertyVerification;
			          $property_varification->property_id = $request->property_id;
			          $property_varification->agent_id = $request->agent_id;
			          $property_varification->user_id = $request->user_id;
			          $property_varification->token = $verification_token;
			          $result = $property_varification->save();

			          if ($result) {
			      				return $this->sendResponse("Verification mail sent successfully to agent!");
			      		}else{
					      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
					      }
			      }catch(\Exception $e){
			          $msg = $e->getMessage();
			          return $this->sendResponse($msg, 200, false);
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
	      		$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->where('verified', 'YES')->get();
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

		      			$dataAssignOwner = [
		      					'name'=>$request->first_name.' '.$request->last_name,
  									'owner_name'=>$prop_owner->first_name.' '.$prop_owner->last_name,
  									'property_name'=>$property->title
  							];

					      $dataSignupMail = [
					      		'name'=>$request->first_name.' '.$request->last_name,
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
	      		'agent_id' => 'required',
	      		'search' => 'nullable',
	      		'sorting' => 'nullable|in:ASC,DESC'
	      ]);

				$search_item = $request->search;
				$sorting = $request->sorting;
				
	      $property_ids = PropertyAgents::where('agent_id', $request->agent_id)->pluck('property_id')->toArray();

	      if (sizeof($property_ids) > 0) {
	      		if ($sorting !== '') {
	      				if ($sorting == 'ASC') {
	      						if ($search_item !== '') {
												$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->where(function($query) use ($search_item) {
														$query->where('mls_id', 'LIKE', '%'.$search_item.'%');
				              			//->orWhere('ga_customer.email', 'LIKE', '%'.$search_item.'%')
												})->orderBy('created_at', 'ASC')->get();
										}else{
												$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->orderBy('created_at', 'ASC')->get();
										}
	      				}elseif ($sorting == 'DESC') {
	      						if ($search_item !== '') {
	      								$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->where(function($query) use ($search_item) {
														$query->where('mls_id', 'LIKE', '%'.$search_item.'%');
				              			//->orWhere('ga_customer.email', 'LIKE', '%'.$search_item.'%')
												})->orderBy('created_at', 'DESC')->get();
										}else{
												$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->orderBy('created_at', 'DESC')->get();
										}
	      				}else{
	      						if ($search_item !== '') {
												$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->where(function($query) use ($search_item) {
														$query->where('mls_id', 'LIKE', '%'.$search_item.'%');
				              			//->orWhere('ga_customer.email', 'LIKE', '%'.$search_item.'%')
												})->get();
										}else{
												$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->get();
										}
	      				}
	      		}else{
	      				if ($search_item !== '') {
										$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->where(function($query) use ($search_item) {
												$query->where('mls_id', 'LIKE', '%'.$search_item.'%');
		              			//->orWhere('ga_customer.email', 'LIKE', '%'.$search_item.'%')
										})->get();
								}else{
										$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->get();
								}
	      		}
			      		

	      		if (sizeof($properties) > 0) {
	      				return $this->sendResponse($properties);
	      		}else{
			      		return $this->sendResponse("Sorry, Property not found!", 200, false);
			      }
	      }else{
	      		return $this->sendResponse("Sorry, Property not found!", 200, false);
	      }
		}

		public function verifiedProperty(Request $request){
				
				$check = PropertyVerification::where('token', $request->token)->first();
				if (!empty($check)) {
						$status = 'verified';

						$time = strtotime(Carbon::now());

		    		$setup_uuid = "show".$time.rand(10,99)*rand(10,99);
			      $setup = new PropertyShowingSetup;
			      $setup->uuid = $setup_uuid;
			      $setup->property_id = $request->property;
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

			      Properties::where('uuid', $request->property)->update(['verified' => 'YES']);
			      
			      $property_agent = new PropertyAgents;
						$property_agent->property_id = $check->property_id;
						$property_agent->agent_id = $check->agent_id;
						$property_agent->user_id = $check->user_id;
						$result = $property_agent->save();
				}else{
						$status = 'expired';
				}

				return view('verified-property', ["status"=>$status]);
		}
}