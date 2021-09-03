<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\PropertyBookingSchedule;
use App\Models\PropertyShowingSetup;
use App\Models\PropertyShowingAvailability;
use App\Models\Properties;
use App\Models\PropertyHomendo;
use App\Models\PropertyBuyers;
use App\Models\ShowingFeedback;
use App\Models\SurveySubCategories;
use App\Models\SurveyCategories;
use App\Models\Settings;
use App\Models\PropertyAgents;
use App\Models\PropertyOwners;
use App\Mail\SignupMail;
use App\Mail\BookingMail;
use App\Mail\BookingUpdateMail;
use App\Mail\UpdateShowingMail;
use App\Mail\AgentShowingMail;
use Carbon\Carbon;
use Twilio\Rest\Client as TwilioClient;

class BookingScheduleController extends Controller
{
    public function createBooking(Request $request){
        $this->validate($request, [
            'first_name'            => 'nullable',
            'last_name'             => 'nullable',
            'phone'                 => 'nullable',
            'email'                 => 'nullable',
            'property_id'           => 'nullable',
            'property_mls_id'       => 'nullable',
            'property_originator'   => 'nullable',
            'booking_date'          => 'nullable',
            'booking_time'          => 'nullable',
            'booking_slots'         => 'nullable',
            'showing_note'          => 'nullable',
            'buyer_id'              => 'nullable',
            'seller_agent_id'       => 'nullable',
            'buyer_agent_id'        => 'nullable',
            'url'                   => 'nullable',
            'interval'              => 'nullable',
        ]);

        $formetted_date = date('Y-m-d', strtotime($request->booking_date));
        $phone = $request->phone;
        $email = $request->email;
        $property_id = $request->property_id;
        $booking_date = $request->booking_date;
        $booking_time = $request->booking_time;
        $property = Properties::where('uuid', $property_id)->first();
        $homendo = PropertyHomendo::where('property_id', $property_id)->first();
        if ($homendo != null || $homendo != '') {
            $hmdo_mls_propname = $homendo->hmdo_mls_propname;
        }else{
            $hmdo_mls_propname = '';
        }
        $showing_setup = PropertyShowingSetup::where('property_id', $property_id)->first();
        if ($showing_setup != null || $showing_setup != '') {
            if ($showing_setup->validator != null || $showing_setup->validator != '') {
                $validator = $showing_setup->validator[0];
            }else{
                $validator = '';
            }

            $availibility = PropertyShowingAvailability::where('showing_setup_id', $showing_setup->uuid)->first();
            if($availibility !== null){
                $get_availibility = json_decode($availibility);
                $availibility_data = json_decode($get_availibility->availability);
            }
        }else{
            $validator = '';
        }
        
        $settings = Settings::where('option_key', 'twillio')->first();
        $twilio_setting = json_decode($settings->option_value);

        if ($request->buyer_id !== null && $request->buyer_id !== '') {
            $users = Users::where('uuid',$request->buyer_id)->first();
            $time = strtotime(Carbon::now());
            $uuid = "sch".$time.rand(10,99)*rand(10,99);
            $propertyBookingSchedule = new PropertyBookingSchedule;
            $propertyBookingSchedule->uuid = $uuid;
            $propertyBookingSchedule->buyer_id = $users->uuid;
            $propertyBookingSchedule->property_id = $property_id;
            $propertyBookingSchedule->property_mls_id = $request->property_mls_id;
            $propertyBookingSchedule->property_originator = $request->property_originator;
            $propertyBookingSchedule->booking_date = $formetted_date;
            $propertyBookingSchedule->booking_time = $booking_time;
            $propertyBookingSchedule->booking_slots = json_encode($request->booking_slots);
            if ($showing_setup != null || $showing_setup != '') {
                if ($showing_setup->type == 'VALID') {
                    $propertyBookingSchedule->status = 'P';
                }else{
                    $propertyBookingSchedule->status = 'A';
                }
            }else{
                $propertyBookingSchedule->status = 'P';
            }
            $propertyBookingSchedule->cv_status = 'verified';
            if ($request->seller_agent_id != '' || $request->seller_agent_id != null) {
                $propertyBookingSchedule->seller_agent_id = $request->seller_agent_id;
            }
            if ($request->buyer_agent_id != '' || $request->buyer_agent_id != null) {
                $propertyBookingSchedule->buyer_agent_id = $request->buyer_agent_id;
            }
            $propertyBookingSchedule->showing_note = $request->showing_note;
            $propertyBookingSchedule->interval = $request->interval;
            $propertyBookingSchedule->cancel_at = null;

            if ($propertyBookingSchedule->save()) {
                $check_buyer = PropertyBuyers::where(['buyer_id'=>$request->buyer_id, 'property_id'=>$property_id])->first();
                $check_seller = PropertyOwners::where(['property_id'=>$property_id, 'type'=>'main_owner'])->orWhere('property_id', $property_id)->first();
                if (!empty($check_seller)) {
                    $seller_id = $check_seller->user_id;
                }else{
                    $seller_id = null;
                }
                if (empty($check_buyer)) {
                    $property_buyer = new PropertyBuyers;
                    $property_buyer->property_id = $property_id;
                    //$property_buyer->seller_id = $seller_id;
                    $property_buyer->buyer_id = $request->buyer_id;
                    if ($request->buyer_agent_id != '' || $request->buyer_agent_id != null) {
                        $property_buyer->agent_id = $request->buyer_agent_id;
                    }
                    $property_buyer->save();
                }

                if ($request->seller_agent_id !== null) {
                    if ($property_id !== null) {
                        $check_agent = PropertyAgents::where(['property_id'=>$property_id, 'agent_id'=>$request->seller_agent_id, 'agent_type'=>'seller'])->first();
                        $check_seller = PropertyOwners::where(['property_id'=>$property_id, 'type'=>'main_owner'])->orWhere('property_id', $property_id)->first();
                        if (!empty($check_seller)) {
                            $seller_id = $check_seller->user_id;
                        }else{
                            $seller_id = null;
                        }
                        if (empty($check_agent)) {
                            $property_agent = new PropertyAgents;
                            $property_agent->property_id = $property_id;
                            $property_agent->property_mls_id = $request->property_mls_id;
                            $property_agent->property_originator = $request->property_originator;
                            //$property_agent->seller_id = $seller_id;
                            $property_agent->buyer_id = $request->buyer_id;
                            $property_agent->agent_id = $request->seller_agent_id;
                            $property_agent->agent_type = 'seller';
                            $property_agent->save();
                        }
                    }else{
                        $check_agent = PropertyAgents::where(['property_mls_id'=>$request->property_mls_id, 'property_originator'=>$request->property_originator, 'agent_id'=>$request->seller_agent_id, 'agent_type'=>'seller'])->first();
                        $check_property = PropertyHomendo::where(['hmdo_mls_id'=>$request->property_mls_id, 'hmdo_mls_originator'=>$request->property_originator])->first();
                        if (!empty($check_property)) {
                            $check_seller = PropertyOwners::where(['property_id'=>$check_property->property_id, 'type'=>'main_owner'])->orWhere('property_id', $check_property->property_id)->first();
                            if (!empty($check_seller)) {
                                $seller_id = $check_seller->user_id;
                            }else{
                                $seller_id = null;
                            }
                            $property_id = $check_property->property_id;
                        }else{
                            $seller_id = null;
                            $property_id = null;
                        }
                        if (empty($check_agent)) {
                            $property_agent = new PropertyAgents;
                            $property_agent->property_id = $property_id;
                            $property_agent->property_mls_id = $request->property_mls_id;
                            $property_agent->property_originator = $request->property_originator;
                            //$property_agent->seller_id = $seller_id;
                            $property_agent->buyer_id = $request->buyer_id;
                            $property_agent->agent_id = $request->seller_agent_id;
                            $property_agent->agent_type = 'seller';
                            $property_agent->save();
                        }
                    }
                }

                if ($request->buyer_agent_id !== null) {
                    if ($property_id !== null) {
                        $check_agent = PropertyAgents::where(['property_id'=>$property_id, 'agent_id'=>$request->buyer_agent_id, 'agent_type'=>'buyer'])->first();
                        $check_seller = PropertyOwners::where(['property_id'=>$property_id, 'type'=>'main_owner'])->orWhere('property_id', $property_id)->first();
                        if (!empty($check_seller)) {
                            $seller_id = $check_seller->user_id;
                        }else{
                            $seller_id = null;
                        }
                        if (empty($check_agent)) {
                            $property_agent = new PropertyAgents;
                            $property_agent->property_id = $property_id;
                            $property_agent->property_mls_id = $request->property_mls_id;
                            $property_agent->property_originator = $request->property_originator;
                            //$property_agent->seller_id = $seller_id;
                            $property_agent->buyer_id = $request->buyer_id;
                            $property_agent->agent_id = $request->buyer_agent_id;
                            $property_agent->agent_type = 'buyer';
                            $property_agent->save();
                        }
                    }else{
                        $check_agent = PropertyAgents::where(['property_mls_id'=>$request->property_mls_id, 'property_originator'=>$request->property_originator, 'agent_id'=>$request->buyer_agent_id, 'agent_type'=>'buyer'])->first();
                        $check_property = PropertyHomendo::where(['hmdo_mls_id'=>$request->property_mls_id, 'hmdo_mls_originator'=>$request->property_originator])->first();
                        if (!empty($check_property)) {
                            $check_seller = PropertyOwners::where(['property_id'=>$check_property->property_id, 'type'=>'main_owner'])->orWhere('property_id', $check_property->property_id)->first();
                            if (!empty($check_seller)) {
                                $seller_id = $check_seller->user_id;
                            }else{
                                $seller_id = null;
                            }
                            $property_id = $check_property->property_id;
                        }else{
                            $seller_id = null;
                            $property_id = null;
                        }
                        if (empty($check_agent)) {
                            $property_agent = new PropertyAgents;
                            $property_agent->property_id = $property_id;
                            $property_agent->property_mls_id = $request->property_mls_id;
                            $property_agent->property_originator = $request->property_originator;
                            //$property_agent->seller_id = $seller_id;
                            $property_agent->buyer_id = $request->buyer_id;
                            $property_agent->agent_id = $request->buyer_agent_id;
                            $property_agent->agent_type = 'buyer';
                            $property_agent->save();
                        }
                    }
                }

                if ($showing_setup != null || $showing_setup != '') {
                    if ($availibility !== null) {
                        foreach ($availibility_data as $data) {
                            if ($data->date == date('F d l', strtotime($booking_date))) {
                                foreach ($data->slots as $slot) {
                                    /*if ($slot->slot == date('H:i A', strtotime($booking_time))) {
                                        $slot->status = 'booked';
                                    }*/
                                    foreach ($request->booking_slots as $booking_slot) {
                                        if ($slot->slot == date('H:i A', strtotime($booking_slot))) {
                                            $slot->status = 'booked';
                                        }
                                    }
                                }
                            }
                        }
                        PropertyShowingAvailability::where('showing_setup_id', $showing_setup->uuid)->update(['availability'=>json_encode($availibility_data)]);
                    }

                    if ($showing_setup->validator != null || $showing_setup->validator != '') {
                        if ($twilio_setting->status == true) {
                            try {
                                $this->twilioClient = new TwilioClient($twilio_setting->account_sid, $twilio_setting->auth_token);
                                $message =  $this->twilioClient->messages->create(
                                    $validator->phone,
                                    array(
                                        "from" => $twilio_setting->twilio_sender_number,
                                        "body" => 'Hi '.$validator->first_name.' '.$validator->last_name.', '.$users->first_name.' '.$users->last_name.' want to visit your property on '.$request->booking_date.' '.$request->booking_time
                                    )
                                );
                            } catch(\Exception $e) {

                            }
                        }

                        $this->configSMTP();
                        $mail_data = [
                            'name'=>$users->first_name.' '.$users->last_name,
                            'validator_name'=>$validator->first_name.' '.$validator->last_name,
                            'property_name'=>$hmdo_mls_propname,
                            'booking_date'=>$request->booking_date,
                            'booking_time'=>$request->booking_time,
                            'booking_id'=>base64_encode($uuid),
                            'validator_id'=>base64_encode($validator->uuid),
                            'booker_id'=>base64_encode($request->buyer_id)
                        ];

                        try {
                            Mail::to($validator->email)->send(new BookingMail($mail_data));
                        } catch(\Exception $e) {
                            /*$msg = $e->getMessage();
                            return $this->sendResponse($msg, 200, false);*/
                        }
                    }
                }

                if ($request->agent_mls_id != '' || $request->agent_originator != '') {
                    $agent = Users::where(['mls_id'=>$request->agent_mls_id, 'mls_name'=>$request->agent_originator, 'email'=>$request->agent_email])->first();
                    $this->configSMTP();
                    $data = [
                        'name' => $agent->first_name.' '.$agent->last_name,
                        'mls_id' => $request->property_mls_id,
                        'originator' => $request->property_originator
                    ];

                    try{
                        Mail::to($agent->email)->send(new AgentShowingMail($data));
                    }catch(\Exception $e){
                        /*$msg = $e->getMessage();
                        return $this->sendResponse($msg, 200, false);*/
                    }
                }
                return $this->sendResponse("Showing booked successfully!");
            }else{
                return $this->sendResponse("Sorry, Something went wrong!", 200, false);
            }
        }else{
            $email_check = Users::where('email', $request->email)->first();
            if (!empty($email_check)) {
                return $this->sendResponse("Email already exists!", 200, false);
            }

            $phone_check = Users::where('phone', $request->phone)->first();
            if (!empty($phone_check)) {
                return $this->sendResponse("Phone no. already exists!", 200, false);
            }

            $time = strtotime(Carbon::now());
            $buyer_uuid = "usr".$time.rand(10,99)*rand(10,99);
            $user = new Users;
            $user->uuid = $buyer_uuid;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->role = "USER";
            $user->sub_role = "BUYER";
            $user->phone_verified = "NO";
            $user->email_verified = "NO";
            $user->image = "default.png";
            $result = $user->save();
            if ($result) {
                $uuid = "sch".$time.rand(10,99)*rand(10,99);
                $propertyBookingSchedule = new PropertyBookingSchedule;
                $propertyBookingSchedule->uuid = $uuid;
                $propertyBookingSchedule->buyer_id = $user->uuid;
                $propertyBookingSchedule->property_id = $property_id;
                $propertyBookingSchedule->property_mls_id = $request->property_mls_id;
                $propertyBookingSchedule->property_originator = $request->property_originator;
                $propertyBookingSchedule->booking_date = $formetted_date;
                $propertyBookingSchedule->booking_time = $booking_time;
                $propertyBookingSchedule->booking_slots = json_encode($request->booking_slots);
                $propertyBookingSchedule->status = 'P';
                $propertyBookingSchedule->cv_status = 'on-hold';
                if ($request->seller_agent_id != '' || $request->seller_agent_id != null) {
                    $propertyBookingSchedule->seller_agent_id = $request->seller_agent_id;
                }
                if ($request->buyer_agent_id != '' || $request->buyer_agent_id != null) {
                    $propertyBookingSchedule->buyer_agent_id = $request->buyer_agent_id;
                }
                $propertyBookingSchedule->showing_note = $request->showing_note;
                $propertyBookingSchedule->interval = $request->interval;
                $propertyBookingSchedule->cancel_at = null;

                if ($propertyBookingSchedule->save()) {
                    $check_buyer = PropertyBuyers::where(['buyer_id'=>$buyer_uuid, 'property_id'=>$property_id])->first();
                    $check_seller = PropertyOwners::where(['property_id'=>$property_id, 'type'=>'main_owner'])->orWhere('property_id', $property_id)->first();
                    if (!empty($check_seller)) {
                        $seller_id = $check_seller->user_id;
                    }else{
                        $seller_id = null;
                    }
                    if (empty($check_buyer)) {
                        $property_buyer = new PropertyBuyers;
                        $property_buyer->property_id = $property_id;
                        //$property_buyer->seller_id = $seller_id;
                        $property_buyer->buyer_id = $buyer_uuid;
                        if ($request->buyer_agent_id != '' || $request->buyer_agent_id != null) {
                            $property_buyer->agent_id = $request->buyer_agent_id;
                        }
                        $property_buyer->save();
                    }

                    if ($request->seller_agent_id !== null) {
                        if ($property_id !== null) {
                            $check_agent = PropertyAgents::where(['property_id'=>$property_id, 'agent_id'=>$request->seller_agent_id, 'agent_type'=>'seller'])->first();
                            $check_seller = PropertyOwners::where(['property_id'=>$property_id, 'type'=>'main_owner'])->orWhere('property_id', $property_id)->first();
                            if (!empty($check_seller)) {
                                $seller_id = $check_seller->user_id;
                            }else{
                                $seller_id = null;
                            }
                            if (empty($check_agent)) {
                                $property_agent = new PropertyAgents;
                                $property_agent->property_id = $property_id;
                                $property_agent->property_mls_id = $request->property_mls_id;
                                $property_agent->property_originator = $request->property_originator;
                                //$property_agent->seller_id = $seller_id;
                                $property_agent->buyer_id = $request->buyer_id;
                                $property_agent->agent_id = $request->seller_agent_id;
                                $property_agent->agent_type = 'seller';
                                $property_agent->save();
                            }
                        }else{
                            $check_agent = PropertyAgents::where(['property_mls_id'=>$request->property_mls_id, 'property_originator'=>$request->property_originator, 'agent_id'=>$request->seller_agent_id, 'agent_type'=>'seller'])->first();
                            $check_property = PropertyHomendo::where(['hmdo_mls_id'=>$request->property_mls_id, 'hmdo_mls_originator'=>$request->property_originator])->first();
                            if (!empty($check_property)) {
                                $check_seller = PropertyOwners::where(['property_id'=>$check_property->property_id, 'type'=>'main_owner'])->orWhere('property_id', $check_property->property_id)->first();
                                if (!empty($check_seller)) {
                                    $seller_id = $check_seller->user_id;
                                }else{
                                    $seller_id = null;
                                }
                            }else{
                                $seller_id = null;
                            }
                            if (empty($check_agent)) {
                                $property_agent = new PropertyAgents;
                                $property_agent->property_id = $property_id;
                                $property_agent->property_mls_id = $request->property_mls_id;
                                $property_agent->property_originator = $request->property_originator;
                                //$property_agent->seller_id = $seller_id;
                                $property_agent->buyer_id = $request->buyer_id;
                                $property_agent->agent_id = $request->seller_agent_id;
                                $property_agent->agent_type = 'seller';
                                $property_agent->save();
                            }
                        }
                    }
                    
                    if ($request->buyer_agent_id !== null) {
                        if ($property_id !== null) {
                            $check_agent = PropertyAgents::where(['property_id'=>$property_id, 'agent_id'=>$request->buyer_agent_id, 'agent_type'=>'buyer'])->first();
                            $check_seller = PropertyOwners::where(['property_id'=>$property_id, 'type'=>'main_owner'])->orWhere('property_id', $property_id)->first();
                            if (!empty($check_seller)) {
                                $seller_id = $check_seller->user_id;
                            }else{
                                $seller_id = null;
                            }
                            if (empty($check_agent)) {
                                $property_agent = new PropertyAgents;
                                $property_agent->property_id = $property_id;
                                $property_agent->property_mls_id = $request->property_mls_id;
                                $property_agent->property_originator = $request->property_originator;
                                //$property_agent->seller_id = $seller_id;
                                $property_agent->buyer_id = $request->buyer_id;
                                $property_agent->agent_id = $request->buyer_agent_id;
                                $property_agent->agent_type = 'buyer';
                                $property_agent->save();
                            }
                        }else{
                            $check_agent = PropertyAgents::where(['property_mls_id'=>$request->property_mls_id, 'property_originator'=>$request->property_originator, 'agent_id'=>$request->buyer_agent_id, 'agent_type'=>'buyer'])->first();
                            $check_property = PropertyHomendo::where(['hmdo_mls_id'=>$request->property_mls_id, 'hmdo_mls_originator'=>$request->property_originator])->first();
                            if (!empty($check_property)) {
                                $check_seller = PropertyOwners::where(['property_id'=>$check_property->property_id, 'type'=>'main_owner'])->orWhere('property_id', $check_property->property_id)->first();
                                if (!empty($check_seller)) {
                                    $seller_id = $check_seller->user_id;
                                }else{
                                    $seller_id = null;
                                }
                            }else{
                                $seller_id = null;
                            }
                            if (empty($check_agent)) {
                                $property_agent = new PropertyAgents;
                                $property_agent->property_id = $property_id;
                                $property_agent->property_mls_id = $request->property_mls_id;
                                $property_agent->property_originator = $request->property_originator;
                                //$property_agent->seller_id = $seller_id;
                                $property_agent->buyer_id = $request->buyer_id;
                                $property_agent->agent_id = $request->buyer_agent_id;
                                $property_agent->agent_type = 'buyer';
                                $property_agent->save();
                            }
                        }
                    }

                    $verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
                    Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

                    $this->configSMTP();
                    $data = [
                        'name'=>$request->first_name.' '.$request->last_name, 
                        'verification_token'=>$verification_token, 
                        'email'=>$request->email,
                        'url'=>$request->url
                    ];
                    
                    try {
                        Mail::to($request->email)->send(new SignupMail($data));
                    } catch(\Exception $e) {
                        /*$msg = $e->getMessage();
                        return $this->sendResponse($msg, 200, false);*/
                    }

                    if ($showing_setup != null || $showing_setup != '') {
                        if ($availibility !== null) {
                            foreach ($availibility_data as $data) {
                                if ($data->date == date('F d l', strtotime($booking_date))) {
                                    foreach ($data->slots as $slot) {
                                        /*if ($slot->slot == date('H:i A', strtotime($booking_time))) {
                                            $slot->status = 'booked';
                                        }*/
                                        foreach ($request->booking_slots as $booking_slot) {
                                            if ($slot->slot == date('H:i A', strtotime($booking_slot))) {
                                                $slot->status = 'booked';
                                            }
                                        }
                                    }
                                }
                            }
                            PropertyShowingAvailability::where('showing_setup_id', $showing_setup->uuid)->update(['availability'=>json_encode($availibility_data)]);
                        }

                        if ($showing_setup->validator != null || $showing_setup->validator != '') {
                            if ($twilio_setting->status == true) {
                                try {
                                    $this->twilioClient = new TwilioClient($twilio_setting->account_sid, $twilio_setting->auth_token);
                                    $message =  $this->twilioClient->messages->create(
                                        $validator->phone,
                                        array(
                                            "from" => $twilio_setting->twilio_sender_number,
                                            "body" => 'Hi '.$validator->first_name.' '.$validator->last_name.', '.$request->first_name.' '.$request->last_name.' want to visit your property on '.$request->booking_date.' '.$request->booking_time
                                        )
                                    );
                                } catch(\Exception $e) {
                                    
                                }
                            }

                            $mail_data = [
                                'name'=>$request->first_name.' '.$request->last_name,
                                'validator_name'=>$validator->first_name.' '.$validator->last_name,
                                'property_name'=>$hmdo_mls_propname,
                                'booking_date'=>$request->booking_date,
                                'booking_time'=>$request->booking_time,
                                'booking_id'=>base64_encode($uuid),
                                'validator_id'=>base64_encode($validator->uuid),
                                'booker_id'=>base64_encode($buyer_uuid)
                            ];

                            try {
                                Mail::to($validator->email)->send(new BookingMail($mail_data));
                            } catch(\Exception $e) {
                                /*$msg = $e->getMessage();
                                return $this->sendResponse($msg, 200, false);*/
                            }
                        }
                    }

                    if ($request->agent_mls_id != '' || $request->agent_originator != '') {
                        $agent = Users::where(['mls_id'=>$request->agent_mls_id, 'mls_name'=>$request->agent_originator, 'email'=>$request->agent_email])->first();

                        $data = [
                            'name' => $agent->first_name.' '.$agent->last_name,
                            'mls_id' => $request->property_mls_id,
                            'originator' => $request->property_originator
                        ];

                        try{
                            Mail::to($agent->email)->send(new AgentShowingMail($data));
                        }catch(\Exception $e){
                            /*$msg = $e->getMessage();
                            return $this->sendResponse($msg, 200, false);*/
                        }
                    }
                    return $this->sendResponse("Showing booked successfully!");
                }else{
                    return $this->sendResponse("Sorry, Something went wrong!", 200, false);
                }
            }else{
                return $this->sendResponse("Sorry, Something went wrong!");
            }
        }
    }

    public function updateBooking(Request $request){
        $this->validate($request, [
            'booking_id'        => 'required',
            'booking_date'      => 'required',
            'booking_time'      => 'required',
            'user_id'           => 'nullable',
            'status'            => 'required|in:A,R,P',
            'reason'            => 'nullable',
            'keep_slot'         => 'required'
        ]);

        $user_id = $request->user_id;
        $id      = $request->booking_id;
        $status  = $request->status;
        $reason  = $request->reason;
        $user = Users::where('uuid', $user_id)->first();
        $booking = PropertyBookingSchedule::where('uuid', $id)->first();
        $validator = Users::where('uuid', $booking->buyer_id)->first();
        $property = Properties::where('uuid', $booking->property_id)->first();
        $homendo = PropertyHomendo::where('property_id', $booking->property_id)->first();
        $setup = PropertyShowingSetup::where('property_id', $booking->property_id)->first();
        $availibility = PropertyShowingAvailability::where('showing_setup_id', $setup->uuid)->first();
        $get_availibility = json_decode($availibility);
        $availibility_data = json_decode($get_availibility->availability);

        $settings = Settings::where('option_key', 'twillio')->first();
        $twilio_setting = json_decode($settings->option_value);

        if (!empty($booking)) {
            $update['status'] = $status;
            $update['cancel_by'] = $user->role;
            $update['cancel_reason'] = $reason;
            $update['cancel_at'] = date('Y-m-d H:i:s');
            $result = PropertyBookingSchedule::where('uuid',$id)->update($update);
            if ($status == 'A') {
                $msg = "Confirmed";
            }elseif ($status == 'R') {
                $msg = "Cancelled";
            }else{
                $msg = "Pending";
            }

            if ($result) {
                if ($request->keep_slot == 'close') {
                    foreach ($availibility_data as $data) {
                        if ($data->date == $request->booking_date) {
                            foreach ($data->slots as $slot) {
                                /*if ($slot->slot == $request->booking_time) {
                                    $slot->status = 'booked';
                                }else{
                                    $slot->status = 'confirm';
                                }*/
                                if ($booking->booking_slots != null || $booking->booking_slots != '') {
                                    foreach (json_decode($booking->booking_slots) as $booking_slot) {
                                        if ($slot->slot == $booking_slot) {
                                            $slot->status = 'booked';
                                        }else{
                                            $slot->status = 'confirm';
                                        }
                                    }
                                } 
                            }
                        }
                    }
                    PropertyShowingAvailability::where('showing_setup_id', $setup->uuid)->update(['availability'=>json_encode($availibility_data)]);
                }

                if ($twilio_setting->status == true) {
                    try {
                        $this->twilioClient = new TwilioClient($twilio_setting->account_sid, $twilio_setting->auth_token);
                        $message =  $this->twilioClient->messages->create(
                            '+919624730644',
                            array(
                                "from" => $twilio_setting->twilio_sender_number,
                                "body" => 'Your booking request has been '.$msg.' for property on '.$request->booking_date.' '.$request->booking_time
                            )
                        );
                    } catch(\Exception $e) {

                    }
                }

                $this->configSMTP();
                $data = [
                    'name'=>$validator->first_name.' '.$validator->last_name,
                    'property_name'=>$homendo->hmdo_mls_propname,
                    'status'=>$msg,
                    'booking_date'=>$request->booking_date,
                    'booking_time'=>$request->booking_time
                ];

                try {
                    Mail::to($validator->email)->send(new BookingUpdateMail($data));
                    $booking_info = PropertyBookingSchedule::with('Property', 'Buyer', 'Agent.agentInfo', 'BuyerAgent.agentInfo')->where('uuid',$id)->first();
                    $response['booking_info'] = $booking_info;
                    $response['response_message'] = "Showing ".$msg;
                    return $this->sendResponse($response);
                } catch(\Exception $e) {
                    $msg = $e->getMessage();
                    return $this->sendResponse($msg, 200, false);
                }
            }else{
                return $this->sendResponse("Sorry, Something went wrong!", 200, false);
            }
        }else{
            return $this->sendResponse("Sorry, Showing not found!", 200, false);
        }
    }

    public function getShowingBookings(Request $request){
        $this->validate($request, [
            'property_id'   => 'required',
            'user_id'       => 'required',
            'user_type'     => 'required|in:seller,buyer'
        ]);

        $all_bookings = [];
        $future_bookings = [];
        $past_bookings = [];
        $today_bookings = [];
        if ($request->buyer) {
            $bookings = PropertyBookingSchedule::with('Property', 'Buyer', 'Agent.agentInfo', 'BuyerAgent.agentInfo')->where(['property_id'=>$request->property_id, 'cv_status'=>'verified', 'buyer_id'=>$request->user_id])->get();
        }else{
            $bookings = PropertyBookingSchedule::with('Property', 'Buyer', 'Agent.agentInfo', 'BuyerAgent.agentInfo')->where(['property_id'=>$request->property_id, 'cv_status'=>'verified'])->get();

        }

        if (sizeof($bookings) > 0) {
            $showing_setup = PropertyShowingSetup::with('showingAvailability', 'showingSurvey')->where('property_id',$request->property_id)->first();
            foreach ($bookings as $booking) {
                $booking['showing_setup'] = $showing_setup;
                $surveys = json_decode($booking['showing_setup']->showingSurvey->survey);
                $answers = [];
                $newSurveys = [];
                if(!empty($surveys)){
                    $newSurveys = SurveySubCategories::whereIn('uuid',$surveys)
                                    ->orderBy('id','ASC')->pluck('uuid')->toArray();
                }
                $categories = SurveyCategories::orderBy('id','ASC')->get();
                foreach($categories as $newCategory){
                    if(!empty($newSurveys)){
                        foreach($newSurveys as $survey){
                            $newSubCate = SurveySubCategories::with('category')
                                ->where(['category_id'=>$newCategory['uuid'],'uuid'=>$survey])->first();
                            if($newSubCate != null){
                                $answers[] = $newSubCate;
                            }
                        }
                    }
                }
                /*foreach ($surveys as $survey) {
                    $subCategory = SurveySubCategories::with('category')->where('uuid',$survey)->first();
                    $answers[] = $subCategory;
                }*/
                $booking['answers'] = $answers;
                $booking['feedback'] = ShowingFeedback::where('booking_id', $booking->uuid)->first();
                $booking['office'] = '';

                $booking_count = PropertyBookingSchedule::where(['property_id'=>$booking->property_id, 'buyer_id'=>$booking->buyer_id, 'cv_status'=>'verified'])->get();
                $booking['booking_count'] = sizeof($booking_count);
                if (strtotime(date('Y-m-d')) < strtotime($booking->booking_date)) {
                    $future_bookings[] = $booking;
                }else if (strtotime(date('Y-m-d')) > strtotime($booking->booking_date)) {
                    $past_bookings[] = $booking;
                }else if (strtotime(date('Y-m-d')) == strtotime($booking->booking_date)) {
                    $today_bookings[] = $booking;
                }
            }

            $all_bookings = array('future' => $future_bookings, 'past' => $past_bookings, 'today' => $today_bookings);
            return $this->sendResponse($all_bookings);
        }else{
            return $this->sendResponse("Sorry, Bookings not found!", 200, false);
        }
    }

    public function clientShowingBookings(Request $request){
        $this->validate($request, [
            'property_id'   => 'required',
            'agent_id'      => 'required',
            'agent_type'    => 'required|in:selling,buying'
        ]);

        $all_bookings = [];
        $future_bookings = [];
        $past_bookings = [];
        $today_bookings = [];
        if ($request->agent_type == 'selling') {
            $bookings = PropertyBookingSchedule::with('Property', 'Buyer', 'Agent.agentInfo', 'BuyerAgent.agentInfo')->where(['property_id'=>$request->property_id, 'seller_agent_id'=>$request->agent_id, 'cv_status'=>'verified'])->get();
        }else{
            $bookings = PropertyBookingSchedule::with('Property', 'Buyer', 'Agent.agentInfo', 'BuyerAgent.agentInfo')->where(['property_id'=>$request->property_id, 'buyer_agent_id'=>$request->agent_id, 'cv_status'=>'verified'])->get();
        }
        
        if (sizeof($bookings) > 0) {
            $showing_setup = PropertyShowingSetup::with('showingAvailability', 'showingSurvey')->where('property_id',$request->property_id)->first();
            foreach ($bookings as $booking) {
                $booking['showing_setup'] = $showing_setup;
                $surveys = json_decode($booking['showing_setup']->showingSurvey->survey);
                $answers = [];
                $newSurveys = [];
                if(!empty($surveys)){
                    $newSurveys = SurveySubCategories::whereIn('uuid',$surveys)
                                    ->orderBy('id','ASC')->pluck('uuid')->toArray();
                }
                $categories = SurveyCategories::orderBy('id','ASC')->get();
                foreach($categories as $newCategory){
                    if(!empty($newSurveys)){
                        foreach($newSurveys as $survey){
                            $newSubCate = SurveySubCategories::with('category')
                                ->where(['category_id'=>$newCategory['uuid'],'uuid'=>$survey])->first();
                            if($newSubCate != null){
                                $answers[] = $newSubCate;
                            }
                        }
                    }
                }
                /*foreach ($surveys as $survey) {
                    $subCategory = SurveySubCategories::with('category')->where('uuid',$survey)->first();
                    $answers[] = $subCategory;
                }*/
                $booking['answers'] = $answers;
                $booking['feedback'] = ShowingFeedback::where('booking_id', $booking->uuid)->first();
                $booking['office'] = '';

                $booking_count = PropertyBookingSchedule::where(['property_id'=>$booking->property_id, 'buyer_id'=>$booking->buyer_id, 'cv_status'=>'verified'])->get();
                $booking['booking_count'] = sizeof($booking_count);
                if (strtotime(date('Y-m-d')) < strtotime($booking->booking_date)) {
                    $future_bookings[] = $booking;
                }else if (strtotime(date('Y-m-d')) > strtotime($booking->booking_date)) {
                    $past_bookings[] = $booking;
                }else if (strtotime(date('Y-m-d')) == strtotime($booking->booking_date)) {
                    $today_bookings[] = $booking;
                }
            }

            $all_bookings = array('future' => $future_bookings, 'past' => $past_bookings, 'today' => $today_bookings);
            return $this->sendResponse($all_bookings);
        }else{
            return $this->sendResponse("Sorry, Bookings not found!", 200, false);
        }
    }

    public function allShowingBookings(Request $request){

        $bookings = PropertyBookingSchedule::with('Property', 'Buyer', 'Agent.agentInfo', 'BuyerAgent.agentInfo')->where('cv_status','verified')->get();
        if (sizeof($bookings) > 0) {
            return $this->sendResponse($bookings);
        }else{
            return $this->sendResponse("Sorry, Showings not found!", 200, false);
        }
    }

    public function submitFeedback(Request $request){
        $this->validate($request, [
            'booking_id'    => 'required',
            'feedback'      => 'required'
        ]);

        $feedback = new ShowingFeedback;
        $feedback->booking_id = $request->booking_id;
        $feedback->feedback = json_encode($request->feedback);
        $result = $feedback->save();

        if ($result) {
            return $this->sendResponse("Feedback submitted successfully");
        }else{
            return $this->sendResponse("Sorry, Something went wrong!", 200, false);
        }
    }

    public function getFeedback(Request $request){
        $this->validate($request, [
            'booking_id'   => 'required'
        ]);

        $feedback = ShowingFeedback::where('booking_id', $request->booking_id)->first();
        if (!empty($feedback)) {
            return $this->sendResponse($feedback);
        }else{
            return $this->sendResponse("Sorry, Feedback not found!", 200, false);
        }
    }

    public function updateShowingStatus(Request $request){
        $booker = Users::where('uuid', base64_decode($request->b))->first();
        $validator = Users::where('uuid', base64_decode($request->v))->first();
        $booking = PropertyBookingSchedule::where('uuid', base64_decode($request->s))->first();

        if ($request->d == 'accept') {
            $status = 'approved';
        }elseif ($request->d == 'reject') {
            $status = 'rejected';
        }

        $this->configSMTP();
        $data = [
            'name'=>$booker->first_name.' '.$booker->last_name,
            'status'=>$status,
            'date'=>$booking->booking_date,
            'time'=>$booking->booking_time
        ];
        Mail::to($booker->email)->send(new UpdateShowingMail($data));
        
        return view('update-showing', ["status"=>$request->d]);
    }

    public function adminCreateBooking(Request $request){
        $this->validate($request, [
            'property_id'       => 'required',
            'buyer_id'          => 'required',
            'buyer_agent_id'    => 'required',
            'booking_date'      => 'required',
            'booking_time'      => 'required',
            'booking_slots'     => 'required',
            'interval'          => 'required',
            'showing_note'      => 'nullable',
        ]);

        $formetted_date = date('Y-m-d', strtotime($request->booking_date));
        $property_id = $request->property_id;
        $booking_date = $request->booking_date;
        $booking_time = $request->booking_time;
        $property = Properties::where('uuid', $property_id)->first();
        
        $homendo = PropertyHomendo::where('property_id', $property_id)->first();
        if ($homendo != null || $homendo != '') {
            $hmdo_mls_propname = $homendo->hmdo_mls_propname;
        }else{
            $hmdo_mls_propname = '';
        }
        $showing_setup = PropertyShowingSetup::where('property_id', $property_id)->first();
        if ($showing_setup != null || $showing_setup != '') {
            if ($showing_setup->validator != null || $showing_setup->validator != '') {
                $validator = $showing_setup->validator[0];
            }else{
                $validator = '';
            }

            $availibility = PropertyShowingAvailability::where('showing_setup_id', $showing_setup->uuid)->first();
            if($availibility !== null){
                $get_availibility = json_decode($availibility);
                $availibility_data = json_decode($get_availibility->availability);
            }
        }else{
            $validator = '';
        }
        
        $selling_agent = PropertyAgents::where(['property_id'=>$property_id, 'agent_type'=>'seller'])->first();
        
        $settings = Settings::where('option_key', 'twillio')->first();
        $twilio_setting = json_decode($settings->option_value);

        $user = Users::where('uuid',$request->buyer_id)->first();

        $time = strtotime(Carbon::now());
        $uuid = "sch".$time.rand(10,99)*rand(10,99);

        $propertyBookingSchedule = new PropertyBookingSchedule;
        $propertyBookingSchedule->uuid = $uuid;
        $propertyBookingSchedule->buyer_id = $user->uuid;
        $propertyBookingSchedule->property_id = $property_id;
        $propertyBookingSchedule->property_mls_id = $property->mls_id;
        $propertyBookingSchedule->property_originator = $property->mls_name;
        $propertyBookingSchedule->booking_date = $formetted_date;
        $propertyBookingSchedule->booking_time = $booking_time;
        $propertyBookingSchedule->booking_slots = json_encode($request->booking_slots);
        if ($showing_setup != null || $showing_setup != '') {
            if ($showing_setup->type == 'VALID') {
                $propertyBookingSchedule->status = 'P';
            }else{
                $propertyBookingSchedule->status = 'A';
            }
        }else{
            $propertyBookingSchedule->status = 'P';
        }
        $propertyBookingSchedule->cv_status = 'verified';
        if ($selling_agent != '' || $selling_agent != null) {
            $propertyBookingSchedule->seller_agent_id = $selling_agent->agent_id;
        }
        if ($request->buyer_agent_id != '' || $request->buyer_agent_id != null) {
            $propertyBookingSchedule->buyer_agent_id = $request->buyer_agent_id;
        }
        $propertyBookingSchedule->showing_note = $request->showing_note;
        $propertyBookingSchedule->interval = $request->interval;
        $propertyBookingSchedule->cancel_at = null;

        if ($propertyBookingSchedule->save()) {
            $check_buyer = PropertyBuyers::where(['buyer_id'=>$user->uuid, 'property_id'=>$property_id])->first();
            if (empty($check_buyer)) {
                $property_buyer = new PropertyBuyers;
                $property_buyer->property_id = $property_id;
                $property_buyer->buyer_id = $user->uuid;
                $property_buyer->agent_id = $request->buyer_agent_id;
                $property_buyer->save();
            }

            if ($showing_setup != null || $showing_setup != '') {
                if ($availibility !== null) {
                    foreach ($availibility_data as $data) {
                        if ($data->date == date('F d l', strtotime($booking_date))) {
                            foreach ($data->slots as $slot) {
                                /*if ($slot->slot == date('H:i A', strtotime($booking_time))) {
                                    $slot->status = 'booked';
                                }*/
                                foreach ($request->booking_slots as $booking_slot) {
                                    if ($slot->slot == date('H:i A', strtotime($booking_slot))) {
                                        $slot->status = 'booked';
                                    }
                                }
                            }
                        }
                    }
                    PropertyShowingAvailability::where('showing_setup_id', $showing_setup->uuid)->update(['availability'=>json_encode($availibility_data)]);
                }
                
                if ($showing_setup->validator != null || $showing_setup->validator != '') {
                    if ($twilio_setting->status == true) {
                        try {
                            $this->twilioClient = new TwilioClient($twilio_setting->account_sid, $twilio_setting->auth_token);
                            $message =  $this->twilioClient->messages->create(
                                $validator->phone,
                                array(
                                    "from" => $twilio_setting->twilio_sender_number,
                                    "body" => 'Hi '.$validator->first_name.' '.$validator->last_name.', '.$user->first_name.' '.$user->last_name.' want to visit your property on '.$request->booking_date.' '.$request->booking_time
                                )
                            );
                        } catch(\Exception $e) {

                        }
                    }

                    $this->configSMTP();
                    $mail_data = [
                        'name'=>$user->first_name.' '.$user->last_name,
                        'validator_name'=>$validator->first_name.' '.$validator->last_name,
                        'property_name'=>$hmdo_mls_propname,
                        'booking_date'=>$request->booking_date,
                        'booking_time'=>$request->booking_time,
                        'booking_id'=>base64_encode($uuid),
                        'validator_id'=>base64_encode($validator->uuid),
                        'booker_id'=>base64_encode($request->buyer_id)
                    ];

                    try {
                        Mail::to($validator->email)->send(new BookingMail($mail_data));
                    } catch(\Exception $e) {
                        /*$msg = $e->getMessage();
                        return $this->sendResponse($msg, 200, false);*/
                    }
                }
            }

            return $this->sendResponse("Showing booked successfully!");
        }else{
            return $this->sendResponse("Sorry, Something went wrong!", 200, false);
        }
    }
}