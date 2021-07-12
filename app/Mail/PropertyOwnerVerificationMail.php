<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PropertyOwnerVerificationMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $owner_name;
    public $site_url;
    public $verify_url;
    public $token;
    public function __construct($data)
    {
        $this->owner_name = $data['owner_name'];
        $this->site_url = env('APP_URL');
        $this->verify_url = $this->site_url . "/api/verified-property-owner?token=" . $data['email_verification_token'] . "&owner=" . $data['owner_id'];
    }
    public function build()
    {
        return $this->subject("Verify Property Owner")
                    ->view('verify-property-owner');
    }    
}
?>