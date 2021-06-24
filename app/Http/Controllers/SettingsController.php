<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Settings;
use Carbon\Carbon;

class SettingsController extends Controller
{
    
    public function createSetting(Request $request)
    {
        $this->validate($request, [
            'option_key' => 'required',
            'option_value' => 'required'
        ]);

        $firstCheck = Settings::where('option_key',$request->option_key)->first();
        if($firstCheck){
            return $this->sendResponse("setting already exists!.",200,false);
        }

        if ($request->option_key == 'design_element') {
            if ($request->option_value['bg_image'] !== '') {
                $path = app()->basePath('public/setting-images/');
                $fileName = $this->singleImageUpload($path, $request->option_value['bg_image']);
                $option_value = $request->option_value;
                $option_value['bg_image'] = env('APP_URL').'public/setting-images/'.$fileName;
                $request->merge([
                    'option_value' => $option_value,
                ]);
            }
        }
        
        $time = strtotime(Carbon::now());
        $uuid = "set".$time.rand(10,99)*rand(10,99);

        $settings = new Settings;
        $settings->uuid = $uuid;
        $settings->option_key = $request->option_key;
        $settings->option_value = json_encode($request->option_value);
        if($settings->save()){
            return $this->sendResponse("Setting created!");
        }else{
            return $this->sendResponse("Something wrong!",200,false);
        }
    }

    public function getSingleSetting(Request $request)
    {
        $this->validate($request, [
            'option_key' => 'required'
        ]);
        $result = Settings::where('option_key',$request->option_key)->first();
        if($result){
            return $this->sendResponse($result);
        }else{
            return $this->sendResponse("Sorry, Setting not found!",200,false);
        }
    }

    public function getAllSetting(Request $request)
    {
        $result = Settings::all();
        if($result){
            return $this->sendResponse($result);
        }else{
            return $this->sendResponse("setting not found!",200,false);
        }
    }

    public function updateSetting(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required',
            'option_value'=>'required'
        ]);

        $setting = Settings::where('uuid', $request->uuid)->first();
        $setting_option_value = json_decode($setting->option_value);
        $setting_bg_image = $setting_option_value->bg_image;
        $image_name = substr($setting_bg_image, strrpos($setting_bg_image, '/') + 1);
        //dd($image_name);
        if ($setting->option_key == 'design_element') {
            if ($request->option_value['bg_image'] !== '') {
                $path = app()->basePath('public/setting-images/');
                $fileName = $this->singleImageUpload($path, $request->option_value['bg_image']);
                unlink($path.'/'.$image_name);
                $option_value = $request->option_value;
                $option_value['bg_image'] = env('APP_URL').'public/setting-images/'.$fileName;
            }else{
                $option_value['bg_image'] = $setting_bg_image;
            }

            $request->merge([
                'option_value' => $option_value,
            ]);
        }

        $update['option_value'] = json_encode($request->option_value);
        $result = Settings::where('uuid',$request->uuid)->update($update);
        if($result){
            return $this->sendResponse("Setting Updated!");
        }else{
            return $this->sendResponse("Something wrong!",200,false);
        }
    }
}