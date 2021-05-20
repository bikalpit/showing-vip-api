<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\PropertyBookingSchedule;
use App\Mail\SignupMail;
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
            if($propertyBookingSchedule->save()){
                return $this->sendResponse("Insert Request Successfully!.");
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
                                'url'=>$request->url];
                    try
                    {
                        $uuid = "sch".$time.rand(10,99)*rand(10,99);
                        $propertyBookingSchedule = new PropertyBookingSchedule;
                        $propertyBookingSchedule->uuid = $uuid;
                        $propertyBookingSchedule->buyer_id = $user->uuid;
                        $propertyBookingSchedule->property_id = $property_id;
                        $propertyBookingSchedule->booking_date = $booking_date;
                        $propertyBookingSchedule->booking_time = $booking_time;
                        $propertyBookingSchedule->status = 'P';
                        $propertyBookingSchedule->save();
                        Mail::to($request->email)->send(new SignupMail($data));
                        return $this->sendResponse("Insert Request Successfully!.");
                    }catch(\Exception $e){
                        $msg = $e->getMessage();
                        return $this->sendResponse($msg, 200, false);
                    }
                }
        }
        
    }
    public function updateBooking(Request $request)
    {
        $this->validate($request, [
            'booking_id' => 'required',
            'user_id'    => 'required',
            'status'     => 'required|in:A,R',
            'reason'     => 'nullable'
        ]);
        $user_id = $request->user_id;
        $id      = $request->booking_id;
        $status  = $request->status;
        $reason  = $request->reason;
        $users = Users::where('uuid',$user_id)->first();
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
                return $this->sendResponse("Booking ".$msg);
            }else{
                return $this->sendResponse("Sorry!Something Wrong!.",200,false);
            }
        }else{
            return $this->sendResponse("Sorry!Something Wrong!.",200,false);
        }
    }
}