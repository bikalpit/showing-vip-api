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
    public $owner_name;
    public $agent_name;
    public $user_id;
    public $property_id;
    public $token;
    public $site_url;
    public function __construct($data)
    {
        $this->data = $data;    
        $this->owner_name = $data['owner_name'];
        $this->agent_name = $data['agent_name'];
        $this->user_id = $data['user_id'];
        $this->property_id = $data['property_id'];
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