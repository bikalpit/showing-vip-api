<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingUpdate extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $property_name;
    public $status;
    public $booking_date;
    public $booking_time;
    public $url;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->property_name = $data['property_name'];
        $this->status = $data['status'];
        $this->booking_date = $data['booking_date'];
        $this->booking_time = $data['booking_time'];
        $this->url = env('APP_URL');
    }
    public function build()
    {
        return $this->subject("Booking Update")
                    ->view('booking-update');
    }    
}
?>