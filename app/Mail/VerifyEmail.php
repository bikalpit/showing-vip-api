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
    public $verification_token;
    public $url;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->verification_token = $data['verification_token'];
        $this->url = $data['url'];
    }
    public function build()
    {
        return $this->subject("Verify Email")
                    ->view('verify-email');
    }    
}
?>