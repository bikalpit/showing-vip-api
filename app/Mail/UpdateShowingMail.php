<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateShowingMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $status;
    public $date;
    public $time;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->status = $data['status'];
        $this->date = $data['date'];
        $this->time = $data['time'];
    }
    public function build()
    {
        return $this->subject("Showing Update")
                    ->view('showing-update-mail');
    }    
}
?>