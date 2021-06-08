<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PropertyVerification extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $owner_name;
    public $property_link;
    public $site_url;
    public $token;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->owner_name = $data['owner_name'];
        $this->property_link = $data['property_link'];
        $this->site_url = $data['site_url'];
        $this->token = $data['token'];
    }
    public function build()
    {
        return $this->subject("Verify Property")
                    ->view('verify-property');
    }    
}
?>