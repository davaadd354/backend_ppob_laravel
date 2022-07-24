<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class OperatorController extends Controller
{
    public function getDataOperator(Request $request){
        $request->validate([
            'operator' => 'required|string'
        ]);

        $username = env('USERNAME_IAK_ID');
        $dev_key = env('DEVELOPMENT_KEY_IAK_ID');
        $sign_key = md5($username.$dev_key.'pl');
        $url = 'https://prepaid.iak.dev/api/pricelist?operator='.$request->operator;

        $client = new Client(['verify' => false]);
        
        $res = $client->request('POST', $url, [
            'headers' => [
                'Content-Type'     => 'application/json',
            ],
            'body' => "{\n  \"username\": \"$username\",\n  \"sign\": \"$sign_key\",\n  \"status\": \"all\"\n}"
        ]);

        $response = $res->getBody();
        $arr_body = json_decode($response);
        //$response = Http::get('http://192.168.43.44:8080/api/home');


        $data= [
            'status' => $res->getStatusCode(),
            'operator' => $request->operator,
            'username' => $username,
            'sign_key' => $sign_key,
            'data' => $arr_body->data
        ];

        return response()->json($data);

    }
}
