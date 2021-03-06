<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\Properties;
use App\Models\PropertyAgents;
use App\Models\UserAgents;
use App\Models\PropertyValuecheck;
use App\Models\PropertyZillow;
use App\Models\PropertyHomendo;
use App\Models\PropertyOwners;
use App\Models\PropertyBookingSchedule;
use App\Models\PropertyBuyers;
use App\Models\PropertyVerification;
use App\Mail\TestMail;
use App\Mail\AssignOwner;
use App\Mail\SignupMail;
use Carbon\Carbon;

class AgentController extends Controller
{
    public function getClientWithProperty(Request $request){
        $this->validate($request, [
            'agent_id' => 'required'
        ]);

        $result = PropertyAgents::with('owner','property.Valuecheck','property.Zillow','property.Homendo')->where('agent_id',$request->agent_id)->get();

        if (sizeof($result) > 0) {
            return $this->sendResponse($result);
        }else{
            return $this->sendResponse("No Properties found!.",200,false);
        }
    }

    public function GetRandomAgents(Request $request){
        $this->validate($request, [
            'number' => 'required'
        ]);

        $skip = $request->number;
        $result = Users::with('agentInfo')->whereNotNull('password')->where('role','AGENT')->skip($skip)->take(4)->get();
        if ($result) {
            return $this->sendResponse($result);
        }else{
            return $this->sendResponse("Sorry!Something wrong!.",200,false);
        }
    }

    public function getUserAgents(Request $request){
        $this->validate($request, [
            'user_id' => 'required'
        ]);
		
		$all_users = [];
        $agents = UserAgents::where('user_id',$request->user_id)->pluck('agent_id')->toArray();
        if (sizeof($agents)>0) {
            $users = Users::with(['agentInfo', 'city', 'state'])->whereIn('uuid',$agents)->get();
			foreach($users as $user){
				if($user->country != null || $user->country != ''){
					if($user->country == '231'){
						$user->country = 'US';
					}elseif($user->country == '38'){
						$user->country = 'Canada';
					}elseif($user->country == '142'){
						$user->country = 'Mexico';
					}else{
						$user->country = null;
					}
				}
				$all_users[] = $user;
			}
            return $this->sendResponse($all_users);
        }else{
            return $this->sendResponse("Agent not found!.",200,false);
        }
    }

    public function addAgentProperties(Request $request){
        $this->validate($request, [
            'agent_id' => 'required',
            'properties' => 'required'
        ]);
        
        $all_properties = Properties::with('Homendo')->get();
        
        foreach ($request->properties as $new_property) {

            if ($new_property['hmdo_mls_price'][1] != null || $new_property['hmdo_mls_price'][1] != '') {
                $hmdo_mls_price =$new_property['hmdo_mls_price'][1];
            }else{
                $hmdo_mls_price = 0;
            }

            foreach ($all_properties as $property) {
                $check = 0;
                if ($property->mls_id == $new_property['hmdo_mls_id'][1] && $property->mls_name == $new_property['hmdo_mls_originator'][1]) {
                    $check = 1;
                    $match_property = $property;
                    break;
                }
            }

            if ($check == 0) {
                $time = strtotime(Carbon::now());
                $uuid = "prty".$time.rand(10,99)*rand(10,99);
                $property = new Properties;
                $property->uuid = $uuid;
                $property->mls_id = $new_property['hmdo_mls_id'][1];
                $property->mls_name = $new_property['hmdo_mls_originator'][1];
                $property->data = json_encode($new_property);
                $property->verified = 'YES';
                $property->price = str_replace(array('$', ','), '', $hmdo_mls_price);
                $property->last_update = date('Y-m-d H:i:s');
                $property->added_by = 'Agent';
                $add_property = $property->save();

                $valuecheck = new PropertyValuecheck;
                $valuecheck->uuid = "vlck".$time.rand(10,99)*rand(10,99);
                $valuecheck->property_id = $uuid;
                $add_valuecheck = $valuecheck->save();

                $zillow = new PropertyZillow;
                $zillow->uuid = "zilw".$time.rand(10,99)*rand(10,99);
                $zillow->property_id = $uuid;
                $add_zillow = $zillow->save();

                $homendo = new PropertyHomendo;
                $homendo->uuid = "hmdo".$time.rand(10,99)*rand(10,99);
                $homendo->property_id = $uuid;
                $homendo->hmdo_lastupdated = $new_property['hmdo_lastupdated'][1];
                $homendo->hmdo_mls_id = $new_property['hmdo_mls_id'][1];
                $homendo->hmdo_mls_originator = $new_property['hmdo_mls_originator'][1];
                $homendo->hmdo_mls_proptype = $new_property['hmdo_mls_proptype'][1];
                $homendo->hmdo_mls_propname = $new_property['hmdo_mls_propname'][1];
                $homendo->hmdo_mls_status = $new_property['hmdo_mls_status'][1];
                $homendo->hmdo_mls_price = $new_property['hmdo_mls_price'][1];
                $homendo->hmdo_mls_streetnumber = $new_property['hmdo_mls_streetnumber'][1];
                $homendo->hmdo_mls_streetdirection = $new_property['hmdo_mls_streetdirection'][1];
                $homendo->hmdo_mls_streetname = $new_property['hmdo_mls_streetname'][1];
                $homendo->hmdo_mls_streettype = $new_property['hmdo_mls_streettype'][1];
                $homendo->hmdo_mls_unitnumber = $new_property['hmdo_mls_unitnumber'][1];
                $homendo->hmdo_mls_city = $new_property['hmdo_mls_city'][1];
                $homendo->hmdo_mls_zipcode = $new_property['hmdo_mls_zipcode'][1];
                $homendo->hmdo_mls_state = $new_property['hmdo_mls_state'][1];
                $homendo->hmdo_mls_latitude = $new_property['hmdo_mls_latitude'][1];
                $homendo->hmdo_mls_longitude = $new_property['hmdo_mls_longitude'][1];
                $homendo->hmdo_mls_yearbuilt = $new_property['hmdo_mls_yearbuilt'][1];
                $homendo->hmdo_mls_beds = $new_property['hmdo_mls_beds'][1];
                $homendo->hmdo_mls_baths = $new_property['hmdo_mls_baths'][1];
                $homendo->hmdo_mls_sqft = $new_property['hmdo_mls_sqft'][1];
                $homendo->hmdo_mls_acres = $new_property['hmdo_mls_acres'][1];
                $homendo->hmdo_mls_carspaces = $new_property['hmdo_mls_carspaces'][1];
                $homendo->hmdo_mls_url = $new_property['hmdo_mls_url'][1];
                $homendo->hmdo_mls_thumbnail = $new_property['hmdo_mls_thumbnail'][1];
                $add_homendo = $homendo->save();

                $property_agent = new PropertyAgents;
                $property_agent->property_id = $uuid;
                $property_agent->property_mls_id = $new_property['hmdo_mls_id'][1];
                $property_agent->property_originator = $new_property['hmdo_mls_originator'][1];
                $property_agent->agent_id = $request->agent_id;
                $property_agent->agent_type = 'seller';
                $property_agent->save();

                $showings = PropertyBookingSchedule::where('property_id', '')->orWhereNull('property_id')->get();
                foreach ($showings as $showing) {
                    if ($showing->property_mls_id == $property->mls_id) {
                        PropertyBookingSchedule::where('property_mls_id', $property->mls_id)->update(['property_id'=>$property->uuid]);
                    }
                }
            }else{
                if ($new_property['hmdo_lastupdated'][1] > $property->last_update) {
                    $update_property = Properties::where('uuid', $match_property->uuid)->update(['data'=>json_encode($new_property), 'last_update'=>date('Y-m-d H:i:s')]);
                    $update_homendo = PropertyHomendo::where('property_id', $match_property->uuid)->update([
                        'hmdo_lastupdated'=>$new_property['hmdo_lastupdated'][1],
                        'hmdo_mls_id'=>$new_property['hmdo_mls_id'][1],
                        'hmdo_mls_originator'=>$new_property['hmdo_mls_originator'][1],
                        'hmdo_mls_proptype'=>$new_property['hmdo_mls_proptype'][1],
                        'hmdo_mls_propname'=>$new_property['hmdo_mls_propname'][1],
                        'hmdo_mls_status'=>$new_property['hmdo_mls_status'][1],
                        'hmdo_mls_price'=>$new_property['hmdo_mls_price'][1],
                        'hmdo_mls_streetnumber'=>$new_property['hmdo_mls_streetnumber'][1],
                        'hmdo_mls_streetdirection'=>$new_property['hmdo_mls_streetdirection'][1],
                        'hmdo_mls_streetname'=>$new_property['hmdo_mls_streetname'][1],
                        'hmdo_mls_streettype'=>$new_property['hmdo_mls_streettype'][1],
                        'hmdo_mls_unitnumber'=>$new_property['hmdo_mls_unitnumber'][1],
                        'hmdo_mls_city'=>$new_property['hmdo_mls_city'][1],
                        'hmdo_mls_zipcode'=>$new_property['hmdo_mls_zipcode'][1],
                        'hmdo_mls_state'=>$new_property['hmdo_mls_state'][1],
                        'hmdo_mls_latitude'=>$new_property['hmdo_mls_latitude'][1],
                        'hmdo_mls_longitude'=>$new_property['hmdo_mls_longitude'][1],
                        'hmdo_mls_yearbuilt'=>$new_property['hmdo_mls_yearbuilt'][1],
                        'hmdo_mls_beds'=>$new_property['hmdo_mls_beds'][1],
                        'hmdo_mls_baths'=>$new_property['hmdo_mls_baths'][1],
                        'hmdo_mls_sqft'=>$new_property['hmdo_mls_sqft'][1],
                        'hmdo_mls_acres'=>$new_property['hmdo_mls_acres'][1],
                        'hmdo_mls_carspaces'=>$new_property['hmdo_mls_carspaces'][1],
                        'hmdo_mls_url'=>$new_property['hmdo_mls_url'][1],
                        'hmdo_mls_thumbnail'=>$new_property['hmdo_mls_thumbnail'][1]
                    ]);
                }
            }
        }

        return $this->sendResponse('Agent properties upodated successfully!');
    }

    public function addClient(Request $request){
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'role' => 'required|in:SELLER,BUYER',
            'agent_id' => 'required',
            'property_id' => 'nullable',
            'url' => 'required'
        ]);

        $check_user = Users::where(['email'=>$request->email, 'sub_role'=>'SELLER'])->first();
        if (!empty($check_user)) {
            $owner = new PropertyOwners;
            $owner->property_id = $request->property_id;
            $owner->user_id = $check_user->uuid;
            $owner->type = 'main_owner';
            $owner->verify_status = 'YES';
            $property_owner = $owner->save();
            return $this->sendResponse("Client added successfully!");
        }

        $prop_agent = Users::where('uuid', $request->agent_id)->first();
        $email_check = Users::where('email', $request->email)->first();
        $phone_check = Users::where('phone', $request->phone)->first();
        $property = Properties::where('uuid', $request->property_id)->first();
        if (!empty($property)) {
            $homendo = PropertyHomendo::where('property_id', $request->property_id)->first();
            $property_name = $homendo->hmdo_mls_propname;
        }else{
            $property_name = '';
        }

        if ($email_check !== null) {
            return $this->sendResponse("Sorry, Email already exist!", 200, false);
        }elseif ($phone_check !== null) {
            return $this->sendResponse("Sorry, Phone no. already exist!", 200, false);
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
                $user->phone = $request->phone;
                $user->role = "USER";
                $user->sub_role = $request->role;
                $user->phone_verified = "NO";
                $user->email_verified = "NO";
                $user->image = "default.png";
                $result = $user->save();

                if ($request->role == 'SELLER') {
                    $owner = new PropertyOwners;
                    $owner->property_id = $request->property_id;
                    $owner->user_id = $user->uuid;
                    $owner->type = 'main_owner';
                    $owner->verify_status = 'YES';
                    $property_owner = $owner->save();
                }else{
                    $buyer = new PropertyBuyers;
                    $buyer->property_id = $request->property_id;
                    $buyer->buyer_id = $user->uuid;
                    $buyer->agent_id = $request->agent_id;
                    $property_buyer = $buyer->save();
                }

                $this->configSMTP();
                $verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
                Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

                $dataAssignOwner = [
                    'name'=>$request->first_name.' '.$request->last_name,
                    'owner_name'=>$prop_agent->first_name.' '.$prop_agent->last_name,
                    'property_name'=>$property_name
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

                return $this->sendResponse("Client added successfully!");
            } catch(\Exception $e) {
                \DB::rollBack();
                return $this->sendResponse("Sorry, Something went wrong!", 200, false);
            }
        }
    }

    public function getClientProperties(Request $request){
        $this->validate($request, [
            'agent_id' => 'required'
        ]);

        $properties = PropertyAgents::with('property.Valuecheck','property.Zillow','property.Homendo')->where('agent_id',$request->agent_id)->get();

        $all_properties = [];
        $buying_properties = [];
        $selling_properties = [];

        foreach ($properties as $property) {
            $propertyInfo = Properties::with('propertySellers.User')->where('uuid', $property->property_id)->first();
            if (!empty($propertyInfo)) {
                if ($property->agent_type == 'buyer') {
                    $buyer = Users::where('uuid', $property->buyer_id)->first();
                    $property['buyer'] = $buyer;
                    $buying_properties[] = $property;
                }else{
                    $seller = PropertyOwners::with('User')->where(['property_id'=>$property->property_id, 'type'=>'main_owner'])->first();
                    $property['seller'] = $seller;
                    $property['all_sellers'] = $propertyInfo->propertySellers;
                   
                    $property_verification = 'YES';
                    if ($propertyInfo->verified == 'NO') {
                        $property_verification = 'NO';
                    }

                    $owner_verification = 'YES';
                    if (sizeof($property['all_sellers']) > 0) {
                        foreach ($property['all_sellers'] as $property_seller) {
                            if ($property_seller->verify_status == 'NO') {
                                $owner_verification = 'NO';
                                break;
                            }
                        }
                    }
                    
                    if ($property_verification == 'NO' || $owner_verification == 'NO') {
                        $property['verify_ownership'] = 'NO';
                    }else{
                        $property['verify_ownership'] = 'YES';
                    }

                    $selling_properties[] = $property;
                }
            }
        }

        $response = array('buying_properties'=>$buying_properties, 'selling_properties'=>$selling_properties);
        if (sizeof($response) > 0) {
            return $this->sendResponse($response);
        }else{
            return $this->sendResponse("No Properties found!",200,false);
        }
    }

    public function allClientProperties(Request $request){
        $this->validate($request, [
            'agent_id' => 'required'
        ]);

        $property_ids = PropertyAgents::with('property.Valuecheck','property.Zillow','property.Homendo')->where('agent_id',$request->agent_id)->pluck('property_id')->toArray();

        $seller_ids = PropertyOwners::whereIn('property_id', $property_ids)->pluck('user_id')->toArray();
        $buyer_ids = PropertyBuyers::whereIn('property_id', $property_ids)->pluck('buyer_id')->toArray();

        $sellers = [];
        if (sizeof($seller_ids) > 0) {
            foreach (array_unique($seller_ids) as $seller_id) {
                $seller_properties = [];
                $seller = Users::where('uuid', $seller_id)->first();
                
                $seller_props = PropertyOwners::where('user_id', $seller_id)->get();
                
                foreach ($seller_props as $seller_prop) {
                    $check_agent = PropertyAgents::where(['property_id'=>$seller_prop->property_id, 'agent_id'=>$request->agent_id])->first();
                    if ($check_agent != null) {
                        $property = Properties::with('Valuecheck', 'Zillow', 'Homendo')->where('uuid', $seller_prop->property_id)->first();
                        $property['verify_status'] = $seller_prop->verify_status;
                        $seller_properties[] = $property;
                    }
                }
                $seller['proprties'] = $seller_properties;
                $sellers[] = $seller;
            }
        }

        $buyers = [];
        if (sizeof($buyer_ids) > 0) {
            foreach (array_unique($buyer_ids) as $buyer_id) {
                $buyer_properties = [];
                $buyer = Users::where('uuid', $buyer_id)->first();
                
                $buyer_props = PropertyBuyers::where('buyer_id', $buyer_id)->get();

                foreach ($buyer_props as $buyer_prop) {
                    $check_agent = PropertyAgents::where(['property_id'=>$buyer_prop->property_id, 'agent_id'=>$request->agent_id])->first();
                    if ($check_agent != null) {
                        $property = Properties::with('Valuecheck', 'Zillow', 'Homendo')->where('uuid', $buyer_prop->property_id)->first();
                        $buyer_properties[] = $property;
                    }
                }
                $buyer['proprties'] = $buyer_properties;
                $buyers[] = $buyer;
            }
        }

        if (sizeof($sellers) > 0 || sizeof($buyers) > 0) {
            $response = array('sellers'=>$sellers, 'buyers'=>$buyers);
            return $this->sendResponse($response);
        }else{
            return $this->sendResponse("No data found!",200,false);
        }
    }

    public function addAgentProperty(Request $request){
        $this->validate($request, [
            'agent_id' => 'required',
            'data' => 'required',
            'client_id' => 'required'
        ]);

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

        $propertyCheck = Properties::where(['mls_id'=>$mls_id, 'mls_name'=>$mls_name])->first();

        $add_property = '0';
        if (!empty($propertyCheck)) {
            $checkClient = PropertyOwners::where(['user_id'=>$request->client_id, 'property_id'=>$propertyCheck->uuid])->first();
            if (empty($checkClient)) {
                $add_property = '1';
            }else{
                $add_property = '0';
            }    
        }else{
            $add_property = '2';
        }
        
        if ($add_property == '1') {
            $check_agent = PropertyAgents::where(['property_id'=>$propertyCheck->uuid, 'agent_id'=>$request->agent_id, 'agent_type'=>'seller'])->first();

            if (empty($check_agent)) {
                $agent = new PropertyAgents;
                $agent->property_id = $propertyCheck->uuid;
                $agent->property_mls_id = $mls_id;
                $agent->property_originator = $mls_name;
                $agent->agent_id = $request->agent_id;
                $agent->agent_type = 'seller';
                $property_agent = $agent->save();
            }

            $owner = new PropertyOwners;
            $owner->property_id = $propertyCheck->uuid;
            $owner->user_id = $request->client_id;
            $owner->type = 'main_owner';
            $owner->verify_status = 'YES';
            $property_owner = $owner->save();

            if ($property_owner) {
                return $this->sendResponse("Property added successfully!");
            }else{
                return $this->sendResponse("Sorry, Something went wrong!", 200, false);
            }
        }elseif ($add_property == '2') {
            $time = strtotime(Carbon::now());
            $uuid = "prty".$time.rand(10,99)*rand(10,99);

            $property = new Properties;
            $property->uuid = $uuid;
            $property->mls_id = $mls_id;
            $property->mls_name = $mls_name;
            $property->data = json_encode($request->data);
            $property->verified = 'YES';
            $property->price = str_replace(array('$', ','), '', $hmdo_mls_price);
            $property->last_update = date('Y-m-d H:i:s');
            $property->added_by = 'Agent';
            $property->save();

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
                if (isset($request->data['property'][1][1]['z_prop_url'][1]['changingThisBreaksApplicationSecurity'])) {
                    $zillow->z_prop_url = $request->data['property'][1][1]['z_prop_url'][1]['changingThisBreaksApplicationSecurity'];
                }else{
                    $zillow->z_prop_url = $request->data['property'][1][1]['z_prop_url'][1][0];
                }
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
            $homendo->hmdo_mls_id = $mls_id;
            $homendo->hmdo_mls_originator = $mls_name;
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
                if (isset($request->data['property'][2][1]['hmdo_mls_url'][1]['changingThisBreaksApplicationSecurity'])) {
                    $homendo->hmdo_mls_url = $request->data['property'][2][1]['hmdo_mls_url'][1]['changingThisBreaksApplicationSecurity'];
                }else{
                    $homendo->hmdo_mls_url = $request->data['property'][2][1]['hmdo_mls_url'][1][0];
                }
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
              
            $agent = new PropertyAgents;
            $agent->property_id = $property->uuid;
            $agent->property_mls_id = $mls_id;
            $agent->property_originator = $mls_name;
            $agent->agent_id = $request->agent_id;
            $agent->agent_type = 'seller';
            $property_agent = $agent->save();

            $owner = new PropertyOwners;
            $owner->property_id = $property->uuid;
            $owner->user_id = $request->client_id;
            $owner->type = 'main_owner';
            $owner->verify_status = 'YES';
            $property_owner = $owner->save();

            $showings = PropertyBookingSchedule::where('property_id', '')->orWhereNull('property_id')->get();

            foreach ($showings as $showing) {
                if ($showing->property_mls_id == $property->mls_id) {
                    PropertyBookingSchedule::where('property_mls_id', $property->mls_id)->update(['property_id'=>$property->uuid]);
                }
            }

            if ($property_agent) {
                return $this->sendResponse("Property added successfully!");
            }else{
                return $this->sendResponse("Sorry, Something went wrong!", 200, false);
            }
        }else{
            return $this->sendResponse("Property already exist!", 200, false);
        }
    }

    public function testMail(Request $request){
        $this->validate($request, [
            'email' => 'required'
        ]);

        $configSMTP = $this->configSMTP();
        $data = array('url'=>env('APP_URL'));
        try{
            Mail::to($request->email)->send(new TestMail($data));
            return $this->sendResponse("Mail sent successfully!");
        }catch(\Exception $e){
            $msg = $e->getMessage();
            return $this->sendResponse($msg, 200, false);
        }
    }

    public function allAgentUsers(Request $request){
        $this->validate($request, [
            'agent_id' => 'required'
        ]);

        $property_ids = PropertyAgents::with('property.Valuecheck','property.Zillow','property.Homendo')->where('agent_id',$request->agent_id)->pluck('property_id')->toArray();

        $user_ids = [];
        $user_ids[] = PropertyOwners::whereIn('property_id', $property_ids)->pluck('user_id')->toArray();
        $user_ids[] = PropertyBuyers::whereIn('property_id', $property_ids)->pluck('buyer_id')->toArray();
        
        $all_user_ids = call_user_func_array('array_merge', $user_ids);
        
        if (sizeof($all_user_ids) > 0) {
            $all_users = Users::whereIn('uuid', array_unique($all_user_ids))->get();
        }else{
            return $this->sendResponse("Sorry, Users not found!", 200, false);
        }

        return $this->sendResponse($all_users);
    }

    public function agentPropertyVerification(Request $request){
        $this->validate($request, [
            'property_id' => 'required',
            'status' => 'required|in:YES,NO,VC'
        ]);

        $property = Properties::where('uuid', $request->property_id)->first();
        if (!empty($property)) {
            if ($request->status == 'YES') {
                Properties::where('uuid', $request->property_id)->update(['verified'=>'YES']);
                return $this->sendResponse("Property verified successfully!");
            }else{
                Properties::where('uuid', $request->property_id)->update(['verified'=>'VC']);
                return $this->sendResponse("Verification cancelled!", 200, false);
            } 
        }else{
            return $this->sendResponse("Sorry, Property not found!", 200, false);
        }
    }

    public function agentOwnerVerification(Request $request){
        $this->validate($request, [
            'user_id' => 'required',
            'property_id' => 'required',
            'status' => 'required|in:YES,NO,VC'
        ]);

        $user = PropertyOwners::where(['user_id'=>$request->user_id, 'property_id'=>$request->property_id])->first();

        if (!empty($user)) {
            if ($request->status == 'YES') {
                PropertyOwners::where(['user_id'=>$request->user_id, 'property_id'=>$request->property_id])->update(['verify_status'=>'YES']);
                return $this->sendResponse("User verified successfully!");
            }else{
                PropertyOwners::where(['user_id'=>$request->user_id, 'property_id'=>$request->property_id])->update(['verify_status'=>'VC']);
                return $this->sendResponse("Verification cancelled!");
            }

            PropertyVerification::where(['user_id'=>$request->user_id, 'property_id'=>$request->property_id])->delete();
        }else{
            return $this->sendResponse("Sorry, User not found!", 200, false);
        }
    }

    public function getClientShowings(Request $request){
        $this->validate($request, [
            'client_id' => 'required'
        ]);

        $showings = [];
        $future_bookings = [];
        $past_bookings = [];
        $today_bookings = [];
        $all_buyers_showing = null;
        $all_sellers_showing = null;
        $buyers_showings = PropertyBookingSchedule::with('Property', 'Client', 'Agent.agentInfo')->where('buyer_id', $request->client_id)->get();
        if (sizeof($buyers_showings) > 0) {
            foreach ($buyers_showings as $b_showing) {
                if (strtotime(date('Y-m-d')) < strtotime($b_showing->booking_date)) {
                    $future_bookings[] = $b_showing;
                }else if (strtotime(date('Y-m-d')) > strtotime($b_showing->booking_date)) {
                    $past_bookings[] = $b_showing;
                }else if (strtotime(date('Y-m-d')) == strtotime($b_showing->booking_date)) {
                    $today_bookings[] = $b_showing;
                }
            }
            $all_buyers_showing = array('future' => $future_bookings, 'past' => $past_bookings, 'today' => $today_bookings);
        }

        $seller_properties = PropertyOwners::where('user_id', $request->client_id)->distinct('property_id')->pluck('property_id')->toArray();

        $sellers_showings = PropertyBookingSchedule::with('Property', 'Client', 'Agent.agentInfo')->whereIn('property_id', $seller_properties)->get();
        if (sizeof($sellers_showings) > 0) {
            foreach ($sellers_showings as $s_showing) {
                if (strtotime(date('Y-m-d')) < strtotime($s_showing->booking_date)) {
                    $future_bookings[] = $s_showing;
                }else if (strtotime(date('Y-m-d')) > strtotime($s_showing->booking_date)) {
                    $past_bookings[] = $s_showing;
                }else if (strtotime(date('Y-m-d')) == strtotime($s_showing->booking_date)) {
                    $today_bookings[] = $s_showing;
                }
            }
            $all_sellers_showing = array('future' => $future_bookings, 'past' => $past_bookings, 'today' => $today_bookings);
        }

        if ($all_buyers_showing != '' || $all_sellers_showing != '') {
            $showings['buyers_showing'] = $all_buyers_showing;
            $showings['sellers_showing'] = $all_sellers_showing;
            return $this->sendResponse($showings);
        }else{
            return $this->sendResponse("Sorry, Showings not found!", 200, false);
        }
    }

    public function hideAgentProperty(Request $request){
        $this->validate($request, [
            'agent_id' => 'required',
            'property_id' => 'required'
        ]);

        $hide_property = PropertyAgents::where(['agent_id'=>$request->agent_id, 'property_id'=>$request->property_id])->update(['status'=>'hide']);

        if ($hide_property) {
            return $this->sendResponse("Property hided!");
        }else{
            return $this->sendResponse("Sorry, Property not found or Something went wrong!", 200, false);
        }
    }
    public function unhideAgentProperty(Request $request){
        $this->validate($request, [
            'agent_id' => 'required',
            'property_id' => 'required'
        ]);

        $unhide_property = PropertyAgents::where(['agent_id'=>$request->agent_id, 'property_id'=>$request->property_id])->update(['status'=>'show']);

        if ($unhide_property) {
            return $this->sendResponse("Property unhided!");
        }else{
            return $this->sendResponse("Sorry, Property not found or Something went wrong!", 200, false);
        }
    }
}