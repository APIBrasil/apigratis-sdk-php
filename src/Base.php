<?php

namespace ApiBrasil;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class Base
{
    public static function defaultRequest(String $method, String $base_uri, String $action, Array $headers, Array $body)
    {
        try {

            $client = new Client([
                'timeout' => 300, // timeout 5 minutes
                'verify' => false, // disable ssl verify
                'base_uri' => $base_uri // base uri
            ]);

            $headers['Content-Type'] = 'application/json'; // set content type json
            $headers['Accept'] = 'application/json'; // set accept json

            //headers
            $headers['Agent'] = 'ApiBrasil/2.0.0 (https://www.apibrasil.io)'; // set user agent

            $request = new Request($method, $action, $headers, json_encode($body)); // create request
            $response = $client->send($request); // send request

            if(isset(explode("?", $action)[0]) and explode("?", $action)[0] === 'qrcode'){
                return $response->getBody()->getContents();
            }
            
            return json_decode($response->getBody()->getContents()); // return response object

        } catch (ClientException $e) {

            // return exception error
            $response = $e->getResponse();
            return json_decode((string)($response->getBody()->getContents()));

        }
    }
}
