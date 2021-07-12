<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $validator_name;
    public $property_name;
    public $booking_date;
    public $booking_time;
    public $url;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->validator_name = $data['validator_name'];
        $this->property_name = $data['property_name'];
        $this->booking_date = $data['booking_date'];
        $this->booking_time = $data['booking_time'];
        $this->url = env('APP_URL');
    }
    public function build()
    {
        return $this->subject("New Booking")
                    ->view('booking-mail');
    }    
}
?>