<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\PropertyShowingSetup;
use App\Models\PropertyShowingAvailability;
use App\Models\PropertyShowingSurvey;
use App\Models\SurveyCategories;
use App\Models\SurveySubCategories;
use App\Models\PropertyHomendo;
use App\Models\Users;
use App\Models\PropertyBookingSchedule;
use App\Models\AgentInfo;
use App\Mail\AgentShowingMail;
use App\Mail\SignupMail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

class ShowingController extends Controller
{
		public function createSlots(Request $request){
				$this->validate($request, [
						'interval'  => 'required',
						'start_date'=> 'required|date',
						'end_date'  => 'required|date'
			  ]);

				$startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);
				$endDate = Carbon::createFromFormat('Y-m-d', $request->end_date);
				$dateRange = CarbonPeriod::create($startDate, $endDate);
				$dateArray = $dateRange->toArray();  
				
				$interval = $request->interval*60;
        $open_time = strtotime('00:00');
        $close_time = strtotime('24:00');

        $output = [];
        for( $i=$open_time; $i<$close_time; $i+=$interval) 
				{
            $output[] = array('slot'=>date("h:i A", $i), 'status'=>'');
        }

				foreach($dateArray as $newDate)
				{
					$date = $newDate->format('F d l');
					$lastResult[] = array('date'=>$date,'slots'=>$output);
				}
	    	return $this->sendResponse($lastResult);
    }

		public function createSurveyCategory(Request $request){
				$this->validate($request, [
	      		'name' => 'required'
	      ]);

	      $time = strtotime(Carbon::now());
	      $uuid = "cat".$time.rand(10,99)*rand(10,99);
	      $category = new SurveyCategories;
	      $category->uuid = $uuid;
	      $category->name = $request->name;
	      $save_category = $category->save();

	      if ($save_category) {
	      		return $this->sendResponse("Category created successfully!");
	      }else{
	      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
	      }
		}

		public function updateSurveyCategory(Request $request){
				$this->validate($request, [
	      		'category_id' => 'required',
	      		'name' => 'required'
	      ]);

				$category = SurveyCategories::where('uuid', $request->category_id)->first();

				if ($category) {
	      		$update_category = SurveyCategories::where('uuid', $request->category_id)->update(['name'=>$request->name]);

			      if ($update_category) {
			      		return $this->sendResponse("Category updated successfully!");
			      }else{
			      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
			      }
	      }else{
	      		return $this->sendResponse("Sorry, Category not found!", 200, false);
	      }		
		}

		public function getAllCategories(Request $request){
	      $categories = SurveyCategories::with('subCategory')->get();

	      if (sizeof($categories)) {
	      		return $this->sendResponse($categories);
	      }else{
	      		return $this->sendResponse("Sorry, Category not found!", 200, false);
	      }
		}

		public function getSingleCategory(Request $request){
				$this->validate($request, [
	      		'category_id' => 'required'
	      ]);

	      $category = SurveyCategories::with('subCategory')->where('uuid', $request->category_id)->first();

	      if ($category) {
	      		return $this->sendResponse($category);
	      }else{
	      		return $this->sendResponse("Sorry, Category not found!", 200, false);
	      }
		}

		public function deleteCategory(Request $request){
				$this->validate($request, [
	      		'category_id' => 'required'
	      ]);

				\DB::beginTransaction();
	      try{
			      $delete_category = SurveyCategories::where('uuid', $request->category_id)->delete();
			      $delete_sub_category = SurveySubCategories::where('category_id', $request->category_id)->delete();

			      return $this->sendResponse("Category deleted successfully!");
			  } catch(\Exception $e) {
	      		\DB::rollBack();
	      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
	      }
		}

		public function createSurveySubCategory(Request $request){
				$this->validate($request, [
	      		'category_id' => 'required',
	      		'name' => 'required'
	      ]);

	      $time = strtotime(Carbon::now());
	      $uuid = "scat".$time.rand(10,99)*rand(10,99);
	      $sub_category = new SurveySubCategories;
	      $sub_category->uuid = $uuid;
	      $sub_category->category_id = $request->category_id;
	      $sub_category->name = $request->name;
	      $save_sub_category = $sub_category->save();

	      if ($save_sub_category) {
	      		return $this->sendResponse("Sub-Category created successfully!");
	      }else{
	      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
	      }
		}

		public function updateSurveySubCategory(Request $request){
				$this->validate($request, [
	      		'sub_category_id' => 'required',
				  'name' => 'required',
				  'category_id'=>'required'
	      ]);

				$sub_category = SurveySubCategories::where('uuid', $request->sub_category_id)->first();

				if ($sub_category) {
	      			$update_sub_category = SurveySubCategories::where('uuid', $request->sub_category_id)->update(['name'=>$request->name,'category_id'=>$request->category_id]);
			      if ($update_sub_category) {
			      		return $this->sendResponse("Sub-Category updated successfully!");
			      }else{
			      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
			      }
	      }else{
	      		return $this->sendResponse("Sorry, Sub-Category not found!", 200, false);
	      }		
		}

		public function deleteSubCategory(Request $request){
				$this->validate($request, [
	      		'sub_category_id' => 'required'
	      ]);

	      $delete_sub_category = SurveySubCategories::where('uuid', $request->sub_category_id)->delete();

	      if ($delete_sub_category) {
	      		return $this->sendResponse("Sub-Category deleted successfully!");
	      }else{
	      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
	      }
		}

    public function createShowingSetup(Request $request){
	    	$this->validate($request, [
	      		'property_id' => 'nullable',
	          'notification_email' => 'nullable',
	          'notification_text' => 'nullable',
	      		'type' => 'nullable|in:VALID,NO VALID',
	      		'validator' => 'nullable',
	      		'presence' => 'nullable',
	      		'instructions' => 'nullable',
	      		'lockbox_type' => 'nullable',
	      		'lockbox_location' => 'nullable',
	      		'lockbox_code' => 'nullable',
	      		'start_date' => 'nullable',
	      		'end_date' => 'nullable',
	      		'timeframe' => 'nullable',
	      		'overlap' => 'nullable|in:YES,NO'
	      ]);

	      $time = strtotime(Carbon::now());

    		$setup_uuid = "show".$time.rand(10,99)*rand(10,99);
	      $setup = new PropertyShowingSetup;
	      $setup->uuid = $setup_uuid;
	      $setup->property_id = $request->property_id;
	      $setup->notification_email = $request->notification_email;
	      $setup->notification_text = $request->notification_text;
	      $setup->type = $request->type;
	      $setup->validator = $request->validator;
	      $setup->presence = $request->presence;
	      $setup->instructions = $request->instructions;
	      $setup->lockbox_type = $request->lockbox_type;
	      $setup->lockbox_location = $request->lockbox_location;
	      $setup->lockbox_code = $request->lockbox_code;
	      if ($request->start_date == '') {
	      		$setup->start_date = null;
	      }else{
	      		$setup->start_date = $request->start_date;
	      }
	      if ($request->end_date == '') {
	      		$setup->end_date = null;
	      }else{
	      		$setup->end_date = $request->end_date;
	      }
	      $setup->timeframe = $request->timeframe;
	      $setup->overlap = $request->overlap;
	      $save_setup = $setup->save();

		  	if ($save_setup) {
		  			return $this->sendResponse("Showing setup created successfully!");
		  	}else{
		  			return $this->sendResponse("Sorry, Something went wrong!", 200, false);
		  	}
    }

    public function createShowingAvailability(Request $request){
    		$this->validate($request, [
	      		'showing_setup_id' => 'nullable',
	          'availability' => 'nullable'
	      ]);

    		$time = strtotime(Carbon::now());

	      $availability_uuid = "avlb".$time.rand(10,99)*rand(10,99);
	      $availability = new PropertyShowingAvailability;
	      $availability->uuid = $availability_uuid;
	      $availability->showing_setup_id = $request->showing_setup_id;
	      $availability->availability = json_encode($request->availability);
	      $save_availability = $availability->save();

	      if ($save_availability) {
		  			return $this->sendResponse("Showing availability created successfully!");
		  	}else{
		  			return $this->sendResponse("Sorry, Something went wrong!", 200, false);
		  	}
    }

    public function createShowingSurvey(Request $request){
    		$this->validate($request, [
	      		'showing_setup_id' => 'nullable',
	          'survey' => 'nullable'
	      ]);

    		$time = strtotime(Carbon::now());

	      $survey_uuid = "srvy".$time.rand(10,99)*rand(10,99);
	      $survey = new PropertyShowingSurvey;
	      $survey->uuid = $survey_uuid;
	      $survey->showing_setup_id = $request->showing_setup_id;
	      $survey->survey = json_encode($request->survey);
	      $save_survey = $survey->save();

	      if ($save_survey) {
		  			return $this->sendResponse("Showing survey created successfully!");
		  	}else{
		  			return $this->sendResponse("Sorry, Something went wrong!", 200, false);
		  	}
    }

    public function updateShowingSetup(Request $request){
	    	$this->validate($request, [
	      		'showing_setup_id' => 'nullable',
	          'notification_email' => 'nullable',
	          'notification_text' => 'nullable',
	      		'type' => 'nullable|in:VALID,NO VALID',
	      		'validator' => 'nullable',
	      		'presence' => 'nullable',
	      		'instructions' => 'nullable',
	      		'lockbox_type' => 'nullable',
	      		'lockbox_location' => 'nullable',
	      		'lockbox_code' => 'nullable',
	      		'start_date' => 'nullable',
	      		'end_date' => 'nullable',
	      		'timeframe' => 'nullable',
	      		'overlap' => 'nullable|in:YES,NO'
	      ]);

	    	$showing_setup = PropertyShowingSetup::where('uuid', $request->showing_setup_id)->first();

	    	if ($showing_setup) {
	    			if ($request->start_date != null && $request->end_date != null) {
			    			if ($showing_setup->start_date != $request->start_date || $showing_setup->end_date != $request->end_date) {
			    					$showing_availability = PropertyShowingAvailability::where('showing_setup_id', $request->showing_setup_id)->first();
										$get_availability = json_decode($showing_availability);
										$availability = json_decode($get_availability->availability);

										if (!empty($showing_availability)) {
												$startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);
												$endDate = Carbon::createFromFormat('Y-m-d', $request->end_date);
												$dateRange = CarbonPeriod::create($startDate, $endDate);
												$dateArray = $dateRange->toArray();  
												
												$interval = 15*60;
								        $open_time = strtotime('00:00');
								        $close_time = strtotime('24:00');

								        $output = [];
								        for( $i=$open_time; $i<$close_time; $i+=$interval) 
												{
														$output[] = array('slot'=>date("h:i A", $i), 'status'=>'');
								        }

												foreach($dateArray as $newDate)
												{
														$date = $newDate->format('F d l');
														$lastResult[] = array('date'=>$date,'slots'=>$output);
												}

												$newResult = [];
												foreach ($lastResult as $result) {
														foreach ($availability as $avail) {
																if ($avail->date == $result['date']) {
																		$result['slots'] = $avail->slots;
																		break;
																}
														}
														$newResult[] = $result;
												}

												$old_dates = [];
												foreach ($availability as $avail) {
														$old_dates[] = date('Y-m-d', strtotime($avail->date));
												}

												$new_dates = [];
												foreach ($lastResult as $result) {
														$new_dates[] = date('Y-m-d', strtotime($result['date']));
												}

												$drop_dates = array_diff($old_dates, $new_dates);

												if (sizeof($drop_dates) > 0) {
														$bookings = PropertyBookingSchedule::where('property_id', $showing_setup->property_id)->whereIn('booking_date', $drop_dates)->update(['status'=>'R']);
												}
												
												PropertyShowingAvailability::where('showing_setup_id', $request->showing_setup_id)->update(['availability'=>json_encode($newResult)]);
										}
								}
						}

	    			if ($request->start_date == '') {
			      		$start_date = null;
			      }else{
			      		$start_date = $request->start_date;
			      }
			      if ($request->end_date == '') {
			      		$end_date = null;
			      }else{
			      		$end_date = $request->end_date;
			      }

			      $update_setup = PropertyShowingSetup::where('uuid', $request->showing_setup_id)->update([
				      	'notification_email'=>$request->notification_email,
				      	'notification_text'=>$request->notification_text,
				      	'type'=>$request->type,
				      	'validator'=>$request->validator,
				      	'presence'=>$request->presence,
				      	'instructions'=>$request->instructions,
				      	'lockbox_type'=>$request->lockbox_type,
				      	'lockbox_location'=>$request->lockbox_location,
				      	'lockbox_code'=>$request->lockbox_code,
				      	'start_date'=>$start_date,
				      	'end_date'=>$end_date,
				      	'timeframe'=>$request->timeframe,
				      	'overlap'=>$request->overlap,
			      ]);

			      if ($update_setup) {
				  			return $this->sendResponse("Showing setup updated successfully!");
				  	}else{
				  			return $this->sendResponse("Sorry, Something went wrong!", 200, false);
				  	}
	    	}else{
	    			return $this->sendResponse("Sorry, Showing setup not found!", 200, false);
	    	}
    }

    public function updateShowingAvailability(Request $request){
    		$this->validate($request, [
	      		'showing_setup_id' => 'nullable',
	      		'availability' => 'nullable'
	      ]);

    		$showing_setup = PropertyShowingSetup::where('uuid', $request->showing_setup_id)->first();

	    	if ($showing_setup) {
	    			$showing_availability = PropertyShowingAvailability::where('showing_setup_id', $request->showing_setup_id)->first();
	    			if ($showing_availability) {
	    					$update_availibility = PropertyShowingAvailability::where('showing_setup_id', $request->showing_setup_id)->update([
	    							'availability'=>json_encode($request->availability)
			      		]);
	    			}else{
	    					$time = strtotime(Carbon::now());
			    			$availability_uuid = "avlb".$time.rand(10,99)*rand(10,99);
			    			$update_availibility = PropertyShowingAvailability::updateOrCreate(
			    					['showing_setup_id'=>$request->showing_setup_id],
			    					['uuid'=>$availability_uuid, 'availability'=>json_encode($request->availability)]
			    			);
	    			}

			      if ($update_availibility) {
				  			return $this->sendResponse("Showing availability updated successfully!");
				  	}else{
				  			return $this->sendResponse("Sorry, Something went wrong!", 200, false);
				  	}
				}else{
	    			return $this->sendResponse("Sorry, Showing setup not found!", 200, false);
	    	}
    }

    public function updateShowingSurvey(Request $request){
    		$this->validate($request, [
	      		'showing_setup_id' => 'nullable',
	      		'survey' => 'nullable'
	      ]);

    		$showing_setup = PropertyShowingSetup::where('uuid', $request->showing_setup_id)->first();
    		
    		if ($showing_setup) {
    				$showing_survey = PropertyShowingSurvey::where('showing_setup_id', $request->showing_setup_id)->first();
    				if ($showing_survey) {
    						$update_survey = PropertyShowingSurvey::where('showing_setup_id', $request->showing_setup_id)->update([
					      		'survey'=>json_encode($request->survey)
					      ]);
    				}else{
    						$time = strtotime(Carbon::now());
	      				$survey_uuid = "srvy".$time.rand(10,99)*rand(10,99);
	      				$update_survey = PropertyShowingSurvey::updateOrCreate(
			    					['showing_setup_id'=>$request->showing_setup_id],
			    					['uuid'=>$survey_uuid, 'survey'=>json_encode($request->survey)]
			    			);
    				}

			      if ($update_survey) {
				  			return $this->sendResponse("Showing survey updated successfully!");
				  	}else{
				  			return $this->sendResponse("Sorry, Something went wrong!", 200, false);
				  	}
				}else{
	    			return $this->sendResponse("Sorry, Showing setup not found!", 200, false);
	    	}
    }

    public function getSingleShowingSetup(Request $request){
    		$this->validate($request, [
	      		'property_id' => 'required'
	      ]);

	      $showing_setup = PropertyShowingSetup::with('showingAvailability', 'showingSurvey', 'Property')->where('property_id', $request->property_id)->first();

	      if ($showing_setup) {
	      		return $this->sendResponse($showing_setup);
	      }else{
	      		return $this->sendResponse("Sorry, Showing setup not found!", 200, false);
	      }
    }

    public function getSingleShowingSetupNonAuth(Request $request){
    		$this->validate($request, [
	      		'property_id' => 'required'
	      ]);

	      $showing_setup = PropertyShowingSetup::with('showingAvailability', 'showingSurvey', 'Property')->where('property_id', $request->property_id)->first();

	      if ($showing_setup) {
	      		return $this->sendResponse($showing_setup);
	      }else{
	      		return $this->sendResponse("Sorry, Showing setup not found!", 200, false);
	      }
    }

    public function getSingleSetup(Request $request){
    		$this->validate($request, [
	      		'mls_id'						=> 'nullable',
						'originator'				=> 'nullable',
						'agent_id'					=> 'nullable',
						'agent_originator'	=> 'nullable',
						'email'							=> 'nullable',
						'property_status'		=> 'nullable',
						'url'								=> 'nullable',
	      ]);

	      $property = PropertyHomendo::where(['hmdo_mls_id'=>$request->mls_id, 'hmdo_mls_originator'=>$request->originator])->first();
	      $agent = Users::where(['mls_id'=>$request->agent_id, 'mls_name'=>$request->agent_originator, 'email'=>$request->email])->first();

	      if ($property != null) {
	      		$showing_setup = PropertyShowingSetup::with('showingAvailability', 'showingSurvey', 'Property')->where('property_id', $property->property_id)->first();

			      if ($showing_setup) {
			      		return $this->sendResponse($showing_setup);
			      }else{
			      		return $this->sendResponse("Sorry, Showing setup not found!", 200, false);
			      }
	      }elseif ($agent != null) {
	      		$data = [
                'name' => $agent->first_name.' '.$agent->last_name,
                'mls_id' => $request->mls_id,
                'originator' => $request->originator
            ];

            try{
                Mail::to($agent->email)->send(new AgentShowingMail($data));
            }catch(\Exception $e){
                /*$msg = $e->getMessage();
                return $this->sendResponse($msg, 200, false);*/
            }
	      		return $this->sendResponse('Mail sent successfully to agent for property showing!');
	      }else{
	      		$checkAgent = Users::where(['email'=>$request->email, 'mls_id'=>$request->agent_id, 'mls_name'=>$request->agent_originator])->first();
	      		if (!empty($checkAgent)) {
	      				$checkAgentInfo = 0;
	      				$agentInfo = $this->getAgentInfo($request->agent_id, $request->email, $request->agent_originator);
	      				if ($agentInfo) {
	      					$checkAgentInfo = 1;
	      					$agent = json_decode($agentInfo);
	      				}
			      		
			      		$time = strtotime(Carbon::now());
		            $uuid = "usr".$time.rand(10,99)*rand(10,99);
		            $user = new Users;
		            $user->uuid = $uuid;
		            $user->email = $request->email;
		            $user->role = "AGENT";
		            $user->mls_id = $request->agent_id;
		            $user->mls_name = $request->agent_originator;
		            $user->phone_verified = "NO";
		            $user->email_verified = "NO";
		            $user->image = env("APP_URL")."public/user-images/default.png";
		            $result = $user->save();
		            if ($result) {
			            	if ($checkAgentInfo == 1) {
			            			$agent_info = new AgentInfo;
						        		$agent_info->agent_id = $uuid;
						        		$agent_info->hmdo_lastupdated = $agent->agent->hmdo_lastupdated[1];
						        		$agent_info->hmdo_mls_originator = $agent->agent->hmdo_mls_originator[1];
						        		$agent_info->hmdo_agent_name = $agent->agent->hmdo_agent_name[1];
						        		$agent_info->hmdo_agent_title = $agent->agent->hmdo_agent_title[1];
						        		$agent_info->hmdo_agent_photo_url = $agent->agent->hmdo_agent_photo_url[1];
						        		$agent_info->hmdo_agent_email = $agent->agent->hmdo_agent_email[1];
						        		$agent_info->hmdo_office_main_phone = $agent->agent->hmdo_office_main_phone[1];
						        		$agent_info->hmdo_office_direct_phone = $agent->agent->hmdo_office_direct_phone[1];
						        		$agent_info->hmdo_office_mobile_phone = $agent->agent->hmdo_office_mobile_phone[1];
						        		$agent_info->hmdo_agent_skills = $agent->agent->hmdo_agent_skills[1];
						        		$agent_info->hmdo_office_id = $agent->agent->hmdo_office_id[1];
						        		$agent_info->hmdo_office_name = $agent->agent->hmdo_office_name[1];
						        		$agent_info->hmdo_office_photo = $agent->agent->hmdo_office_photo[1];
						        		$agent_info->hmdo_office_street = $agent->agent->hmdo_office_street[1];
						        		$agent_info->hmdo_office_city = $agent->agent->hmdo_office_city[1];
						        		$agent_info->hmdo_office_zipcode = $agent->agent->hmdo_office_zipcode[1];
						        		$agent_info->hmdo_office_state = $agent->agent->hmdo_office_state[1];
						        		$agent_info->hmdo_office_phone = $agent->agent->hmdo_office_phone[1];
						        		$agent_info->hmdo_office_website = $agent->agent->hmdo_office_website[1];
						        		$agent_info->save();
			            	}

		            		$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
				            Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

				            $this->configSMTP();
				            $data = [
				                'name'=>'',
				                'verification_token'=>$verification_token,
				                'email'=>$request->email,
				                'url'=>$request->url
				            ];
				            Mail::to($request->email)->send(new SignupMail($data));

				            $showingData = [
					      				'name' => '',
					      				'mls_id' => $request->mls_id,
					      				'originator' => $request->originator
					      		];
				            Mail::to($request->email)->send(new AgentShowingMail($showingData));
				            
				            return $this->sendResponse('Mail sent successfully to agent for create password!');
		            }else{
		            		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
		            }
	      		}else{
            		return $this->sendResponse("User already exist!", 200, false);
            }
	      }
    }

    public function getAgentInfo($mls_id, $email, $originator){
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
					  	'token' => '"'.md5(strtotime('now')).'"',
					  	'agentid' => '"'.$mls_id.'"',
					  	'email' => '"'.$email.'"',
					  	'originator' => '"'.$originator.'"',
					  	'deviceid' => '"'.$_SERVER['HTTP_USER_AGENT'].'"',
					  	'hmdoapp' => 'Showing.VIP-1.0'
					  ),
				));

				$response = curl_exec($curl);

				curl_close($curl);

				return $response;
		}
}