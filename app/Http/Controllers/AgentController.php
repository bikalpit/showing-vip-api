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
    public function getClientWithProperty(Request $request)
    {
        $this->validate($request, [
            'agent_id' => 'required'
        ]);
        $result = PropertyAgents::with('owner','property')->where('agent_id',$request->agent_id)->get();
        if($result){
            return $this->sendResponse($result);
        }else{
            return $this->sendResponse("No Properties found!.",200,false);
        }
    }
    public function getSingleClient(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required',
            'agent_id'  => 'required'
        ]);
        $properteyIds = PropertyAgents::where(['agent_id'=>$request->agent_id,'user_id'=>$request->client_id])
                        ->pluck('property_id')->toArray();
        $result = Users::where('uuid',$request->client_id)->first();
        if($result){
            $allProperty = Properties::whereIn('uuid',$properteyIds)->get();
            $finalArray = ['profile'=>$result,'property_list'=>$allProperty];
            return $this->sendResponse($finalArray);
        }else{
            return $this->sendResponse("Sorry!Something Wrong!.",200,false);
        }
    }
}