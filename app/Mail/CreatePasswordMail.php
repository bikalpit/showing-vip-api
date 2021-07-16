<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreatePasswordMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
    }
    public function build()
    {
        return $this->subject("Create Password")
                    ->view('create-password-mail');
    }    
}
?>