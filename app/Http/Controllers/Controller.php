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
            'mail.host' => 'mail.showing.vip',
            'mail.port' => 587,
            'mail.from' => ['address' => "no_reply@showing.vip", 'name' =>"Showing VIP"],
            'mail.encryption' => "tls",
            'mail.username' =>  "no_reply@showing.vip",
            'mail.password' => "p692rsV%X@X@",
        ]);
        /*config([
            'mail.driver' => 'smtp',
            'mail.host' => 'smtp.gmail.com',
            'mail.port' => 587,
            'mail.from' => ['address' => "broadviewinnovations8181@gmail.com", 'name' =>"Showing VIP"],
            'mail.encryption' => "tls",
            'mail.username' =>  "broadviewinnovations8181@gmail.com",
            'mail.password' => "famkvwztymnresye",
        ]);*/
    }

    public function singleImageUpload($myPath,$image)
    {
        $folderPath = $myPath;//app()->basePath('public/staff-images/');
        $fileName =  rand().'Image.png';
        $base64Image = $image;
        $base64Image = trim($base64Image);
        $base64Image = str_replace('data:image/png;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/gif;base64,', '', $base64Image);
        $base64Image = str_replace(' ', '+', $base64Image);

        $imageData = base64_decode($base64Image);
        $filePath = $folderPath . $fileName;
        if(file_put_contents($filePath, $imageData)){
            $finalImage = $fileName; 
        }
        else
        {
            $finalImage = "default.png";
        } 
        
        return $finalImage;
    }
}
