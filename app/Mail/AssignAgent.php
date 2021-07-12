<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignAgent extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $property_id;
    public $name;
    public $property_name;
    public $url;
    public function __construct($data)
    {
        $this->data = $data;
        $this->property_id = $data['property_id'];
        $this->name = $data['name'];
        $this->property_name = $data['property_name'];
        $this->url = env('APP_URL');
    }
    public function build()
    {
        return $this->subject("Assign Agent")
                    ->view('assign-agent');
    }    
}
?>