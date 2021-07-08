<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgetPasswordMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $verification_token;
    public $email;
    public $app_url;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->verification_token = $data['verification_token'];
        $this->email = $data['email'];
        $this->app_url = $data['app_url'];
    }
    public function build()
    {
        return $this->subject("Forget Password")
                    ->view('forget-password-mail');
    }    
}
?>