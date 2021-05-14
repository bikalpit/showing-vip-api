<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Properties;
use Carbon\Carbon;

class PropertiesController extends Controller
{
		public function addProperty(Request $request){
				$this->validate($request, [
	      		'mls_id' => 'required',
	          'agent_id' => 'required',
	          'property_verified' => 'required|in:P,VS,V',
	          'property_title' => 'required',
	          'property_type' => 'required',
	          'property_size' => 'required',
	          'property_status' => 'required',
	          'property_year_built' => 'required',
	          'lat_area' => 'required',
	          'elementary' => 'required',
	          'middle' => 'required',
	          'high' => 'required',
	          'district' => 'required'
	          'phone' => 'required'
	          'office' => 'required'
	          'hoa' => 'required'
	          'taxes' => 'required'
	          'parking' => 'required'
	          'sources' => 'required'
	          'disclaimer' => 'required'
	      ]);

	      $time = strtotime(Carbon::now());
        $uuid = "prty".$time.rand(10,99)*rand(10,99);
	      $property = new Properties;
	      $property->uuid = $uuid;
	      $property->mls_id = $request->mls_id;
	      $property->agent_id = $request->agent_id;
	      $property->property_verified = $request->property_verified;
	      $property->property_title = $request->property_title;
	      $property->property_type = $request->property_type;
	      $property->property_size = $request->property_size;
	      $property->property_status = $request->property_status;
	      $property->property_year_built = $request->property_year_built;
	      $property->lat_area = $request->lat_area;
	      $property->elementary = $request->elementary;
	      $property->middle = $request->middle;
	      $property->high = $request->high;
	      $property->district = $request->district;
	      $property->phone = $request->phone;
	      $property->office = $request->office;
	      $property->hoa = $request->hoa;
	      $property->taxes = $request->taxes;
	      $property->parking = $request->parking;
	      $property->sources = $request->sources;
	      $property->disclaimer = $request->disclaimer;
	      $result = $property->save();

	      if ($result) {
	      		return $this->sendResponse("Property added successfully!");
	      }else{
	      		return $this->sendResponse("Sorry, Something went wrong!.",200,false);
	      }
		}
}