<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public $atma_api_url = "https://open-sandbox.atma.io";
    public $atma_api_key = "164a23dab67642f18b8cfd1941aab094";

    public function prepareGetRequest($requestURL, $requestBody = null){
        
        try{
            $requestUrl = $this->atma_api_url .  substr($requestURL,4);// /api/ kısmını çıkarıyorum.
            $response = $this->client->get($requestUrl);
            return $response;
        }catch(\GuzzleHttp\Exception\ClientException $e){
            if($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '404'){ 
                    return response($e->getResponse()->getBody(),$e->getResponse()->getStatusCode());
                }
            }
        }

    }

    public function preparePostRequest($requestURL, $requestBody = null){
        try{
            $requestUrl = $this->atma_api_url . substr($requestURL,4);// /api/ kısmını çıkarıyorum.
            $response = $this->client->post($requestUrl,[
                'json' => $requestBody
            ]);
            return $response;
        }catch(\GuzzleHttp\Exception\ClientException $e){
            if($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '404'){ 
                    return response($e->getResponse()->getBody(),$e->getResponse()->getStatusCode());
                }
                if ($e->getResponse()->getStatusCode() == '400'){ 
                    return response($e->getResponse()->getBody(),$e->getResponse()->getStatusCode());
                }
            }
        }   

    }

    public function preparePutRequest($requestURL, $requestBody = null){
        try{
            $requestUrl = $this->atma_api_url . substr($requestURL,4);// /api/ kısmını çıkarıyorum.
            $response = $this->client->put($requestUrl,[
                'json' => $requestBody
            ]);
            return $response;
        }catch(\GuzzleHttp\Exception\ClientException $e){
            if($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '404'){ 
                    return response($e->getResponse()->getBody(),$e->getResponse()->getStatusCode());
                }
            }
        }
        

    }

    public function preparePatchRequest($requestURL, $requestBody = null){
        try{
            $requestUrl = $this->atma_api_url . substr($requestURL,4);// /api/ kısmını çıkarıyorum.
            $response = $this->client->patch($requestUrl,[
                'json' => $requestBody
            ]);
            return $response;
        }catch(\GuzzleHttp\Exception\ClientException $e){
            if($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '404'){ 
                    return response($e->getResponse()->getBody(),$e->getResponse()->getStatusCode());
                }
                if ($e->getResponse()->getStatusCode() == '400'){ 
                    return response($e->getResponse()->getBody(),$e->getResponse()->getStatusCode());
                }
            }
        }   

    }

    public function prepareDeleteRequest($requestURL, $requestBody = null){
        try{
            $requestUrl = $this->atma_api_url . substr($requestURL,4);// /api/ kısmını çıkarıyorum.
            $response = $this->client->put($requestUrl,[
                'json' => $requestBody
            ]);
            return $response;
        }catch(\GuzzleHttp\Exception\ClientException $e){
            if($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '404'){ 
                    return response($e->getResponse()->getBody(),$e->getResponse()->getStatusCode());
                }
            }
        }
        

    }
}
