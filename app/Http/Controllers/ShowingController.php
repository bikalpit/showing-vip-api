<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyShowingSetup;
use App\Models\PropertyShowingAvailability;
use App\Models\PropertyShowingSurvey;
use App\Models\SurveyCategories;
use App\Models\SurveySubCategories;
use Carbon\Carbon;
use DB;

class ShowingController extends Controller
{
		public function createSlots(Request $request){
				$this->validate($request, [
	      		'interval' => 'required'
	      ]);

				$interval = $request->interval*60;

        $open_time = strtotime('00:00');
        $close_time = strtotime('24:00');

        $output = [];
        for( $i=$open_time; $i<$close_time; $i+=$interval) 
				{
            $output[] = date("h:i A", $i);
        }

        return $this->sendResponse($output);
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
	      		'name' => 'required'
	      ]);

				$sub_category = SurveySubCategories::where('uuid', $request->sub_category_id)->first();

				if ($sub_category) {
	      		$update_sub_category = SurveySubCategories::where('uuid', $request->sub_category_id)->update(['name'=>$request->name]);

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
	      		'property_id' => 'required',
	          'notification_email' => 'required',
	          'notification_text' => 'required',
	      		'type' => 'required|in:VALID,NO VALID',
	      		'validator' => 'required',
	      		'presence' => 'required',
	      		'instructions' => 'required',
	      		'lockbox_type' => 'required',
	      		'lockbox_location' => 'required',
	      		'start_date' => 'required',
	      		'end_date' => 'required',
	      		'timeframe' => 'required',
	      		'overlap' => 'required|in:YES,NO',
	      		'availability' => 'required',
	      		'survey' => 'required'
	      ]);

	      $time = strtotime(Carbon::now());

	      \DB::beginTransaction();
	      try{
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
			      $setup->start_date = $request->start_date;
			      $setup->end_date = $request->end_date;
			      $setup->timeframe = $request->timeframe;
			      $setup->overlap = $request->overlap;
			      $save_setup = $setup->save();

		        $availability_uuid = "avlb".$time.rand(10,99)*rand(10,99);
			      $availability = new PropertyShowingAvailability;
			      $availability->uuid = $availability_uuid;
			      $availability->showing_setup_id = $setup_uuid;
			      $availability->availability = json_encode($request->availability);
			      $save_availability = $availability->save();

			      $survey_uuid = "srvy".$time.rand(10,99)*rand(10,99);
			      $survey = new PropertyShowingSurvey;
			      $survey->uuid = $survey_uuid;
			      $survey->showing_setup_id = $setup_uuid;
			      $survey->survey = json_encode($request->survey);
			      $save_survey = $survey->save();

				  DB::commit();
			      return $this->sendResponse("Showing setup created successfully!");
	      } catch(\Exception $e) {
	      		\DB::rollBack();
	      		return $this->sendResponse("Sorry, Something went wrong!", 200, false);
	      }
    }

    public function updateShowingSetup(Request $request){
	    	$this->validate($request, [
	      		'showing_setup_id' => 'required',
	          'notification_email' => 'required',
	          'notification_text' => 'required',
	      		'type' => 'required|in:VALID,NO VALID',
	      		'validator' => 'required',
	      		'presence' => 'required',
	      		'instructions' => 'required',
	      		'lockbox_type' => 'required',
	      		'lockbox_location' => 'required',
	      		'start_date' => 'required',
	      		'end_date' => 'required',
	      		'timeframe' => 'required',
	      		'overlap' => 'required|in:YES,NO',
	      		'availability' => 'required',
	      		'survey' => 'required'
	      ]);

	    	$showing_setup = PropertyShowingSetup::where('uuid', $request->showing_setup_id)->first();

	    	if ($showing_setup) {
			      $update_setup = PropertyShowingSetup::where('uuid', $request->showing_setup_id)->update([
				      	'notification_email'=>$request->notification_email,
				      	'notification_text'=>$request->notification_text,
				      	'type'=>$request->type,
				      	'validator'=>$request->validator,
				      	'presence'=>$request->presence,
				      	'instructions'=>$request->instructions,
				      	'lockbox_type'=>$request->lockbox_type,
				      	'lockbox_location'=>$request->lockbox_location,
				      	'start_date'=>$request->start_date,
				      	'end_date'=>$request->end_date,
				      	'timeframe'=>$request->timeframe,
				      	'overlap'=>$request->overlap,
			      ]);

			      $update_availibility = PropertyShowingAvailability::where('showing_setup_id', $request->showing_setup_id)->update([
			      		'availability'=>json_encode($request->availability)
			      ]);

			      $update_survey = PropertyShowingSurvey::where('showing_setup_id', $request->showing_setup_id)->update([
			      		'survey'=>json_encode($request->survey)
			      ]);

			      return $this->sendResponse("Showing setup updated successfully!");
	    	}else{
	    			return $this->sendResponse("Sorry, Showing setup not found!", 200, false);
	    	}
    }

    public function getSingleShowingSetup(Request $request){
    		$this->validate($request, [
	      		'property_id' => 'required'
	      ]);

	      $showing_setup = PropertyShowingSetup::with('showingAvailability', 'showingSurvey')->where('property_id', $request->property_id)->first();

	      if ($showing_setup) {
	      		return $this->sendResponse($showing_setup);
	      }else{
	      		return $this->sendResponse("Sorry, Showing setup not found!", 200, false);
	      }
    }
}