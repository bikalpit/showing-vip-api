<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\Properties;
use App\Models\PropertyAgents;
use App\Models\UserAgents;
use App\Models\PropertyValuecheck;
use App\Models\PropertyZillow;
use App\Models\PropertyHomendo;
use Carbon\Carbon;
class AgentController extends Controller
{
    public function getClientWithProperty(Request $request){
        $this->validate($request, [
            'agent_id' => 'required'
        ]);

        $result = PropertyAgents::with('owner','property')->where('agent_id',$request->agent_id)->get();
        if ($result) {
            return $this->sendResponse($result);
        }else{
            return $this->sendResponse("No Properties found!.",200,false);
        }
    }

    public function getSingleClient(Request $request){
        $this->validate($request, [
            'client_id' => 'required',
            'agent_id'  => 'required'
        ]);

        $properteyIds = PropertyAgents::where(['agent_id'=>$request->agent_id,'user_id'=>$request->client_id])
                        ->pluck('property_id')->toArray();
        $result = Users::where('uuid',$request->client_id)->first();
        if ($result) {
            $allProperty = Properties::with('Valuecheck', 'Zillow', 'Homendo')->whereIn('uuid',$properteyIds)->get();
            $finalArray = ['profile'=>$result,'property_list'=>$allProperty];
            return $this->sendResponse($finalArray);
        }else{
            return $this->sendResponse("Sorry!Something Wrong!.",200,false);
        }
    }

    public function GetRandomAgents(Request $request){
        $this->validate($request, [
            'number' => 'required'
        ]);

        $skip = $request->number;
        $result = Users::with('agentInfo')->where('role','AGENT')->skip($skip)->take(4)->get();
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

        $agents = UserAgents::where('user_id',$request->user_id)->pluck('agent_id')->toArray();
        if (sizeof($agents)>0) {
            $result = Users::with('agentInfo')->whereIn('uuid',$agents)->get();
            return $this->sendResponse($result);
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
            foreach ($all_properties as $property) {
                $check = 0;
                if ($property->mls_id == $new_property['hmdo_mls_id'][1]) {
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
                $property->data = json_encode($new_property);
                $property->verified = 'NO';
                $property->last_update = date('Y-m-d H:i:s');
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
                $property_agent->user_id = $uuid;
                $property_agent->agent_id = $request->agent_id;
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
            'property_id' => 'required',
            'url' => 'required'
        ]);

        $email_check = Users::where('email', $request->email)->first();
        $phone_check = Users::where('phone', $request->phone)->first();
        $property = Properties::where('uuid', $request->property_id)->first();

        if ($email_check !== null) {
            return $this->sendResponse("Sorry, Email already exist!", 200, false);
        }elseif ($phone_check !== null) {
            return $this->sendResponse("Sorry, Phone no. already exist!", 200, false);
        }else{
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
        }
    }
}