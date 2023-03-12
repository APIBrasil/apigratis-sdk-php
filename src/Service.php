<?php

namespace ApiBrasil;

use GuzzleHttp\Exception\ClientException;

class Service extends Base
{   
    public static function WhatsApp(String $action = '', Array $data = []) {

        try {

            $base_uri = "https://cluster.apigratis.com/api/v1/whatsapp/";
            $method = $data['method'] ?? 'POST';
            
            $headers = [

                "Content-Type" => "application/json",
                "Accept" => "application/json",

                "Authorization" => "Bearer ".$data['Bearer'],
                "SecretKey" => $data['SecretKey'] ?? '',
                "PublicToken" => $data['PublicToken'] ?? '',
                "DeviceToken" => $data['DeviceToken'] ?? '',
            ];

            $body = $data['body'] ?? [];

            $response = self::defaultRequest($method, $base_uri, $action, $headers, $body);
            return $response;

        } catch (ClientException $e) {

            $response = $e->getResponse();
            return json_decode((string)($response->getBody()->getContents()));

        }

    }

}
