<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\Users;
use App\Models\PropertyShowingSetup;
use App\Models\PropertyShowingSurvey;
use Carbon\Carbon;
use DB;

class SuperAdminController extends Controller
{
		public function allAgents(Request $request){
				
				$all_agents = Users::with('agentInfo')->where('role', 'AGENT')->paginate(10);

				if (sizeof($all_agents) > 0) {
						return $this->sendResponse($all_agents);
				}else {
						return $this->sendResponse("Sorry, Agents not found!", 200, false);
				}
		}

		public function allUsers(Request $request){
				
				$all_users = Users::paginate(10);

				if (sizeof($all_users) > 0) {
						return $this->sendResponse($all_users);
				}else {
						return $this->sendResponse("Sorry, Users not found!", 200, false);
				}
		}

		public function allProperties(Request $request){
				
				$all_properties = Properties::get();

				if (sizeof($all_properties) > 0) {
						return $this->sendResponse($all_properties);
				}else {
						return $this->sendResponse("Sorry, Properties not found!", 200, false);
				}
		}

		public function allShowings(Request $request){
				
				$all_showings = PropertyShowingSetup::get();

				if (sizeof($all_showings) > 0) {
						return $this->sendResponse($all_showings);
				}else {
						return $this->sendResponse("Sorry, Showings not found!", 200, false);
				}
		}

		public function allSurveys(Request $request){
				
				$all_surveys = PropertyShowingSurvey::with('showing')->get();

				if (sizeof($all_surveys) > 0) {
						return $this->sendResponse($all_surveys);
				}else {
						return $this->sendResponse("Sorry, Surveys not found!", 200, false);
				}
		}
}