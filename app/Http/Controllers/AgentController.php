<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\Properties;
use App\Models\PropertyAgents;
use App\Models\UserAgents;
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

        dd($request->properties);
    }

    public function addClient(Request $request){
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'role' => 'required',
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
        }
    }
}