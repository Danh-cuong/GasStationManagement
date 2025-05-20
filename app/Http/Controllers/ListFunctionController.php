<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ListFunctionController extends Controller
{
    function postAPIToken($url, $datasubmit){
        $data=[];
        try {
            $client = new \GuzzleHttp\Client();
            $res = $client->request('POST',$url,[
                'headers' => [
                    'Content-Type'=>'application/json',
                    'User-Agent' => 'testing/1.0',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($datasubmit),
                'verify' => false 
            ]);
            
            $data=json_decode($res->getBody()->getContents());
            
            return $data;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            
        } 
    }
    
    function postAPIAuth($url, $datasubmit,$token){
        $data=[];
        try {
            $client = new \GuzzleHttp\Client();
            $res = $client->request('POST',$url,[
                'headers' => [
                    'Authorization'=>"Bearer ".$token,
                    'Content-Type'=>'application/json',
                    'User-Agent' => 'testing/1.0',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($datasubmit),
                'verify' => false 
            ]);
            
            $data=json_decode($res->getBody()->getContents());
           
            return $data;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
         
        }
    }
    
    function getAPIToken($url,$datasubmit){
        $client = new \GuzzleHttp\Client();
        try { 
        $res = $client->request('GET', $url, [
            'body' => json_encode($datasubmit),
            'headers' => [
                'Content-Type'=>'application/json',
                'User-Agent' => 'testing/1.0',
                'Accept' => 'application/json',
            ],
            'verify' => false 
        ]);
        // dd( $res);
        $data=json_decode($res->getBody()->getContents());
         return $data;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
          
        }
    }
    function getAPIAuth($url,$datasubmit,$token){
        $client = new \GuzzleHttp\Client();
        try { 
            $res = $client->request('GET', $url, [
            
                'headers' => [
                    'Authorization'=>"Bearer ".$token,
                    'Content-Type'=>'application/json',
                    'User-Agent' => 'testing/1.0',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($datasubmit),
                'verify' => false 
            ]);
      
            $data=json_decode($res->getBody()->getContents());
            return $data;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
         
           
        }
    }
}