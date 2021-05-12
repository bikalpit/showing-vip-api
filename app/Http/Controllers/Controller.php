<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function sendResponse($message, int $responseCode = 200, bool $data = true)
    {
        return response()->json(["data" =>$data, "response" => $message], $responseCode); 
    }

    public function configSMTP()
    {
        config([
            'mail.driver' => 'smtp',
            'mail.host' => 'smtp.gmail.com',
            'mail.port' => 587,
            'mail.from' => ['address' => "vishal.mistry.bi@gmail.com", 'name' =>"Showing VIP"],
            'mail.encryption' => "tls",
            'mail.username' =>  "vishal.mistry.bi@gmail.com",
            'mail.password' => "ijqswgrmryipmrnt",
        ]);
    }
}
