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
use App\Mail\SignupMail;
use App\Mail\BookingMail;
use App\Mail\BookingUpdate;
use Carbon\Carbon;
use Twilio\Rest\Client as TwilioClient;

class BookingScheduleController extends Controller
{
    public function createBooking(Request $request){
        $this->validate($request, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'     => 'required',
            'email'     => 'required',
            'property_id'=> 'required',
            'booking_date'=> 'required|date|date_format:Y-m-d',
            'booking_time'=> 'required',
            'buyer_id'    => 'nullable',
            'agent_id'    => 'required'
        ]);
        
        $phone = $request->phone;
        $email = $request->email;
        $property_id = $request->property_id;
        $booking_date = $request->booking_date;
        $booking_time = $request->booking_time;
        $property = Properties::where('uuid', $property_id)->first();
        $homendo = PropertyHomendo::where('property_id', $property_id)->first();
        $showing_setup = PropertyShowingSetup::where('property_id', $property_id)->first();
        $validator = $showing_setup->validator[0];
        $availibility = PropertyShowingAvailability::where('showing_setup_id', $showing_setup->uuid)->first();
        $get_availibility = json_decode($availibility);
        $availibility_data = json_decode($get_availibility->availability);

        if ($request->has('buyer_id')) {
            $users = Users::where('uuid',$request->buyer_id)->first();
            $time = strtotime(Carbon::now());
            $uuid = "sch".$time.rand(10,99)*rand(10,99);
            $propertyBookingSchedule = new PropertyBookingSchedule;
            $propertyBookingSchedule->uuid = $uuid;
            $propertyBookingSchedule->buyer_id = $users->uuid;
            $propertyBookingSchedule->property_id = $property_id;
            $propertyBookingSchedule->booking_date = $booking_date;
            $propertyBookingSchedule->booking_time = $booking_time;
            if ($showing_setup->type == 'VALID') {
                $propertyBookingSchedule->status = 'P';
            }else{
                $propertyBookingSchedule->status = 'A';
            }
            $propertyBookingSchedule->agent_id = $request->agent_id;
            $propertyBookingSchedule->cancel_at = null;

            $this->configSMTP();

            if ($propertyBookingSchedule->save()) {
                foreach ($availibility_data as $data) {
                    if ($data->date == date('F d l', strtotime($booking_date))) {
                        foreach ($data->slots as $slot) {
                            if ($slot->slot == date('H:i A', strtotime($booking_time))) {
                                $slot->status = 'booked';
                            }
                        }
                    }
                }
                PropertyShowingAvailability::where('showing_setup_id', $showing_setup->uuid)->update(['availability'=>json_encode($availibility_data)]);

                try {
                    $this->twilioClient = new TwilioClient('AC77bf6fe8f1ff8ee95bad95276ffaa586', '94666fdb4b4f3090f7b26be77e67a819');
                    $message =  $this->twilioClient->messages->create(
                        '+919624730644',
                        array(
                            "from" => '+14243918787',
                            "body" => 'Hi '.$validator->first_name.' '.$validator->last_name.', '.$users->first_name.' '.$users->last_name.' want to visit your this Luxury Property property on '.$request->booking_date.' '.$request->booking_time
                        )
                    );
                } catch(\Exception $e) {

                }

                $mail_data = [
		                'name'=>$users->first_name.' '.$users->last_name,
		                'validator_name'=>$validator->first_name.' '.$validator->last_name,
		                'property_name'=>$homendo->hmdo_mls_propname,
		                'booking_date'=>$request->booking_date,
		                'booking_time'=>$request->booking_time
		            ];

                try {
                    Mail::to($validator->email)->send(new BookingMail($mail_data));
                    return $this->sendResponse("Insert Request Successfully!");
                } catch(\Exception $e) {
                    $msg = $e->getMessage();
                    return $this->sendResponse($msg, 200, false);
                }
            }else{
                return $this->sendResponse("Sorry, Something went wrong!");
            }
        }else{
            if (Users::where('email', $request->email)->exists()) {
                return $this->sendResponse("Email already exists!", 200, false);
            }

	        if (Users::where('phone', $request->phone)->exists()) {
                return $this->sendResponse("Phone no. already exists!", 200, false);
            }
            $uuid = "usr".$time.rand(10,99)*rand(10,99);
            $user = new Users;
            $user->uuid = $uuid;
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
                try {
                    $this->twilioClient = new TwilioClient('AC77bf6fe8f1ff8ee95bad95276ffaa586', '94666fdb4b4f3090f7b26be77e67a819');
                    $message =  $this->twilioClient->messages->create(
                        '+919624730644',
                        array(
                            "from" => '+14243918787',
                            "body" => 'Hi '.$validator->first_name.' '.$validator->last_name.', '.$request->first_name.' '.$request->last_name.' want to visit your this Luxury Property property on '.$request->booking_date.' '.$request->booking_time
                        )
                    );
                } catch(\Exception $e) {

                }

                $this->configSMTP();
                $verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
                Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);

                $data = [
                    'name'=>$request->first_name.' '.$request->last_name, 
                    'verification_token'=>$verification_token, 
                    'email'=>$request->email,
                    'url'=>$request->url
                ];

                $mail_data = [
                    'name'=>$request->first_name.' '.$request->last_name,
                    'validator_name'=>$validator->first_name.' '.$validator->last_name,
                    'property_name'=>$homendo->hmdo_mls_propname,
                    'booking_date'=>$request->booking_date,
                    'booking_time'=>$request->booking_time
                ];

                try {
                    $uuid = "sch".$time.rand(10,99)*rand(10,99);
                    $propertyBookingSchedule = new PropertyBookingSchedule;
                    $propertyBookingSchedule->uuid = $uuid;
                    $propertyBookingSchedule->buyer_id = $user->uuid;
                    $propertyBookingSchedule->property_id = $property_id;
                    $propertyBookingSchedule->booking_date = $booking_date;
                    $propertyBookingSchedule->booking_time = $booking_time;
                    $propertyBookingSchedule->status = 'P';
                    $propertyBookingSchedule->save();
                    Mail::to($request->email)->send(new BookingMail($mail_data));
                    Mail::to($request->email)->send(new SignupMail($data));
                    return $this->sendResponse("Insert Request Successfully!");
                } catch(\Exception $e) {
                    $msg = $e->getMessage();
                    return $this->sendResponse($msg, 200, false);
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
            'user_id'           => 'required',
            'status'            => 'required|in:A,R,P',
            'reason'            => 'nullable',
            'keep_slot'         => 'required'
        ]);

        $user_id = $request->user_id;
        $id      = $request->booking_id;
        $status  = $request->status;
        $reason  = $request->reason;
        $users = Users::where('uuid',$user_id)->first();
        $booking = PropertyBookingSchedule::where('uuid',$id)->first();
        $validator = Users::where('uuid', $booking->buyer_id)->first();
        $property = Properties::where('uuid', $booking->property_id)->first();
        $homendo = PropertyHomendo::where('property_id', $booking->property_id)->first();
        $setup = PropertyShowingSetup::where('property_id', $booking->property_id)->first();
        $availibility = PropertyShowingAvailability::where('showing_setup_id', $setup->uuid)->first();
        $get_availibility = json_decode($availibility);
        $availibility_data = json_decode($get_availibility->availability);

        if (!empty($users)) {
            $update['status'] = $status;
            $update['cancel_reason'] = $reason;
            $update['cancel_at'] = date('Y-m-d H:i:s');
            $result = PropertyBookingSchedule::where('uuid',$id)->update($update);
            if ($status == 'A') {
                $msg = "Approved";
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
                                if ($slot->slot == $request->booking_time) {
                                    $slot->status = 'booked';
                                }else{
                                    $slot->status = 'confirm';
                                }
                            }
                        }
                    }
                    PropertyShowingAvailability::where('showing_setup_id', $setup->uuid)->update(['availability'=>json_encode($availibility_data)]);
                }

                try {
                    $this->twilioClient = new TwilioClient('AC77bf6fe8f1ff8ee95bad95276ffaa586', '94666fdb4b4f3090f7b26be77e67a819');
                    $message =  $this->twilioClient->messages->create(
                        '+919624730644',
                        array(
                            "from" => '+14243918787',
                            "body" => 'Your booking request has been '.$msg.' for this Luxury Property property on '.$request->booking_date.' '.$request->booking_time
                        )
                    );
                } catch(\Exception $e) {

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
                    Mail::to($validator->email)->send(new BookingUpdate($data));
                    return $this->sendResponse("Showing ".$msg);
                } catch(\Exception $e) {
                    $msg = $e->getMessage();
                    return $this->sendResponse($msg, 200, false);
                }
            }else{
                return $this->sendResponse("Sorry, Something went wrong!", 200, false);
            }
        }else{
            return $this->sendResponse("Sorry, Something went wrong!", 200, false);
        }
    }

    public function getShowingBookings(Request $request){
        $this->validate($request, [
            'property_id'   => 'required'
        ]);

        $all_bookings = [];
        $future_bookings = [];
        $past_bookings = [];
        $today_bookings = [];
        $bookings = PropertyBookingSchedule::with('Property', 'Buyer', 'Agent.agentInfo')->where('property_id',$request->property_id)->get();
        $showing_setup = PropertyShowingSetup::with('showingAvailability', 'showingSurvey')->where('property_id',$request->property_id)->first();
        if (sizeof($bookings) > 0) {
            foreach ($bookings as $booking) {
                $booking['showing_setup'] = $showing_setup;
                $booking['office'] = '';

                $booking_count = PropertyBookingSchedule::where(['property_id'=>$booking->property_id, 'buyer_id'=>$booking->buyer_id])->get();
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

        $bookings = PropertyBookingSchedule::with('Property', 'Buyer', 'Agent.agentInfo')->get();
        if (sizeof($bookings) > 0) {
            return $this->sendResponse($bookings);
        }else{
            return $this->sendResponse("Sorry, Showings not found!", 200, false);
        }
    }
}