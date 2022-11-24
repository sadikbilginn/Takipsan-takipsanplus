<?php

namespace App\Http\Controllers;

use App\Locale;
use App\Menu;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\View;
use App\Helpers\OptionTrait;
use App\Helpers\LogActivity;

class Controller extends BaseController
{
    use LogActivity;
    use OptionTrait;
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $data = [];
    protected $headData = [];
    public $atma_api_url = "https://open-sandbox.atma.io";
    public $atma_api_key = "164a23dab67642f18b8cfd1941aab094";

    public function __construct()
    {

        $this->middleware(function ($request, $next){

            if(!session()->has('glb_locales')){
                session()->put('glb_locales', Locale::all());
            }

            view::share([
                'glb_locales'    => session('glb_locales')
            ]);

            return $next($request);
        });

    }

    public function prepareGetRequest($requestURL, $requestBody = null){
        
        try{
            $requestUrl = $this->atma_api_url .  substr($requestURL,4);// /api/ kısmını çıkarıyorum.
            $response = $this->client->get($requestUrl);
            return $response;
        }catch(\GuzzleHttp\Exception\ClientException $e){
            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.err_add_company'), 'error'));
            if($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '404'){ 
                    return response($e->getResponse(),$e->getResponse()->getStatusCode());
                }
                if ($e->getResponse()->getStatusCode() == '401'){ 
                    return response($e->getResponse(),$e->getResponse()->getStatusCode());
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
