<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AgentShowingMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $mls_id;
    public $originator;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->mls_id = $data['mls_id'];
        $this->originator = $data['originator'];
    }
    public function build()
    {
        return $this->subject("Showing Request")
                    ->view('agent-showing-mail');
    }    
}
?>