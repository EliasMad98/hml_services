<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Config;

class FatoorahServices
{
    private $base_url;
    private $headers;
    private $request_client;
/**
*FotooraServices constructor.
*@param Client $request_client
*/
public function __construct (Client $request_client)
{
    $this->request_client = $request_client;

    $this->base_url = config('app.fatoorah_base_url');

    $this->headers = [
    'Content-Type' => 'application/json',
    'authorization'=> "Bearer ".config("app.fatoorah_token")
    ];

}
/**
*@param $uri
*@param $method
*@param array $body
*@return false |mixed
*@throws GuzzleHttp\Exception\GuzzleException
*/
public function buildRequest($uri,$method,$data=[]){
    $full_url=$this->base_url.$uri;
    $request = new Request ($method, $full_url, $this->headers);
    if(!$data)
        return false;

    $response=$this->request_client->send($request, [
        'json' => $data
    ]);
    if ($response->getStatusCode() != 200) {

        return false;

    }

    $response = json_decode($response->getBody(),true);

    return $response;
}
/**
*@param $value
*@return mixed
*/

    public function sendPayment($data){
        return $response = $this->buildRequest("SendPayment",'POST',$data);
    }

    public function getPaymentStatus($data){
        return $response = $this->buildRequest("getPaymentStatus",'POST',$data);
        }


}

