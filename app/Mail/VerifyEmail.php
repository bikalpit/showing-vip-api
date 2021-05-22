<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $otp;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->otp = $data['otp'];
    }
    public function build()
    {
        return $this->subject("Verify Email")
                    ->view('verify-email');
    }    
}
?>