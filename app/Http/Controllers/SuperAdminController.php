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
				
				$all_properties = Properties::with('propertySellers.User', 'Valuecheck', 'Zillow', 'Homendo')->paginate(10);

				if (sizeof($all_properties) > 0) {
						return $this->sendResponse($all_properties);
				}else {
						return $this->sendResponse("Sorry, Properties not found!", 200, false);
				}
		}

		public function allShowings(Request $request){
				
				$all_showings = PropertyShowingSetup::get();

				$showings = [];
        $future_bookings = [];
        $past_bookings = [];
        $today_bookings = [];
        
				if (sizeof($all_showings) > 0) {
						foreach ($all_showings as $showing) {
                if (strtotime(date('Y-m-d')) < strtotime($showing->booking_date)) {
                    $future_bookings[] = $showing;
                }else if (strtotime(date('Y-m-d')) > strtotime($showing->booking_date)) {
                    $past_bookings[] = $showing;
                }else if (strtotime(date('Y-m-d')) == strtotime($showing->booking_date)) {
                    $today_bookings[] = $showing;
                }
            }
            $showings = array('future' => $future_bookings, 'past' => $past_bookings, 'today' => $today_bookings);
						return $this->sendResponse($showings);
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