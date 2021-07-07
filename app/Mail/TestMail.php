<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $url;
    
    public function __construct($data)
    {
        $this->data = $data;
        $this->url = $data['url'];
    }
    public function build()
    {
        return $this->subject("Test Mail")
                    ->view('test-mail-template');
    }    
}
?>