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
use App\Models\PropertyBuyers;
use App\Models\PropertyBookingSchedule;
use App\Models\AgentInfo;
use App\Models\PropertyShowingAvailability;
use App\Models\PropertyShowingSurvey;
use App\Models\PropertyImages;
use App\Mail\AssignAgent;
use App\Mail\SignupMail;
use App\Mail\AssignOwner;
use App\Mail\PropertyOwnerVerificationMail;
use App\Mail\PropertyVerificationMail;
use App\Mail\OwnerVerificationMail;
use Carbon\Carbon;
use DB;

class PropertiesController extends Controller
{
		public function addProperty(Request $request){
				$this->validate($request, [
		    		'user_id' => 'required',
	        	'data' => 'required',
	        	'agent_info' => 'nullable',
	        	'url' => 'required'
		    ]);

				$user = Users::where('uuid', $request->user_id)->first();

				$vs_listed = $request->data['property'][0][1]['vs_listed'][1];
				$z_listed = $request->data['property'][1][1]['z_listed'][1];
				$hmdo_listed = $request->data['property'][2][1]['hmdo_listed'][1];

				if ($request->data['property'][2][1]['hmdo_mls_price'][1] != null || $request->data['property'][2][1]['hmdo_mls_price'][1] != '') {
						$hmdo_mls_price = $request->data['property'][2][1]['hmdo_mls_price'][1];
				}else{
						$hmdo_mls_price = 0;
				}

				if (is_array($request->data['property'][2][1]['hmdo_mls_id'][1]) == true) {
						$mls_id = $request->data['property'][2][1]['hmdo_mls_id'][1][0];
		    }else{
		    		$mls_id = $request->data['property'][2][1]['hmdo_mls_id'][1];
		    }

		    if (is_array($request->data['property'][2][1]['hmdo_mls_originator'][1]) == true) {
						$mls_name = $request->data['property'][2][1]['hmdo_mls_originator'][1][0];
		    }else{
		    		$mls_name = $request->data['property'][2][1]['hmdo_mls_originator'][1];
		    }

		    $checkAgent = Users::where(['mls_id'=>$request->data['property'][2][1]['hmdo_mls_agentid'][1], 'mls_name'=>$mls_name, 'email'=>$request->data['property'][2][1]['hmdo_mls_agent_email'][1]])->first();

		    $mlsIdCheck = Properties::where(['mls_id'=>$mls_id])->first();
		    $mlsNameCheck = Properties::where(['mls_id'=>$mls_id, 'mls_name'=>$mls_name])->first();

		    if($vs_listed == 1 || $z_listed == 1 || $hmdo_listed == 1){
		    		if ($mls_id != '' && $mls_name != '') {
				    		if (!empty($mlsIdCheck)) {
				    				if (!empty($mlsNameCheck)) {
				    						$verify_status = 'NO';
								      	if ($request->data['property'][0][1]['vs_ownername'][1] != null || $request->data['property'][0][1]['vs_ownername'][1] != '') {
								      			if (strpos($request->data['property'][0][1]['vs_ownername'][1], $user->last_name) == true) {
													      $verify_status = 'PV';
										      	}else{
										      			$verify_status = 'NO';
								      			}
								      	}

								      	if ($verify_status == 'NO') {
								      			if ($request->data['property'][0][1]['vs_ownername2'][1] != null || $request->data['property'][0][1]['vs_ownername2'][1] != '') {
								      					if (strpos($request->data['property'][0][1]['vs_ownername2'][1], $user->last_name) == true) {
										      					$verify_status = 'PV';
										      			}else{
										      					$verify_status = 'NO';
										      			}
										      	}
								      	}

								      	$checkOwner = PropertyOwners::where(['property_id'=>$mlsNameCheck->uuid, 'user_id'=>$request->user_id])->first();
								      	if ($checkOwner == null) {
								      			$owner = new PropertyOwners;
										      	$owner->property_id = $mlsNameCheck->uuid;
										      	$owner->user_id = $request->user_id;
										      	$owner->type = 'main_owner';
										      	$owner->verify_status = $verify_status;
										      	$property_owner = $owner->save();
								      	}

				    						$property_homendo = PropertyHomendo::where('property_id', $mlsNameCheck->uuid)->first();
				    						
								      	if (empty($checkAgent) || $checkAgent == null) {
									      		$time = strtotime(Carbon::now());
								            $agent_uuid = "usr".$time.rand(10,99)*rand(10,99);

								            $agent = new Users;
								            $agent->uuid = $agent_uuid;
								            $agent->email = $request->data['property'][2][1]['hmdo_mls_agent_email'][1];
								            $agent->role = "AGENT";
								            $agent->mls_id = $request->data['property'][2][1]['hmdo_mls_agentid'][1];
								            $agent->mls_name = $mls_name;
								            $agent->phone_verified = "NO";
								            $agent->email_verified = "NO";
								            $agent->image = env("APP_URL")."public/user-images/default.png";
								            $result = $agent->save();
								            if ($result) {
									            	$agent_info = new AgentInfo;
										        		$agent_info->agent_id = $agent_uuid;
										        		$agent_info->hmdo_lastupdated = ($request->agent_info != null)?$request->agent_info['hmdo_lastupdated'][1]:NULL;
										        		$agent_info->hmdo_mls_originator = ($request->agent_info != null)?$request->agent_info['hmdo_mls_originator'][1]:NULL;
										        		$agent_info->hmdo_agent_name = ($request->agent_info != null)?$request->agent_info['hmdo_agent_name'][1]:NULL;
										        		$agent_info->hmdo_agent_title = ($request->agent_info != null)?$request->agent_info['hmdo_agent_title'][1]:NULL;
										        		$agent_info->hmdo_agent_photo_url = ($request->agent_info != null)?$request->agent_info['hmdo_agent_photo_url'][1]:NULL;
										        		$agent_info->hmdo_agent_email = ($request->agent_info != null)?$request->agent_info['hmdo_agent_email'][1]:NULL;
										        		$agent_info->hmdo_office_main_phone = ($request->agent_info != null)?$request->agent_info['hmdo_office_main_phone'][1]:NULL;
										        		$agent_info->hmdo_office_direct_phone = ($request->agent_info != null)?$request->agent_info['hmdo_office_direct_phone'][1]:NULL;
										        		$agent_info->hmdo_agent_mobile_phone = ($request->agent_info != null)?$request->agent_info['hmdo_agent_mobile_phone'][1]:NULL;
										        		$agent_info->hmdo_agent_skills = ($request->agent_info != null)?$request->agent_info['hmdo_agent_skills'][1]:NULL;
										        		$agent_info->hmdo_office_id = ($request->agent_info != null)?$request->agent_info['hmdo_office_id'][1]:NULL;
										        		$agent_info->hmdo_office_name = ($request->agent_info != null)?$request->agent_info['hmdo_office_name'][1]:NULL;
										        		$agent_info->hmdo_office_photo = ($request->agent_info != null)?$request->agent_info['hmdo_office_photo'][1]:NULL;
										        		$agent_info->hmdo_office_street = ($request->agent_info != null)?$request->agent_info['hmdo_office_street'][1]:NULL;
										        		$agent_info->hmdo_office_city = ($request->agent_info != null)?$request->agent_info['hmdo_office_city'][1]:NULL;
										        		$agent_info->hmdo_office_zipcode = ($request->agent_info != null)?$request->agent_info['hmdo_office_zipcode'][1]:NULL;
										        		$agent_info->hmdo_office_state = ($request->agent_info != null)?$request->agent_info['hmdo_office_state'][1]:NULL;
										        		$agent_info->hmdo_office_phone = ($request->agent_info != null)?$request->agent_info['hmdo_office_phone'][1]:NULL;
										        		$agent_info->hmdo_office_website = ($request->agent_info != null)?$request->agent_info['hmdo_office_website'][1]:NULL;
										        		$agent_info->hmdo_agent_website = ($request->agent_info != null)?$request->agent_info['hmdo_agent_website'][1]:NULL;
										        		$agent_info->save();

										        		$check_agent = PropertyAgents::where(['property_id'=>$mlsNameCheck->uuid, 'agent_id'=>$agent->uuid, 'agent_type'=>'seller'])->first();
				                        
				                        if (empty($check_agent)) {
				                            $property_agent = new PropertyAgents;
				                            $property_agent->property_id = $mlsNameCheck->uuid;
				                            $property_agent->property_mls_id = $mlsNameCheck->mls_id;
				                            $property_agent->property_originator = $mlsNameCheck->mls_name;
				                            $property_agent->agent_id = $agent->uuid;
				                            $property_agent->agent_type = 'seller';
				                            $property_agent->save();
				                        }

								            		$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
										            Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

										            $this->configSMTP();
										            $data = [
										                'name'=>'',
										                'verification_token'=>$verification_token,
										                'email'=>$agent->email,
										                'url'=>$request->url
										            ];
										            Mail::to($agent->email)->send(new SignupMail($data));

										            $property_varification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );

										            /*$verification_data = [
																		'name'=>($request->agent_info != null)?$request->agent_info['hmdo_agent_name'][1]:'',
																		'owner_name'=>$user->first_name.' '.$user->last_name,
										                'property_id'=>$mlsNameCheck->uuid,
										                'property_link'=>$property_homendo->hmdo_mls_url,
										                'site_url'=>env('APP_URL'),
										                'token'=>$property_varification_token
										            ];
										            Mail::to($agent->email)->send(new PropertyVerificationMail($verification_data));

										            $property_varification = new PropertyVerification;
											          $property_varification->property_id = $mlsNameCheck->uuid;
											          $property_varification->agent_id = $agent->uuid;
											          $property_varification->user_id = $request->user_id;
											          $property_varification->token = $property_varification_token;
											          $property_varification->send_time = date('Y-m-d h:i:s');
											          $result = $property_varification->save();*/

										            if ($verify_status == 'NO') {
										            		$update_token = PropertyOwners::where(['user_id'=>$request->user_id, 'property_id'=>$mlsNameCheck->uuid])->update(['verification_token'=>$property_varification_token]);
												            if ($update_token) {
												            		$this->configSMTP();
																				$verification_data = [
																						'owner_name'=>$user->first_name.' '.$user->last_name,
																						'agent_name'=>($request->agent_info != null)?$request->agent_info['hmdo_agent_name'][1]:'',
														                'user_id'=>base64_encode($request->user_id),
														                'property_id'=>base64_encode($mlsNameCheck->uuid),
														                'token'=>base64_encode($property_varification_token)
														            ];
														            try{
																	          Mail::to($agent->email)->send(new OwnerVerificationMail($verification_data));

																	          $property_varification = new PropertyVerification;
																	          $property_varification->property_id = $mlsNameCheck->uuid;
																	          $property_varification->agent_id = $agent->uuid;
																	          $property_varification->user_id = $request->user_id;
																	          $property_varification->token = $property_varification_token;
																	          $property_varification->send_time = date('Y-m-d h:i:s');
																	          $result = $property_varification->save();
																	      }catch(\Exception $e){

																	      }
												            }
										            }
								            }
							      		}else{
							      				$this->configSMTP();
							      				$check_agent = PropertyAgents::where(['property_id'=>$mlsNameCheck->uuid, 'agent_id'=>$checkAgent->uuid, 'agent_type'=>'seller'])->first();
									          
		                        if (empty($check_agent)) {
		                            $property_agent = new PropertyAgents;
		                            $property_agent->property_id = $mlsNameCheck->uuid;
		                            $property_agent->property_mls_id = $mlsNameCheck->mls_id;
		                            $property_agent->property_originator = $mlsNameCheck->mls_name;
		                            $property_agent->agent_id = $checkAgent->uuid;
		                            $property_agent->agent_type = 'seller';
		                            $property_agent->save();
		                        }

							      				$property_varification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
							      				
							      				/*$verification_data = [
																'name'=>$checkAgent->first_name.' '.$checkAgent->last_name,
																'owner_name'=>$user->first_name.' '.$user->last_name,
								                'property_id'=>$mlsNameCheck->uuid,
								                'property_link'=>$property_homendo->hmdo_mls_url,
								                'site_url'=>env('APP_URL'),
								                'token'=>$property_varification_token
								            ];
								            Mail::to($checkAgent->email)->send(new PropertyVerificationMail($verification_data));

									          $property_varification = new PropertyVerification;
									          $property_varification->property_id = $mlsNameCheck->uuid;
									          $property_varification->agent_id = $checkAgent->uuid;
									          $property_varification->user_id = $request->user_id;
									          $property_varification->token = $property_varification_token;
									          $property_varification->send_time = date('Y-m-d h:i:s');
									          $result = $property_varification->save();*/

									          if ($verify_status == 'NO') {
								            		$update_token = PropertyOwners::where(['user_id'=>$request->user_id, 'property_id'=>$mlsNameCheck->uuid])->update(['verification_token'=>$property_varification_token]);
										            if ($update_token) {
										            		$this->configSMTP();
																		$verification_data = [
																				'owner_name'=>$user->first_name.' '.$user->last_name,
																				'agent_name'=>$checkAgent->first_name.' '.$checkAgent->last_name,
												                'user_id'=>base64_encode($request->user_id),
												                'property_id'=>base64_encode($mlsNameCheck->uuid),
												                'token'=>base64_encode($property_varification_token)
												            ];
												            try{
															          Mail::to($checkAgent->email)->send(new OwnerVerificationMail($verification_data));

															          $property_varification = new PropertyVerification;
															          $property_varification->property_id = $mlsNameCheck->uuid;
															          $property_varification->agent_id = $checkAgent->uuid;
															          $property_varification->user_id = $request->user_id;
															          $property_varification->token = $property_varification_token;
															          $property_varification->send_time = date('Y-m-d h:i:s');
															          $result = $property_varification->save();
															      }catch(\Exception $e){

															      }
										            }
								            }
							      		}

							      		if ($verify_status == 'PV') {
								      			$time = strtotime(Carbon::now());
								      			$setup_uuid = "show".$time.rand(10,99)*rand(10,99);

											      $setup = new PropertyShowingSetup;
											      $setup->uuid = $setup_uuid;
											      $setup->property_id = $mlsNameCheck->uuid;
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

								      	return $this->sendResponse("Property added successfully!");
				    				}else{
						    				$time = strtotime(Carbon::now());
								        $uuid = "prty".$time.rand(10,99)*rand(10,99);
								      	$property = new Properties;
								      	$property->uuid = $uuid;
								      	$property->mls_id = $mls_id;
								      	$property->mls_name = $mls_name;
								      	$property->data = json_encode($request->data);
								      	$property->verified = 'NO';
								      	$property->price = str_replace(array('$', ','), '', $hmdo_mls_price);
								      	$property->last_update = date('Y-m-d H:i:s');
								      	$add_property = $property->save();

								      	$valuecheck = new PropertyValuecheck;
								      	$valuecheck->uuid = "vlck".$time.rand(10,99)*rand(10,99);
								      	$valuecheck->property_id = $uuid;
								      	$valuecheck->vs_listed = $vs_listed;
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
								      	$zillow->z_listed = $z_listed;
								      	$zillow->z_zpid = $request->data['property'][1][1]['z_zpid'][1];
								      	$zillow->z_sale_amount = $request->data['property'][1][1]['z_sale_amount'][1];
								      	$zillow->z_sale_lowrange = $request->data['property'][1][1]['z_sale_lowrange'][1];
								      	$zillow->z_sale_highrange = $request->data['property'][1][1]['z_sale_highrange'][1];
								      	$zillow->z_sale_lastupdated = $request->data['property'][1][1]['z_sale_lastupdated'][1];
								      	$zillow->z_rental_amount = $request->data['property'][1][1]['z_rental_amount'][1];
								      	$zillow->z_rental_lowrange = $request->data['property'][1][1]['z_rental_lowrange'][1];
								      	$zillow->z_rental_highrange = $request->data['property'][1][1]['z_rental_highrange'][1];
								      	$zillow->z_rental_lastupdated = $request->data['property'][1][1]['z_rental_lastupdated'][1];
								      	if (is_array($request->data['property'][1][1]['z_prop_url'][1]) == true) {
								  					$zillow->z_prop_url = $request->data['property'][1][1]['z_prop_url'][1]['changingThisBreaksApplicationSecurity'];
								  			}else{
								  					$zillow->z_prop_url = $request->data['property'][1][1]['z_prop_url'][1];
								  			}
								      	$add_zillow = $zillow->save();

								      	$homendo = new PropertyHomendo;
								      	$homendo->uuid = "hmdo".$time.rand(10,99)*rand(10,99);
								      	$homendo->property_id = $uuid;
								      	$homendo->hmdo_listed = $hmdo_listed;
								      	$homendo->hmdo_lastupdated = $request->data['property'][2][1]['hmdo_lastupdated'][1];
								      	$homendo->hmdo_mls_agent_email = $request->data['property'][2][1]['hmdo_mls_agent_email'][1];
								      	$homendo->hmdo_mls_agentid = $request->data['property'][2][1]['hmdo_mls_agentid'][1];
								      	$homendo->hmdo_mls_description = $request->data['property'][2][1]['hmdo_mls_description'][1];
								      	if (is_array($request->data['property'][2][1]['hmdo_mls_id'][1]) == true) {
								      			$homendo->hmdo_mls_id = $request->data['property'][2][1]['hmdo_mls_id'][1][0];
								      	}else{
								      			$homendo->hmdo_mls_id = $request->data['property'][2][1]['hmdo_mls_id'][1];
								      	}
								      	if (is_array($request->data['property'][2][1]['hmdo_mls_originator'][1]) == true) {
								      			$homendo->hmdo_mls_originator = $request->data['property'][2][1]['hmdo_mls_originator'][1][0];
								      	}else{
								      			$homendo->hmdo_mls_originator = $request->data['property'][2][1]['hmdo_mls_originator'][1];
								      	}
								      	if (is_array($request->data['property'][2][1]['hmdo_mls_proptype'][1]) == true) {
								      			$homendo->hmdo_mls_proptype = $request->data['property'][2][1]['hmdo_mls_proptype'][1][0];
								      	}else{
								      			$homendo->hmdo_mls_proptype = $request->data['property'][2][1]['hmdo_mls_proptype'][1];
								      	}
								      	$homendo->hmdo_mls_propname = $request->data['property'][2][1]['hmdo_mls_propname'][1];
								      	if (is_array($request->data['property'][2][1]['hmdo_mls_status'][1]) == true) {
								      			$homendo->hmdo_mls_status = $request->data['property'][2][1]['hmdo_mls_status'][1][0];
								      	}else{
								      			$homendo->hmdo_mls_status = $request->data['property'][2][1]['hmdo_mls_status'][1];
								      	}
								      	$homendo->hmdo_mls_price = $request->data['property'][2][1]['hmdo_mls_price'][1];
								      	if (is_array($request->data['property'][2][1]['hmdo_mls_url'][1]) == true) {
								      			$homendo->hmdo_mls_url = $request->data['property'][2][1]['hmdo_mls_url'][1]['changingThisBreaksApplicationSecurity'];
								      	}else{
								      			$homendo->hmdo_mls_url = $request->data['property'][2][1]['hmdo_mls_url'][1];
								      	}
								      	if (is_array($request->data['property'][2][1]['hmdo_mls_thumbnail'][1]) == true) {
								      			$homendo->hmdo_mls_thumbnail = $request->data['property'][2][1]['hmdo_mls_thumbnail'][1][0];
								      	}else{
								      			$homendo->hmdo_mls_thumbnail = $request->data['property'][2][1]['hmdo_mls_thumbnail'][1];
								      	}
								      	$homendo->hmdo_mls_officeid = $request->data['property'][2][1]['hmdo_mls_officeid'][1];
								      	$add_homendo = $homendo->save();

								      	$verify_status = 'NO';
								      	if ($request->data['property'][0][1]['vs_ownername'][1] != null || $request->data['property'][0][1]['vs_ownername'][1] != '') {
								      			if (strpos($request->data['property'][0][1]['vs_ownername'][1], $user->last_name) == true) {
										      			$verify_status = 'PV';
										      	}else{
										      			$verify_status = 'NO';
								      			}
								      	}

								      	if ($verify_status == 'NO') {
								      			if ($request->data['property'][0][1]['vs_ownername2'][1] != null || $request->data['property'][0][1]['vs_ownername2'][1] != '') {
								      					if (strpos($request->data['property'][0][1]['vs_ownername2'][1], $user->last_name) == true) {
										      					$verify_status = 'PV';
										      			}else{
										      					$verify_status = 'NO';
										      			}
										      	}
								      	}

								      	$checkOwner = PropertyOwners::where(['property_id'=>$property->uuid, 'user_id'=>$request->user_id])->first();
								      	if ($checkOwner == null) {
								      			$owner = new PropertyOwners;
										      	$owner->property_id = $property->uuid;
										      	$owner->user_id = $request->user_id;
										      	$owner->type = 'main_owner';
										      	$owner->verify_status = $verify_status;
										      	$property_owner = $owner->save();
								      	}

								      	if (empty($checkAgent) || $checkAgent == null) {
									      		$time = strtotime(Carbon::now());
								            $agent_uuid = "usr".$time.rand(10,99)*rand(10,99);

								            $agent = new Users;
								            $agent->uuid = $agent_uuid;
								            $agent->email = $request->data['property'][2][1]['hmdo_mls_agent_email'][1];
								            $agent->role = "AGENT";
								            $agent->mls_id = $request->data['property'][2][1]['hmdo_mls_agentid'][1];
								            $agent->mls_name = $mls_name;
								            $agent->phone_verified = "NO";
								            $agent->email_verified = "NO";
								            $agent->image = env("APP_URL")."public/user-images/default.png";
								            $result = $agent->save();
								            if ($result) {
									            	$agent_info = new AgentInfo;
										        		$agent_info->agent_id = $agent_uuid;
										        		$agent_info->hmdo_lastupdated = ($request->agent_info != null)?$request->agent_info['hmdo_lastupdated'][1]:NULL;
										        		$agent_info->hmdo_mls_originator = ($request->agent_info != null)?$request->agent_info['hmdo_mls_originator'][1]:NULL;
										        		$agent_info->hmdo_agent_name = ($request->agent_info != null)?$request->agent_info['hmdo_agent_name'][1]:NULL;
										        		$agent_info->hmdo_agent_title = ($request->agent_info != null)?$request->agent_info['hmdo_agent_title'][1]:NULL;
										        		$agent_info->hmdo_agent_photo_url = ($request->agent_info != null)?$request->agent_info['hmdo_agent_photo_url'][1]:NULL;
										        		$agent_info->hmdo_agent_email = ($request->agent_info != null)?$request->agent_info['hmdo_agent_email'][1]:NULL;
										        		$agent_info->hmdo_office_main_phone = ($request->agent_info != null)?$request->agent_info['hmdo_office_main_phone'][1]:NULL;
										        		$agent_info->hmdo_office_direct_phone = ($request->agent_info != null)?$request->agent_info['hmdo_office_direct_phone'][1]:NULL;
										        		$agent_info->hmdo_agent_mobile_phone = ($request->agent_info != null)?$request->agent_info['hmdo_agent_mobile_phone'][1]:NULL;
										        		$agent_info->hmdo_agent_skills = ($request->agent_info != null)?$request->agent_info['hmdo_agent_skills'][1]:NULL;
										        		$agent_info->hmdo_office_id = ($request->agent_info != null)?$request->agent_info['hmdo_office_id'][1]:NULL;
										        		$agent_info->hmdo_office_name = ($request->agent_info != null)?$request->agent_info['hmdo_office_name'][1]:NULL;
										        		$agent_info->hmdo_office_photo = ($request->agent_info != null)?$request->agent_info['hmdo_office_photo'][1]:NULL;
										        		$agent_info->hmdo_office_street = ($request->agent_info != null)?$request->agent_info['hmdo_office_street'][1]:NULL;
										        		$agent_info->hmdo_office_city = ($request->agent_info != null)?$request->agent_info['hmdo_office_city'][1]:NULL;
										        		$agent_info->hmdo_office_zipcode = ($request->agent_info != null)?$request->agent_info['hmdo_office_zipcode'][1]:NULL;
										        		$agent_info->hmdo_office_state = ($request->agent_info != null)?$request->agent_info['hmdo_office_state'][1]:NULL;
										        		$agent_info->hmdo_office_phone = ($request->agent_info != null)?$request->agent_info['hmdo_office_phone'][1]:NULL;
										        		$agent_info->hmdo_office_website = ($request->agent_info != null)?$request->agent_info['hmdo_office_website'][1]:NULL;
										        		$agent_info->hmdo_agent_website = ($request->agent_info != null)?$request->agent_info['hmdo_agent_website'][1]:NULL;
										        		$agent_info->save();

								            		$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
										            Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

										            $this->configSMTP();
										            $data = [
										                'name'=>'',
										                'verification_token'=>$verification_token,
										                'email'=>$agent->email,
										                'url'=>$request->url
										            ];
										            Mail::to($agent->email)->send(new SignupMail($data));

										            $property_varification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );

										            /*$verification_data = [
																		'name'=>($request->agent_info != null)?$request->agent_info['hmdo_agent_name'][1]:'',
																		'owner_name'=>$user->first_name.' '.$user->last_name,
										                'property_id'=>$property->uuid,
										                'property_link'=>$homendo->hmdo_mls_url,
										                'site_url'=>env('APP_URL'),
										                'token'=>$property_varification_token
										            ];
										            Mail::to($agent->email)->send(new PropertyVerificationMail($verification_data));

											          $property_varification = new PropertyVerification;
											          $property_varification->property_id = $property->uuid;
											          $property_varification->agent_id = $agent->uuid;
											          $property_varification->user_id = $request->user_id;
											          $property_varification->token = $property_varification_token;
											          $property_varification->send_time = date('Y-m-d h:i:s');
											          $result = $property_varification->save();*/

											          if ($verify_status == 'NO') {
										            		$update_token = PropertyOwners::where(['user_id'=>$request->user_id, 'property_id'=>$property->uuid])->update(['verification_token'=>$property_varification_token]);
												            if ($update_token) {
												            		$this->configSMTP();
																				$verification_data = [
																						'owner_name'=>$user->first_name.' '.$user->last_name,
																						'agent_name'=>($request->agent_info != null)?$request->agent_info['hmdo_agent_name'][1]:'',
														                'user_id'=>base64_encode($request->user_id),
														                'property_id'=>base64_encode($property->uuid),
														                'token'=>base64_encode($property_varification_token)
														            ];
														            try{
																	          Mail::to($agent->uuid)->send(new OwnerVerificationMail($verification_data));

																	          $property_varification = new PropertyVerification;
																	          $property_varification->property_id = $property->uuid;
																	          $property_varification->agent_id = $agent->uuid;
																	          $property_varification->user_id = $request->user_id;
																	          $property_varification->token = $property_varification_token;
																	          $property_varification->send_time = date('Y-m-d h:i:s');
																	          $result = $property_varification->save();
																	      }catch(\Exception $e){

																	      }
												            }
										            }

											          $check_agent = PropertyAgents::where(['property_id'=>$property->uuid, 'agent_id'=>$agent->uuid, 'agent_type'=>'seller'])->first();
				                        
				                        if (empty($check_agent)) {
				                            $property_agent = new PropertyAgents;
				                            $property_agent->property_id = $property->uuid;
				                            $property_agent->property_mls_id = $property->mls_id;
				                            $property_agent->property_originator = $property->mls_name;
				                            $property_agent->agent_id = $agent->uuid;
				                            $property_agent->agent_type = 'seller';
				                            $property_agent->save();
				                        }
								            }
							      		}else{
							      				$this->configSMTP();
							      				$property_varification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );

							      				/*$verification_data = [
																'name'=>$checkAgent->first_name.' '.$checkAgent->last_name,
																'owner_name'=>$user->first_name.' '.$user->last_name,
								                'property_id'=>$property->uuid,
								                'property_link'=>$homendo->hmdo_mls_url,
								                'site_url'=>env('APP_URL'),
								                'token'=>$property_varification_token
								            ];
								            Mail::to($checkAgent->email)->send(new PropertyVerificationMail($verification_data));

									          $property_varification = new PropertyVerification;
									          $property_varification->property_id = $property->uuid;
									          $property_varification->agent_id = $checkAgent->uuid;
									          $property_varification->user_id = $request->user_id;
									          $property_varification->token = $property_varification_token;
									          $property_varification->send_time = date('Y-m-d h:i:s');
									          $result = $property_varification->save();*/

									          if ($verify_status == 'NO') {
								            		$update_token = PropertyOwners::where(['user_id'=>$request->user_id, 'property_id'=>$property->uuid])->update(['verification_token'=>$property_varification_token]);
										            if ($update_token) {
										            		$this->configSMTP();
																		$verification_data = [
																				'owner_name'=>$user->first_name.' '.$user->last_name,
																				'agent_name'=>$checkAgent->first_name.' '.$checkAgent->last_name,
												                'user_id'=>base64_encode($request->user_id),
												                'property_id'=>base64_encode($property->uuid),
												                'token'=>base64_encode($property_varification_token)
												            ];
												            try{
															          Mail::to($checkAgent->email)->send(new OwnerVerificationMail($verification_data));

															          $property_varification = new PropertyVerification;
															          $property_varification->property_id = $property->uuid;
															          $property_varification->agent_id = $checkAgent->uuid;
															          $property_varification->user_id = $request->user_id;
															          $property_varification->token = $property_varification_token;
															          $property_varification->send_time = date('Y-m-d h:i:s');
															          $result = $property_varification->save();
															      }catch(\Exception $e){

															      }
										            }
								            }

									          $check_agent = PropertyAgents::where(['property_id'=>$property->uuid, 'agent_id'=>$checkAgent->uuid, 'agent_type'=>'seller'])->first();
				                        
		                        if (empty($check_agent)) {
		                            $property_agent = new PropertyAgents;
		                            $property_agent->property_id = $property->uuid;
		                            $property_agent->property_mls_id = $property->mls_id;
		                            $property_agent->property_originator = $property->mls_name;
		                            $property_agent->agent_id = $checkAgent->uuid;
		                            $property_agent->agent_type = 'seller';
		                            $property_agent->save();
		                        }
							      		}

								      	if ($verify_status == 'PV') {
								      			$time = strtotime(Carbon::now());
								      			$setup_uuid = "show".$time.rand(10,99)*rand(10,99);

											      $setup = new PropertyShowingSetup;
											      $setup->uuid = $setup_uuid;
											      $setup->property_id = $property->uuid;
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

								      	return $this->sendResponse("Property added successfully!");
					      		}	
					    	}else{
						    		$time = strtotime(Carbon::now());
							    	$uuid = "prty".$time.rand(10,99)*rand(10,99);
						      	$property = new Properties;
						      	$property->uuid = $uuid;
						      	$property->mls_id = $mls_id;
						      	$property->mls_name = $mls_name;
						      	$property->data = json_encode($request->data);
						      	$property->verified = 'NO';
						      	$property->price = str_replace(array('$', ','), '', $hmdo_mls_price);
						      	$property->last_update = date('Y-m-d H:i:s');
						      	$add_property = $property->save();

						      	$valuecheck = new PropertyValuecheck;
						      	$valuecheck->uuid = "vlck".$time.rand(10,99)*rand(10,99);
						      	$valuecheck->property_id = $uuid;
						      	$valuecheck->vs_listed = $vs_listed;
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
						      	$zillow->z_listed = $z_listed;
						      	$zillow->z_zpid = $request->data['property'][1][1]['z_zpid'][1];
						      	$zillow->z_sale_amount = $request->data['property'][1][1]['z_sale_amount'][1];
						      	$zillow->z_sale_lowrange = $request->data['property'][1][1]['z_sale_lowrange'][1];
						      	$zillow->z_sale_highrange = $request->data['property'][1][1]['z_sale_highrange'][1];
						      	$zillow->z_sale_lastupdated = $request->data['property'][1][1]['z_sale_lastupdated'][1];
						      	$zillow->z_rental_amount = $request->data['property'][1][1]['z_rental_amount'][1];
						      	$zillow->z_rental_lowrange = $request->data['property'][1][1]['z_rental_lowrange'][1];
						      	$zillow->z_rental_highrange = $request->data['property'][1][1]['z_rental_highrange'][1];
						      	$zillow->z_rental_lastupdated = $request->data['property'][1][1]['z_rental_lastupdated'][1];
						      	if (is_array($request->data['property'][1][1]['z_prop_url'][1]) == true) {
						  					$zillow->z_prop_url = $request->data['property'][1][1]['z_prop_url'][1]['changingThisBreaksApplicationSecurity'];
						  			}else{
						  					$zillow->z_prop_url = $request->data['property'][1][1]['z_prop_url'][1];
						  			}
						      	$add_zillow = $zillow->save();

						      	$homendo = new PropertyHomendo;
						      	$homendo->uuid = "hmdo".$time.rand(10,99)*rand(10,99);
						      	$homendo->property_id = $uuid;
						      	$homendo->hmdo_listed = $hmdo_listed;
						      	$homendo->hmdo_lastupdated = $request->data['property'][2][1]['hmdo_lastupdated'][1];
						      	$homendo->hmdo_mls_agent_email = $request->data['property'][2][1]['hmdo_mls_agent_email'][1];
						      	$homendo->hmdo_mls_agentid = $request->data['property'][2][1]['hmdo_mls_agentid'][1];
						      	$homendo->hmdo_mls_description = $request->data['property'][2][1]['hmdo_mls_description'][1];
						      	if (is_array($request->data['property'][2][1]['hmdo_mls_id'][1]) == true) {
						      			$homendo->hmdo_mls_id = $request->data['property'][2][1]['hmdo_mls_id'][1][0];
						      	}else{
						      			$homendo->hmdo_mls_id = $request->data['property'][2][1]['hmdo_mls_id'][1];
						      	}
						      	if (is_array($request->data['property'][2][1]['hmdo_mls_originator'][1]) == true) {
						      			$homendo->hmdo_mls_originator = $request->data['property'][2][1]['hmdo_mls_originator'][1][0];
						      	}else{
						      			$homendo->hmdo_mls_originator = $request->data['property'][2][1]['hmdo_mls_originator'][1];
						      	}
						      	if (is_array($request->data['property'][2][1]['hmdo_mls_proptype'][1]) == true) {
						      			$homendo->hmdo_mls_proptype = $request->data['property'][2][1]['hmdo_mls_proptype'][1][0];
						      	}else{
						      			$homendo->hmdo_mls_proptype = $request->data['property'][2][1]['hmdo_mls_proptype'][1];
						      	}
						      	$homendo->hmdo_mls_propname = $request->data['property'][2][1]['hmdo_mls_propname'][1];
						      	if (is_array($request->data['property'][2][1]['hmdo_mls_status'][1]) == true) {
						      			$homendo->hmdo_mls_status = $request->data['property'][2][1]['hmdo_mls_status'][1][0];
						      	}else{
						      			$homendo->hmdo_mls_status = $request->data['property'][2][1]['hmdo_mls_status'][1];
						      	}
						      	$homendo->hmdo_mls_price = $request->data['property'][2][1]['hmdo_mls_price'][1];
						      	if (is_array($request->data['property'][2][1]['hmdo_mls_url'][1]) == true) {
						      			$homendo->hmdo_mls_url = $request->data['property'][2][1]['hmdo_mls_url'][1]['changingThisBreaksApplicationSecurity'];
						      	}else{
						      			$homendo->hmdo_mls_url = $request->data['property'][2][1]['hmdo_mls_url'][1];
						      	}
						      	if (is_array($request->data['property'][2][1]['hmdo_mls_thumbnail'][1]) == true) {
						      			$homendo->hmdo_mls_thumbnail = $request->data['property'][2][1]['hmdo_mls_thumbnail'][1][0];
						      	}else{
						      			$homendo->hmdo_mls_thumbnail = $request->data['property'][2][1]['hmdo_mls_thumbnail'][1];
						      	}
						      	$homendo->hmdo_mls_officeid = $request->data['property'][2][1]['hmdo_mls_officeid'][1];
						      	$add_homendo = $homendo->save();

						      	$verify_status = 'NO';
						      	if ($request->data['property'][0][1]['vs_ownername'][1] != null || $request->data['property'][0][1]['vs_ownername'][1] != '') {
						      			if (strpos($request->data['property'][0][1]['vs_ownername'][1], $user->last_name) == true) {
								      			$verify_status = 'PV';
								      	}else{
								      			$verify_status = 'NO';
						      			}
						      	}

						      	if ($verify_status == 'NO') {
						      			if ($request->data['property'][0][1]['vs_ownername2'][1] != null || $request->data['property'][0][1]['vs_ownername2'][1] != '') {
						      					if (strpos($request->data['property'][0][1]['vs_ownername2'][1], $user->last_name) == true) {
								      					$verify_status = 'PV';
								      			}else{
								      					$verify_status = 'NO';
								      			}
								      	}
						      	}

						      	$checkOwner = PropertyOwners::where(['property_id'=>$property->uuid, 'user_id'=>$request->user_id])->first();
						      	if ($checkOwner == null) {
						      			$owner = new PropertyOwners;
								      	$owner->property_id = $property->uuid;
								      	$owner->user_id = $request->user_id;
								      	$owner->type = 'main_owner';
								      	$owner->verify_status = $verify_status;
								      	$property_owner = $owner->save();
						      	}

						      	if (empty($checkAgent) || $checkAgent == null) {
							      		$time = strtotime(Carbon::now());
						            $agent_uuid = "usr".$time.rand(10,99)*rand(10,99);

						            $agent = new Users;
						            $agent->uuid = $agent_uuid;
						            $agent->email = $request->data['property'][2][1]['hmdo_mls_agent_email'][1];
						            $agent->role = "AGENT";
						            $agent->mls_id = $request->data['property'][2][1]['hmdo_mls_agentid'][1];
						            $agent->mls_name = $mls_name;
						            $agent->phone_verified = "NO";
						            $agent->email_verified = "NO";
						            $agent->image = env("APP_URL")."public/user-images/default.png";
						            $result = $agent->save();
						            if ($result) {
							            	$agent_info = new AgentInfo;
								        		$agent_info->agent_id = $agent_uuid;
								        		$agent_info->hmdo_lastupdated = ($request->agent_info != null)?$request->agent_info['hmdo_lastupdated'][1]:NULL;
								        		$agent_info->hmdo_mls_originator = ($request->agent_info != null)?$request->agent_info['hmdo_mls_originator'][1]:NULL;
								        		$agent_info->hmdo_agent_name = ($request->agent_info != null)?$request->agent_info['hmdo_agent_name'][1]:NULL;
								        		$agent_info->hmdo_agent_title = ($request->agent_info != null)?$request->agent_info['hmdo_agent_title'][1]:NULL;
								        		$agent_info->hmdo_agent_photo_url = ($request->agent_info != null)?$request->agent_info['hmdo_agent_photo_url'][1]:NULL;
								        		$agent_info->hmdo_agent_email = ($request->agent_info != null)?$request->agent_info['hmdo_agent_email'][1]:NULL;
								        		$agent_info->hmdo_office_main_phone = ($request->agent_info != null)?$request->agent_info['hmdo_office_main_phone'][1]:NULL;
								        		$agent_info->hmdo_office_direct_phone = ($request->agent_info != null)?$request->agent_info['hmdo_office_direct_phone'][1]:NULL;
								        		$agent_info->hmdo_agent_mobile_phone = ($request->agent_info != null)?$request->agent_info['hmdo_agent_mobile_phone'][1]:NULL;
								        		$agent_info->hmdo_agent_skills = ($request->agent_info != null)?$request->agent_info['hmdo_agent_skills'][1]:NULL;
								        		$agent_info->hmdo_office_id = ($request->agent_info != null)?$request->agent_info['hmdo_office_id'][1]:NULL;
								        		$agent_info->hmdo_office_name = ($request->agent_info != null)?$request->agent_info['hmdo_office_name'][1]:NULL;
								        		$agent_info->hmdo_office_photo = ($request->agent_info != null)?$request->agent_info['hmdo_office_photo'][1]:NULL;
								        		$agent_info->hmdo_office_street = ($request->agent_info != null)?$request->agent_info['hmdo_office_street'][1]:NULL;
								        		$agent_info->hmdo_office_city = ($request->agent_info != null)?$request->agent_info['hmdo_office_city'][1]:NULL;
								        		$agent_info->hmdo_office_zipcode = ($request->agent_info != null)?$request->agent_info['hmdo_office_zipcode'][1]:NULL;
								        		$agent_info->hmdo_office_state = ($request->agent_info != null)?$request->agent_info['hmdo_office_state'][1]:NULL;
								        		$agent_info->hmdo_office_phone = ($request->agent_info != null)?$request->agent_info['hmdo_office_phone'][1]:NULL;
								        		$agent_info->hmdo_office_website = ($request->agent_info != null)?$request->agent_info['hmdo_office_website'][1]:NULL;
								        		$agent_info->hmdo_agent_website = ($request->agent_info != null)?$request->agent_info['hmdo_agent_website'][1]:NULL;
								        		$agent_info->save();

						            		$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
								            Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

								            $this->configSMTP();
								            $data = [
								                'name'=>'',
								                'verification_token'=>$verification_token,
								                'email'=>$agent->email,
								                'url'=>$request->url
								            ];
								            Mail::to($agent->email)->send(new SignupMail($data));

								            $property_varification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );

								            /*$verification_data = [
																'name'=>($request->agent_info != null)?$request->agent_info['hmdo_agent_name'][1]:'',
																'owner_name'=>$user->first_name.' '.$user->last_name,
								                'property_id'=>$property->uuid,
								                'property_link'=>$homendo->hmdo_mls_url,
								                'site_url'=>env('APP_URL'),
								                'token'=>$property_varification_token
								            ];
								            Mail::to($agent->email)->send(new PropertyVerificationMail($verification_data));

									          $property_varification = new PropertyVerification;
									          $property_varification->property_id = $property->uuid;
									          $property_varification->agent_id = $agent->uuid;
									          $property_varification->user_id = $request->user_id;
									          $property_varification->token = $property_varification_token;
									          $property_varification->send_time = date('Y-m-d h:i:s');
									          $result = $property_varification->save();*/

									          if ($verify_status == 'NO') {
								            		$update_token = PropertyOwners::where(['user_id'=>$request->user_id, 'property_id'=>$property->uuid])->update(['verification_token'=>$property_varification_token]);
										            if ($update_token) {
										            		$this->configSMTP();
																		$verification_data = [
																				'owner_name'=>$user->first_name.' '.$user->last_name,
																				'agent_name'=>($request->agent_info != null)?$request->agent_info['hmdo_agent_name'][1]:'',
												                'user_id'=>base64_encode($request->user_id),
												                'property_id'=>base64_encode($property->uuid),
												                'token'=>base64_encode($property_varification_token)
												            ];
												            try{
															          Mail::to($agent->email)->send(new OwnerVerificationMail($verification_data));

															          $property_varification = new PropertyVerification;
															          $property_varification->property_id = $property->uuid;
															          $property_varification->agent_id = $agent->uuid;
															          $property_varification->user_id = $request->user_id;
															          $property_varification->token = $property_varification_token;
															          $property_varification->send_time = date('Y-m-d h:i:s');
															          $result = $property_varification->save();
															      }catch(\Exception $e){

															      }
										            }
								            }

									          $check_agent = PropertyAgents::where(['property_id'=>$property->uuid, 'agent_id'=>$agent->uuid, 'agent_type'=>'seller'])->first();
				                        
		                        if (empty($check_agent)) {
		                            $property_agent = new PropertyAgents;
		                            $property_agent->property_id = $property->uuid;
		                            $property_agent->property_mls_id = $property->mls_id;
		                            $property_agent->property_originator = $property->mls_name;
		                            $property_agent->agent_id = $agent->uuid;
		                            $property_agent->agent_type = 'seller';
		                            $property_agent->save();
		                        }
						            }
					      		}else{
					      				$this->configSMTP();
					      				$property_varification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );

					      				/*$verification_data = [
														'name'=>$checkAgent->first_name.' '.$checkAgent->last_name,
														'owner_name'=>$user->first_name.' '.$user->last_name,
						                'property_id'=>$property->uuid,
						                'property_link'=>$homendo->hmdo_mls_url,
						                'site_url'=>env('APP_URL'),
						                'token'=>$property_varification_token
						            ];
						            Mail::to($checkAgent->email)->send(new PropertyVerificationMail($verification_data));

							          $property_varification = new PropertyVerification;
							          $property_varification->property_id = $property->uuid;
							          $property_varification->agent_id = $checkAgent->uuid;
							          $property_varification->user_id = $request->user_id;
							          $property_varification->token = $property_varification_token;
							          $property_varification->send_time = date('Y-m-d h:i:s');
							          $result = $property_varification->save();*/

							          if ($verify_status == 'NO') {
						            		$update_token = PropertyOwners::where(['user_id'=>$request->user_id, 'property_id'=>$property->uuid])->update(['verification_token'=>$property_varification_token]);
								            if ($update_token) {
								            		$this->configSMTP();
																$verification_data = [
																		'owner_name'=>$user->first_name.' '.$user->last_name,
																		'agent_name'=>$checkAgent->first_name.' '.$checkAgent->last_name,
										                'user_id'=>base64_encode($request->user_id),
										                'property_id'=>base64_encode($property->uuid),
										                'token'=>base64_encode($property_varification_token)
										            ];
										            try{
													          Mail::to($checkAgent->email)->send(new OwnerVerificationMail($verification_data));

													          $property_varification = new PropertyVerification;
													          $property_varification->property_id = $property->uuid;
													          $property_varification->agent_id = $checkAgent->uuid;
													          $property_varification->user_id = $request->user_id;
													          $property_varification->token = $property_varification_token;
													          $property_varification->send_time = date('Y-m-d h:i:s');
													          $result = $property_varification->save();
													      }catch(\Exception $e){

													      }
								            }
						            }

							          $check_agent = PropertyAgents::where(['property_id'=>$property->uuid, 'agent_id'=>$checkAgent->uuid, 'agent_type'=>'seller'])->first();
				                        
                        if (empty($check_agent)) {
                            $property_agent = new PropertyAgents;
                            $property_agent->property_id = $property->uuid;
                            $property_agent->property_mls_id = $property->mls_id;
                            $property_agent->property_originator = $property->mls_name;
                            $property_agent->agent_id = $checkAgent->uuid;
                            $property_agent->agent_type = 'seller';
                            $property_agent->save();
                        }
					      		}

						      	if ($verify_status == 'PV') {
						      			$time = strtotime(Carbon::now());
						      			$setup_uuid = "show".$time.rand(10,99)*rand(10,99);

									      $setup = new PropertyShowingSetup;
									      $setup->uuid = $setup_uuid;
									      $setup->property_id = $property->uuid;
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

								    return $this->sendResponse("Property added successfully!");	
						    }
				  	}else{
				  			//return $this->sendResponse("Sorry this property can not be added to your account yet.", 200, false);
				  			$time = strtotime(Carbon::now());
					    	$uuid = "prty".$time.rand(10,99)*rand(10,99);
				      	$property = new Properties;
				      	$property->uuid = $uuid;
				      	$property->mls_id = $mls_id;
				      	$property->mls_name = $mls_name;
				      	$property->data = json_encode($request->data);
				      	$property->verified = 'NO';
				      	$property->price = str_replace(array('$', ','), '', $hmdo_mls_price);
				      	$property->last_update = date('Y-m-d H:i:s');
				      	$add_property = $property->save();

				      	$valuecheck = new PropertyValuecheck;
				      	$valuecheck->uuid = "vlck".$time.rand(10,99)*rand(10,99);
				      	$valuecheck->property_id = $uuid;
				      	$valuecheck->vs_listed = $vs_listed;
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
				      	$zillow->z_listed = $z_listed;
				      	$zillow->z_zpid = $request->data['property'][1][1]['z_zpid'][1];
				      	$zillow->z_sale_amount = $request->data['property'][1][1]['z_sale_amount'][1];
				      	$zillow->z_sale_lowrange = $request->data['property'][1][1]['z_sale_lowrange'][1];
				      	$zillow->z_sale_highrange = $request->data['property'][1][1]['z_sale_highrange'][1];
				      	$zillow->z_sale_lastupdated = $request->data['property'][1][1]['z_sale_lastupdated'][1];
				      	$zillow->z_rental_amount = $request->data['property'][1][1]['z_rental_amount'][1];
				      	$zillow->z_rental_lowrange = $request->data['property'][1][1]['z_rental_lowrange'][1];
				      	$zillow->z_rental_highrange = $request->data['property'][1][1]['z_rental_highrange'][1];
				      	$zillow->z_rental_lastupdated = $request->data['property'][1][1]['z_rental_lastupdated'][1];
				      	if (is_array($request->data['property'][1][1]['z_prop_url'][1]) == true) {
				  					$zillow->z_prop_url = $request->data['property'][1][1]['z_prop_url'][1]['changingThisBreaksApplicationSecurity'];
				  			}else{
				  					$zillow->z_prop_url = $request->data['property'][1][1]['z_prop_url'][1];
				  			}
				      	$add_zillow = $zillow->save();

				      	$homendo = new PropertyHomendo;
				      	$homendo->uuid = "hmdo".$time.rand(10,99)*rand(10,99);
				      	$homendo->property_id = $uuid;
				      	$homendo->hmdo_listed = $hmdo_listed;
				      	$homendo->hmdo_lastupdated = $request->data['property'][2][1]['hmdo_lastupdated'][1];
				      	$homendo->hmdo_mls_agent_email = $request->data['property'][2][1]['hmdo_mls_agent_email'][1];
				      	$homendo->hmdo_mls_agentid = $request->data['property'][2][1]['hmdo_mls_agentid'][1];
				      	$homendo->hmdo_mls_description = $request->data['property'][2][1]['hmdo_mls_description'][1];
				      	if (is_array($request->data['property'][2][1]['hmdo_mls_id'][1]) == true) {
				      			$homendo->hmdo_mls_id = $request->data['property'][2][1]['hmdo_mls_id'][1][0];
				      	}else{
				      			$homendo->hmdo_mls_id = $request->data['property'][2][1]['hmdo_mls_id'][1];
				      	}
				      	if (is_array($request->data['property'][2][1]['hmdo_mls_originator'][1]) == true) {
				      			$homendo->hmdo_mls_originator = $request->data['property'][2][1]['hmdo_mls_originator'][1][0];
				      	}else{
				      			$homendo->hmdo_mls_originator = $request->data['property'][2][1]['hmdo_mls_originator'][1];
				      	}
				      	if (is_array($request->data['property'][2][1]['hmdo_mls_proptype'][1]) == true) {
				      			$homendo->hmdo_mls_proptype = $request->data['property'][2][1]['hmdo_mls_proptype'][1][0];
				      	}else{
				      			$homendo->hmdo_mls_proptype = $request->data['property'][2][1]['hmdo_mls_proptype'][1];
				      	}
				      	$homendo->hmdo_mls_propname = $request->data['property'][2][1]['hmdo_mls_propname'][1];
				      	if (is_array($request->data['property'][2][1]['hmdo_mls_status'][1]) == true) {
				      			$homendo->hmdo_mls_status = $request->data['property'][2][1]['hmdo_mls_status'][1][0];
				      	}else{
				      			$homendo->hmdo_mls_status = $request->data['property'][2][1]['hmdo_mls_status'][1];
				      	}
				      	$homendo->hmdo_mls_price = $request->data['property'][2][1]['hmdo_mls_price'][1];
				      	if (is_array($request->data['property'][2][1]['hmdo_mls_url'][1]) == true) {
				      			$homendo->hmdo_mls_url = $request->data['property'][2][1]['hmdo_mls_url'][1]['changingThisBreaksApplicationSecurity'];
				      	}else{
				      			$homendo->hmdo_mls_url = $request->data['property'][2][1]['hmdo_mls_url'][1];
				      	}
				      	if (is_array($request->data['property'][2][1]['hmdo_mls_thumbnail'][1]) == true) {
				      			$homendo->hmdo_mls_thumbnail = $request->data['property'][2][1]['hmdo_mls_thumbnail'][1][0];
				      	}else{
				      			$homendo->hmdo_mls_thumbnail = $request->data['property'][2][1]['hmdo_mls_thumbnail'][1];
				      	}
				      	$homendo->hmdo_mls_officeid = $request->data['property'][2][1]['hmdo_mls_officeid'][1];
				      	$add_homendo = $homendo->save();

				      	$checkOwner = PropertyOwners::where(['property_id'=>$property->uuid, 'user_id'=>$request->user_id])->first();
				      	if ($checkOwner == null) {
				      			$owner = new PropertyOwners;
						      	$owner->property_id = $property->uuid;
						      	$owner->user_id = $request->user_id;
						      	$owner->type = 'main_owner';
						      	$owner->verify_status = 'NO';
						      	$property_owner = $owner->save();
						      	
						      	if ($property_owner) {
						      			return $this->sendResponse("Property added successfully!");
						      	}else{
						      			return $this->sendResponse("Sorry, Something went wrong!", 200, false);
						      	}
				      	}

				      	return $this->sendResponse("Property added successfully!");
				  	}
		  	}else{
		  			return $this->sendResponse("Sorry this property can not be added to your account yet.", 200, false);
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
	      if ($property != null) {
	      		$checkProperty = true;
	      }else{
	      		$checkProperty = false;
	      }

	      $property['selected_agent'] = PropertyAgents::with('agent')->where(['property_id'=>$request->property_id, 'agent_type'=>'seller'])->first();
	      $property['sellers'] = PropertyOwners::with('User')->where('property_id', $request->property_id)->get();

	      if ($checkProperty) {
	      		return $this->sendResponse($property);
	      }else{
	      		return $this->sendResponse("Sorry, Property not found!", 200, false);
	      }
		}

		public function userProperties(Request $request){
				$this->validate($request, [
	      		'user_id' => 'required'
	      ]);
				
				$all_selling_properties = [];
				$all_buying_properties = [];

	      $property_ids = PropertyOwners::where('user_id', $request->user_id)->pluck('property_id')->toArray();
	      if (sizeof($property_ids) > 0) {
	      		$properties = Properties::with('Verification', 'Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->get();

	      		foreach ($properties as $property) {
								$last_verification = PropertyVerification::where('property_id', $property->uuid)->orderBy('send_time', 'desc')->first();
								if(!empty($last_verification)){
										$start_date = strtotime($last_verification->send_time);
										$end_date = strtotime("+7 day", $start_date);
										if(strtotime(date('Y-m-d H:i:s')) > strtotime(date('Y-m-d H:i:s', $end_date))){
												$property->can_verification_send = true;
										}else{
												$property->can_verification_send = false;
										}
								}
						
		      			$showings = PropertyBookingSchedule::where('property_id', $property->uuid)->get();

		      			$selling_properties = PropertyOwners::with('User')->where('property_id', $property->uuid)->get();
		      			$agent_property = PropertyAgents::where(['property_id'=>$property->uuid, 'agent_type'=>'seller'])->first();
		      			if (!empty($agent_property)) {
		      					if ($agent_property->agent_id != null || $agent_property->agent_id != '') {
				      					$agent = Users::where('uuid', $agent_property->agent_id)->first();
				      					$property['agent'] = $agent;
				      			}else{
				      					$property['agent'] = null;
				      			}
		      			}else{
		      					$property['agent'] = null;
		      			}
				      	$verify_ownership = PropertyOwners::where(['property_id'=>$property->uuid, 'user_id'=>$request->user_id])->first();
				      	$property['verify_status'] = $verify_ownership->verify_status;
		      			$property['owners'] = $selling_properties;
		      			$property['showings'] = $showings;
		      			$all_selling_properties[] = $property;
	      		}
	      }

	      $buying_property_ids = PropertyBuyers::where('buyer_id', $request->user_id)->pluck('property_id')->toArray();
	      if (sizeof($buying_property_ids) > 0) {
	      		$buying_properties = Properties::with('Verification', 'Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $buying_property_ids)->get();

	      		foreach ($buying_properties as $buying_property) {
								$last_verification = PropertyVerification::where('property_id', $buying_property->uuid)->orderBy('send_time', 'desc')->first();
								if(!empty($last_verification)){
										$start_date = strtotime($last_verification->send_time);
										$end_date = strtotime("+7 day", $start_date);
										if(strtotime(date('Y-m-d H:i:s')) > strtotime(date('Y-m-d H:i:s', $end_date))){
												$buying_property->can_verification_send = true;
										}else{
												$buying_property->can_verification_send = false;
										}
								}
						
		      			$showings = PropertyBookingSchedule::where('property_id', $buying_property->uuid)->get();

		      			$all_properties = PropertyOwners::with('User')->where('property_id', $buying_property->uuid)->get();
		      			$buying_property['owners'] = $all_properties;
		      			$buying_property['showings'] = $showings;
		      			$all_buying_properties[] = $buying_property;
	      		}
	      }

	      $response = array('selling_property'=>$all_selling_properties, 'buying_property'=>$all_buying_properties);
	      return $this->sendResponse($response);
		}

		public function agentClientsProperties(Request $request){
				$this->validate($request, [
						'agent_id' => 'required',
						'user_id' => 'required'
				]);
				
				$all_selling_properties = [];
				$all_buying_properties = [];

				$owner_property_ids = PropertyOwners::where('user_id', $request->user_id)->pluck('property_id')->toArray();
				$buyer_property_ids = PropertyBuyers::where('buyer_id', $request->user_id)->pluck('property_id')->toArray();
				$property_ids = array_merge($owner_property_ids, $buyer_property_ids);
				if (sizeof($property_ids) > 0) {
						$agent_properties = PropertyAgents::where('agent_id', $request->agent_id)->with('property.Valuecheck','property.Zillow','property.Homendo')->whereIn('property_id', $property_ids)->get();
						foreach ($agent_properties as $agent_property) {
								$user = Users::where('uuid', $request->user_id)->first();

                $agent_property['seller'] = $user;

								if ($agent_property->agent_type == 'seller') {
										$verify_ownership = PropertyOwners::where(['property_id'=>$agent_property->property_id, 'user_id'=>$request->user_id])->first();
										$agent_property->property['verify_status'] = $verify_ownership->verify_status;
										$all_selling_properties[] = $agent_property;
								}else{
										$all_buying_properties[] = $agent_property;
								}
						}

						$response = array('selling_property'=>$all_selling_properties, 'buying_property'=>$all_buying_properties);
			      return $this->sendResponse($response);
				}else{
						return $this->sendResponse("Sorry, Properties not found!", 200, false);
				}	
		}

		public function assignAgent(Request $request){
				$this->validate($request, [
	      		'property_id' => 'required',
	      		'agent_id' => 'required',
	      		'agent_type' => 'required|in:seller,buyer',
	      ]);

				$property = Properties::where('uuid', $request->property_id)->first();
				$agent = Users::where('uuid', $request->agent_id)->first();
				$homendo = PropertyHomendo::where('property_id', $request->property_id)->first();

				if (!empty($property)) {
						$property_agent = new PropertyAgents;
						$property_agent->property_id = $request->property_id;
						$property_agent->agent_id = $request->agent_id;
						$property_agent->agent_type = $request->agent_type;
						$result = $property_agent->save();
						if ($result) {
								$this->configSMTP();
								$data = [
										'name'=>$agent->first_name.' '.$agent->last_name, 
		                'property_id'=>$request->property_id,
		                'property_name'=>$homendo->hmdo_mls_propname
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
	      		'seller_id' => 'required',
	      		'agent_id' => 'required',
	      		'agent_type' => 'required|in:seller,buyer',
	      ]);

	      $result = PropertyAgents::where(['property_id'=>$request->property_id, 'agent_id'=>$request->agent_id, 'agent_type'=>$request->agent_type])->delete();

	      $checkVerification = PropertyOwners::where(['property_id'=>$request->property_id, 'user_id'=>$request->seller_id])->first();
	      
	      if ($checkVerification->verify_status !== 'PV') {
	      		PropertyOwners::where(['property_id'=>$request->property_id, 'user_id'=>$request->seller_id])->update(['verify_status'=>'NO']);
	      }
	      
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

			          //PropertyVerification::where('property_id', $request->property_id)->delete();

			          $property_varification = new PropertyVerification;
			          $property_varification->property_id = $request->property_id;
			          $property_varification->agent_id = $request->agent_id;
			          $property_varification->user_id = $request->user_id;
			          $property_varification->token = $verification_token;
			          $property_varification->send_time = date('Y-m-d h:i:s');
			          $result = $property_varification->save();

			          $property_agent = new PropertyAgents;
								$property_agent->property_id = $request->property_id;
								$property_agent->agent_id = $request->agent_id;
								//$property_agent->seller_id = $request->user_id;
								$property_agent->agent_type = 'seller';
								$result = $property_agent->save();

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
	      		'phone' => 'required',
	      		'user_id' => 'required',
	      		'property_id' => 'required',
	      		'url' => 'required'
	      ]);
				
				$user_check = Users::where('email', $request->email)->first();
				if (!empty($user_check)) {
						$checkOwner = PropertyOwners::where(['property_id'=>$request->property_id, 'user_id'=>$user_check->uuid])->first();
						if ($checkOwner == null) {
								$owner = new PropertyOwners;
					      $owner->property_id = $request->property_id;
					      $owner->user_id = $user_check->uuid;
					      $property_owner = $owner->save();
					      return $this->sendResponse("Owner added successfully!");
					  }else{
					  		return $this->sendResponse("Already added!", 200, false);
					  }
				}

	      $prop_owner = Users::where('uuid', $request->user_id)->first();
	      $property = Properties::where('uuid', $request->property_id)->first();
	      $homendo = PropertyHomendo::where('property_id', $request->property_id)->first();

				$time = strtotime(Carbon::now());
        $uuid = "usr".$time.rand(10,99)*rand(10,99);
	      $user = new Users;
        $user->uuid = $uuid;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
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
						'property_name'=>$homendo->hmdo_mls_propname
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
	          return $this->sendResponse("Owner added successfully!");
	      }catch(\Exception $e){
	          $msg = $e->getMessage();
	          return $this->sendResponse($msg, 200, false);
	      }
		}

		public function agentProperties(Request $request){
				$this->validate($request, [
	      		'agent_id' => 'required',
	      		'search' => 'nullable',
				'sorting' => 'nullable|in:date,listing_status,property_type,price',
				'sort_by' => 'required|in:ASC,DESC',
				'filter'  => 'required|in:all,hide'
	      ]);

				$search_item = $request->search;
				$sorting = $request->sorting;
				$sort_by = $request->sort_by;
				$filter  = $request->filter;
	      $property_ids = PropertyAgents::where(['agent_id'=>$request->agent_id])->pluck('property_id')->toArray();

	      if (sizeof($property_ids) > 0) {
	      		if ($sorting !== '') {
	      				if ($sorting == 'date') {
	      						$search_array = explode(' ', $search_item);
	      						if (sizeof(array_filter($search_array)) > 0) {
	      								$searched_property_ids = [];

	      								foreach (array_filter($search_array) as $search_array_item) {
														$properties = Properties::join('property_homendo', 'property_homendo.property_id', '=', 'properties.uuid')
																->join('property_valuecheck', 'property_valuecheck.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('properties.uuid', $property_ids)->where(function($query) use ($search_array_item) {
																$query->where('property_homendo.hmdo_mls_id', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_streetnumber', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_streetname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_streettype', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_city', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_zipcode', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_state', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streetnumber', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streetname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streettype', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_city', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_state', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_zipcode', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_county', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_countyname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_country', 'LIKE', '%'.$search_array_item.'%');
														})->orderBy('properties.created_at', $sort_by)->get('properties.*');

														if (sizeof($properties) > 0) {
																foreach ($properties as $property) {
																		$searched_property_ids[] = $property->uuid;
																}
														}
												}

												$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', array_unique($searched_property_ids))->orderBy('created_at', $sort_by)->get();
										}else{
												$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->orderBy('created_at',$sort_by)->get();
										}
	      				}elseif ($sorting == 'listing_status') {
	      						$search_array = explode(' ', $search_item);
	      						if (sizeof(array_filter($search_array)) > 0) {
	      								$searched_property_ids = [];

	      								foreach (array_filter($search_array) as $search_array_item) {
			      								$properties = Properties::join('property_homendo', 'property_homendo.property_id', '=', 'properties.uuid')
			      										->join('property_valuecheck', 'property_valuecheck.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('properties.uuid', $property_ids)->where(function($query) use ($search_array_item) {
																$query->where('property_homendo.hmdo_mls_id', 'LIKE', '%'.$search_array_item.'%')
																->orWhere('property_homendo.hmdo_mls_streetnumber', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_streetname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_streettype', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_city', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_zipcode', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_state', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streetnumber', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streetname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streettype', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_city', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_state', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_zipcode', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_county', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_countyname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_country', 'LIKE', '%'.$search_array_item.'%');
														})->orderBy('property_homendo.hmdo_mls_status', $sort_by)->get('properties.*');

			      								if (sizeof($properties) > 0) {
																foreach ($properties as $property) {
																		$searched_property_ids[] = $property->uuid;
																}
														}
			      						}

			      						$properties = Properties::join('property_homendo','property_homendo.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', array_unique($searched_property_ids))->orderBy('property_homendo.hmdo_mls_status',$sort_by)->get();
										}else{
												$properties = Properties::join('property_homendo', 'property_homendo.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('properties.uuid', $property_ids)->orderBy('property_homendo.hmdo_mls_status',$sort_by)->get('properties.*');
										}
	      				}elseif ($sorting == 'property_type') {
	      						$search_array = explode(' ', $search_item);
	      						if (sizeof(array_filter($search_array)) > 0) {
	      								$searched_property_ids = [];

	      								foreach (array_filter($search_array) as $search_array_item) {
			      								$properties = Properties::join('property_homendo', 'property_homendo.property_id', '=', 'properties.uuid')
			      										->join('property_valuecheck', 'property_valuecheck.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('properties.uuid', $property_ids)->where(function($query) use ($search_array_item) {
																$query->where('property_homendo.hmdo_mls_id', 'LIKE', '%'.$search_array_item.'%')
																->orWhere('property_homendo.hmdo_mls_streetnumber', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_streetname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_streettype', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_city', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_zipcode', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_state', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streetnumber', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streetname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streettype', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_city', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_state', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_zipcode', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_county', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_countyname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_country', 'LIKE', '%'.$search_array_item.'%');
														})->orderBy('property_homendo.hmdo_mls_proptype', $sort_by)->get('properties.*');

			      								if (sizeof($properties) > 0) {
																foreach ($properties as $property) {
																		$searched_property_ids[] = $property->uuid;
																}
														}
			      						}

			      						$properties = Properties::join('property_homendo', 'property_homendo.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', array_unique($searched_property_ids))->orderBy('property_homendo.hmdo_mls_proptype', $sort_by)->get();
										}else{
												$properties = Properties::join('property_homendo', 'property_homendo.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('properties.uuid', $property_ids)->orderBy('property_homendo.hmdo_mls_proptype', $sort_by)->get('properties.*');
										}
	      				}elseif ($sorting == 'price') {
	      						$search_array = explode(' ', $search_item);
	      						if (sizeof(array_filter($search_array)) > 0) {
	      								$searched_property_ids = [];

	      								foreach (array_filter($search_array) as $search_array_item) {
			      								$properties = Properties::join('property_homendo', 'property_homendo.property_id', '=', 'properties.uuid')
			      										->join('property_valuecheck', 'property_valuecheck.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('properties.uuid', $property_ids)->where(function($query) use ($search_array_item) {
																$query->where('property_homendo.hmdo_mls_id', 'LIKE', '%'.$search_array_item.'%')
																->orWhere('property_homendo.hmdo_mls_streetnumber', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_streetname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_streettype', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_city', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_zipcode', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_homendo.hmdo_mls_state', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streetnumber', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streetname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_streettype', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_city', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_state', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_zipcode', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_county', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_countyname', 'LIKE', '%'.$search_array_item.'%')
						              			->orWhere('property_valuecheck.vs_country', 'LIKE', '%'.$search_array_item.'%');
														})->orderBy('properties.price', $sort_by)->get('properties.*');

			      								if (sizeof($properties) > 0) {
																foreach ($properties as $property) {
																		$searched_property_ids[] = $property->uuid;
																}
														}
											  }

												$properties = Properties::join('property_homendo', 'property_homendo.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('properties.uuid', array_unique($searched_property_ids))->orderBy('properties.price', $sort_by)->get('properties.*');
										}else{
												$properties = Properties::join('property_homendo', 'property_homendo.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('properties.uuid', $property_ids)->orderBy('properties.price', $sort_by)->get('properties.*');
										}
	      				}
	      		}else{
	      				$search_array = explode(' ', $search_item);
	      				if (sizeof(array_filter($search_array)) > 0) {
	      						$searched_property_ids = [];

	      						foreach (array_filter($search_array) as $search_array_item) {
												$properties = Properties::join('property_homendo', 'property_homendo.property_id', '=', 'properties.uuid')
														->join('property_valuecheck', 'property_valuecheck.property_id', '=', 'properties.uuid')->with('Valuecheck', 'Zillow', 'Homendo')->whereIn('properties.uuid', $property_ids)->where(function($query) use ($search_array_item) {
														$query->where('property_homendo.hmdo_mls_id', 'LIKE', '%'.$search_array_item.'%')
														->orWhere('property_homendo.hmdo_mls_streetnumber', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_homendo.hmdo_mls_streetname', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_homendo.hmdo_mls_streettype', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_homendo.hmdo_mls_city', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_homendo.hmdo_mls_zipcode', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_homendo.hmdo_mls_state', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_valuecheck.vs_streetnumber', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_valuecheck.vs_streetname', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_valuecheck.vs_streettype', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_valuecheck.vs_city', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_valuecheck.vs_state', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_valuecheck.vs_zipcode', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_valuecheck.vs_county', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_valuecheck.vs_countyname', 'LIKE', '%'.$search_array_item.'%')
				              			->orWhere('property_valuecheck.vs_country', 'LIKE', '%'.$search_array_item.'%');
												})->get('properties.*');

												if (sizeof($properties) > 0) {
														foreach ($properties as $property) {
																$searched_property_ids[] = $property->uuid;
														}
												}
										}

										$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', array_unique($searched_property_ids))->get();
								}else{
										$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid', $property_ids)->get();
								}
	      		}

	      		if (sizeof($properties) > 0) {
	      				$selling_properties = [];
	      				$buying_properties = [];
	      				foreach ($properties as $property) {
			      				$user_ids = PropertyOwners::where('property_id', $property->uuid)->pluck('user_id')->toArray();
			      				if (sizeof($user_ids) < 0) {
				      					$property['owners'] = null;
				      			}else{
				      					$property['owners'] = Users::whereIn('uuid', array_unique($user_ids))->get();
				      			}

				      			$property_info = PropertyAgents::where('property_id', $property->uuid)->first();
				      			$property['agent_status'] = $property_info->status;
								if($filter == 'hide'){
									if($property_info->status == $filter){
										if ($property_info->agent_type == 'seller') {
											$selling_properties[] = $property;
										}else{
												$buying_properties[] = $property;
										}
									}
								}else{
									if ($property_info->agent_type == 'seller') {
										$selling_properties[] = $property;
									}else{
											$buying_properties[] = $property;
									}
								}
				      			$all_properties = array('selling_properties'=>$selling_properties, 'buying_properties'=>$buying_properties);
			      		}
	      				return $this->sendResponse($all_properties);
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

			      PropertyVerification::where('token', $request->token)->delete();
				}else{
						$status = 'expired';
				}

				return view('verified-property', ["status"=>$status]);
		}

		public function verifyPropertyOwner(Request $request){
				$this->validate($request, [
						'email' => 'required',
						'property_id' => 'required'
				]);

				$user_ids = PropertyOwners::where('property_id', $request->property_id)->pluck('user_id')->toArray();
				if (sizeof($user_ids) !== 0) {
						$owner = Users::whereIn('uuid', array_unique($user_ids))->where('email', $request->email)->first();
						if (!empty($owner)) {
								$this->configSMTP();
								$data = [
										'owner_name' => $owner->first_name . ' ' . $owner->last_name,
										'owner_id' => $owner->uuid,
										'email_verification_token' => $owner->email_verification_token,
								];

								try{
										Mail::to($request->email)->send(new PropertyOwnerVerificationMail($data));
										return $this->sendResponse("Verification mail sent successfully to Property Owner!");
								}catch(\Exception $e){
										$msg = $e->getMessage();
										return $this->sendResponse($msg, 200, false);
								}
						} else {
								return $this->sendResponse("Sorry, Property Owner not found!", 200, false);
						}
				} else {
						return $this->sendResponse("Sorry, Property Owner not found!", 200, false);
				}
		}

		public function verifiedPropertyOwner(Request $request){
				$this->validate($request, [
						'token' => 'required',
						'owner' => 'required',
				]);

				$owner = Users::where(['uuid' => $request->owner, 'email_verification_token' => $request->token])->first();

				if (!empty($owner)) {
						$updateStatus = Users::where(['uuid' => $request->owner, 'email_verification_token' => $request->token])->update(['email_verified' => "YES"]);
						if ($updateStatus) {
								return $this->sendResponse("Email verified successfully!");
						}else{
								return $this->sendResponse("Sorry, Something went wrong!", 200, false);
						}
				}else{
						return $this->sendResponse("Sorry, Property Owner not found!", 200, false);
				}
		}

		public function getAllProperties(Request $request){
				$this->validate($request, [
						'client_id' => 'required',
				]);

				$client_properties = PropertyOwners::where('user_id', $request->client_id)->pluck('property_id')->toArray();
				
				$properties = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereNotIn('uuid', $client_properties)->get();

				if (sizeof($properties) > 0) {
						return $this->sendResponse($properties);
				}else{
						return $this->sendResponse("Sorry, Properties not found!", 200, false);
				}
		}

		public function deleteProperty(Request $request){
				$this->validate($request, [
						'property_id' => 'required',
						'user_id' => 'required',
				]);

				$property_id = $request->property_id;
				$user_id = $request->user_id;

				$property = Properties::with('Homendo')->where('uuid', $property_id)->first();
				$user = Users::where('uuid', $user_id)->first();
				$agent = PropertyAgents::where(['property_id'=>$property_id, 'agent_type'=>'seller'])->first();

				if ($user->role == 'AGENT') {
						if ($property->Homendo->hmdo_listed == 1) {
								return $this->sendResponse("You only hide this property, Can't delete!", 200, false);
						}else{
								$removeAgent = PropertyAgents::where(['property_id'=>$property_id, 'agent_id'=>$user_id])->delete();
								if ($removeAgent) {
										return $this->sendResponse("Property deleted successfully!");
								}else{
										return $this->sendResponse("Sorry, Data not found or Something went wrong!", 200, false);
								}
						}
				}else{
						if ($property->Homendo->hmdo_listed == 1) {
								if (!empty($agent)) {
										$removeSeller = PropertyOwners::where(['property_id'=>$property_id, 'user_id'=>$user_id])->delete();
										if ($removeSeller) {
												return $this->sendResponse("Property deleted successfully!");
										}else{
												return $this->sendResponse("Sorry, Data not found or Something went wrong!", 200, false);
										}
								}else{
										$checkSeller = PropertyOwners::where(['property_id'=>$property_id, 'user_id'=>$user_id])->get();
										$removeSeller = PropertyOwners::where(['property_id'=>$property_id, 'user_id'=>$user_id])->delete();
										if (sizeof($checkSeller) == 1) {
												$removeAgent = PropertyAgents::where('property_id', $property_id)->delete();
												$removeBuyers = PropertyBuyers::where('property_id', $property_id)->delete();
												$deleteProperty = Properties::where('uuid', $property_id)->delete();
												$deletePropertyHomendo = PropertyHomendo::where('property_id', $property_id)->delete();
												$deletePropertyValuecheck = PropertyValuecheck::where('property_id', $property_id)->delete();
												$deletePropertyZillow = PropertyZillow::where('property_id', $property_id)->delete();
												$deletePropertyShowings = PropertyBookingSchedule::where('property_id', $property_id)->delete();
												$getShowingSetup = PropertyShowingSetup::where('property_id', $property_id)->first();
												$deleteShowingAvailibility = PropertyShowingAvailability::where('showing_setup_id', $getShowingSetup->uuid)->delete();
												$deleteShowingSurvey = PropertyShowingSurvey::where('showing_setup_id', $getShowingSetup->uuid)->delete();
												$deleteShowingSetup = PropertyShowingSetup::where('property_id', $property_id)->delete();
												$deletePropertyVerification = PropertyVerification::where('property_id', $property_id)->delete();
												$deletePropertyImages = PropertyImages::where('property_id', $property_id)->delete();
										}
										
										if ($removeSeller) {
												return $this->sendResponse("Property deleted successfully!");
										}else{
												return $this->sendResponse("Sorry, Data not found or Something went wrong!", 200, false);
										}
								}
						}else{
								if (empty($agent) || $agent == null) {
										$removeSeller = PropertyOwners::where(['property_id'=>$property_id, 'user_id'=>$user_id])->delete();
										if ($removeSeller) {
												return $this->sendResponse("Property deleted successfully!");
										}else{
												return $this->sendResponse("Sorry, Data not found or Something went wrong!", 200, false);
										}
								}else{
										$checkSeller = PropertyOwners::where(['property_id'=>$property_id, 'user_id'=>$user_id])->get();
										$removeSeller = PropertyOwners::where(['property_id'=>$property_id, 'user_id'=>$user_id])->delete();
										if (sizeof($checkSeller) == 1) {
												$removeAgent = PropertyAgents::where('property_id', $property_id)->delete();
												$removeBuyers = PropertyBuyers::where('property_id', $property_id)->delete();
												$deleteProperty = Properties::where('uuid', $property_id)->delete();
												$deletePropertyHomendo = PropertyHomendo::where('property_id', $property_id)->delete();
												$deletePropertyValuecheck = PropertyValuecheck::where('property_id', $property_id)->delete();
												$deletePropertyZillow = PropertyZillow::where('property_id', $property_id)->delete();
												$deletePropertyShowings = PropertyBookingSchedule::where('property_id', $property_id)->delete();
												$getShowingSetup = PropertyShowingSetup::where('property_id', $property_id)->first();
												$deleteShowingAvailibility = PropertyShowingAvailability::where('showing_setup_id', $getShowingSetup->uuid)->delete();
												$deleteShowingSurvey = PropertyShowingSurvey::where('showing_setup_id', $getShowingSetup->uuid)->delete();
												$deleteShowingSetup = PropertyShowingSetup::where('property_id', $property_id)->delete();
												$deletePropertyVerification = PropertyVerification::where('property_id', $property_id)->delete();
												$deletePropertyImages = PropertyImages::where('property_id', $property_id)->delete();
										}
										
										if ($removeSeller) {
												return $this->sendResponse("Property deleted successfully!");
										}else{
												return $this->sendResponse("Sorry, Data not found or Something went wrong!", 200, false);
										}
								}
						}
				}
		}
}