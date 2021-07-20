<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OwnerVerificationMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $user_id;
    public $token;
    public $site_url;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->user_id = $data['user_id'];
        $this->token = $data['token'];
        $this->site_url = env('APP_URL');
    }
    public function build()
    {
        return $this->subject("User Verification")
                    ->view('verify-owner');
    }    
}
?>