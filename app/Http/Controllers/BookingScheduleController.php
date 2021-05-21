<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\PropertyBookingSchedule;
use App\Models\PropertyShowingSetup;
use App\Models\Properties;
use App\Mail\SignupMail;
use App\Mail\BookingMail;
use App\Mail\BookingUpdate;
use Carbon\Carbon;

class BookingScheduleController extends Controller
{
    public function createBooking(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'     => 'required',
            'email'     => 'required',
            'property_id'=> 'required',
            'booking_date'=> 'required|date|date_format:Y-m-d',
            'booking_time'=> 'required',
            'buyer_id'    => 'nullable'
        ]);
        $phone = $request->phone;
        $email = $request->email;
        $property_id = $request->property_id;
        $time = strtotime(Carbon::now());
        $booking_date = $request->booking_date;
        $booking_time = $request->booking_time;
        $property = Properties::where('uuid', $property_id)->first();
        $showing_setup = PropertyShowingSetup::where('property_id', $property_id)->first();
        $validator = Users::where('uuid', $showing_setup->validator)->first();

        if($request->has('buyer_id')){
            $users = Users::where('uuid',$request->buyer_id)->first();
            $uuid = "sch".$time.rand(10,99)*rand(10,99);
            $propertyBookingSchedule = new PropertyBookingSchedule;
            $propertyBookingSchedule->uuid = $uuid;
            $propertyBookingSchedule->buyer_id = $users->uuid;
            $propertyBookingSchedule->property_id = $property_id;
            $propertyBookingSchedule->booking_date = $booking_date;
            $propertyBookingSchedule->booking_time = $booking_time;
            $propertyBookingSchedule->status = 'P';

            $this->configSMTP();
            $mail_data = [
                'name'=>$request->first_name.' '.$request->last_name,
                'validator_name'=>$validator->first_name.' '.$validator->last_name,
                'property_name'=>$property->title,
                'booking_date'=>$request->booking_date,
                'booking_time'=>$request->booking_time
            ];

            if($propertyBookingSchedule->save()){
                try{
                    Mail::to($request->email)->send(new BookingMail($mail_data));
                    return $this->sendResponse("Insert Request Successfully!");
                }catch(\Exception $e){
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
            if($result){
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
                    'property_name'=>$property->title,
                    'booking_date'=>$request->booking_date,
                    'booking_time'=>$request->booking_time
                ];

                try{
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
                }catch(\Exception $e){
                    $msg = $e->getMessage();
                    return $this->sendResponse($msg, 200, false);
                }
            }else{
                return $this->sendResponse("Sorry, Something went wrong!");
            }
        }
        
    }
    public function updateBooking(Request $request)
    {
        $this->validate($request, [
            'booking_id' => 'required',
            'booking_date' => 'required',
            'booking_time' => 'required',
            'user_id'    => 'required',
            'status'     => 'required|in:A,R',
            'reason'     => 'nullable'
        ]);
        $user_id = $request->user_id;
        $id      = $request->booking_id;
        $status  = $request->status;
        $reason  = $request->reason;
        $users = Users::where('uuid',$user_id)->first();
        $booking = PropertyBookingSchedule::where('uuid',$id)->first();
        $validator = Users::where('uuid', $booking->buyer_id)->first();
        $property = Properties::where('uuid', $booking->property_id)->first();

        if(!empty($users)){
            $update['status'] = $status;
            $update['cancel_reason'] = $reason;
            $result = PropertyBookingSchedule::where('uuid',$id)->update($update);
            if($status == 'A'){
                $msg = "Approved.";
            }else{
                $msg = "Cancelled.";
            }
            if($result){
                $this->configSMTP();
                $data = [
                    'name'=>$validator->first_name.' '.$validator->last_name,
                    'property_name'=>$property->title,
                    'status'=>$status,
                    'booking_date'=>$request->booking_date,
                    'booking_time'=>$request->booking_time
                ];

                try{
                    Mail::to($validator->email)->send(new BookingUpdate($data));
                    return $this->sendResponse("Booking ".$msg);
                }catch(\Exception $e){
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
}