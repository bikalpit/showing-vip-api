<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignOwner extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $owner_name;
    public $property_name;
    public $url;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->owner_name = $data['owner_name'];
        $this->property_name = $data['property_name'];
        $this->url = env('APP_URL');
    }
    public function build()
    {
        return $this->subject("Assign Owner")
                    ->view('assign-owner');
    }    
}
?>