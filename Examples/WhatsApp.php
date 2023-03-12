<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('vendor/autoload.php');
use ApiBrasil\Service;

class Example
{
    static function sendText()
    {
       
        $response = Service::WhatsApp("sendText", [
            "Bearer" => "SEU TOKEN AQUI",
            "SecretKey" => "SEU SECRETKEY AQUI",
            "PublicToken" => "SEU PUBLICTOKEN AQUI", 
            "DeviceToken" => "SEU DEVICETOKEN AQUI",
            "body" => [
                "number" => "5531994359434",
                "text" => "🟢 Bem vindo ao APIBrasil"
            ]
        ]);
        
        return $response;

    }

}

print_r(Example::sendText());