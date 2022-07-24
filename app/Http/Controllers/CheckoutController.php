<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class CheckoutController extends Controller
{
  
    public function checkout(Request $request){

        $request->validate([
            'telp' => 'required|string',
            'harga' => 'required|numeric',
            'product_code' => 'required|string',
            'product_description' => 'required|string',
            'product_nominal' => 'required|string'
        ]);
        
        $telp = $request->telp;
        $harga = $request->harga;
        $product_code = $request->product_code;
        $product_description = $request->product_description;
        $product_nominal = $request->product_nominal;
        $product_details = $request->product_details;
        $user_id = Auth::user()->id;
        $transaksi_id = rand();

        $username = env('USERNAME_IAK_ID');
        $dev_key = env('DEVELOPMENT_KEY_IAK_ID');
        $sign_key = md5($username.$dev_key.$transaksi_id);

        $url = 'https://prepaid.iak.dev/api/top-up';

        $client = new Client(['verify' => false]);
        
        $res = $client->request('POST', $url, [
            'headers' => [
                'Content-Type'     => 'application/json',
            ],
            'body' => "{\n  \"username\": \"$username\",\n  \"customer_id\": \"$telp\",\n  \"ref_id\": \"$transaksi_id\",\n  \"product_code\": \"$product_code\",\n  \"sign\": \"$sign_key\"\n}"
        ]);

        $response = $res->getBody();
        $arr_body = json_decode($response);

        if($product_details){
            $data_insert['product_details'] = $product_details;
        }

        $data_insert = [
            'user_id' => $user_id,
            'transaksi_id' => $transaksi_id,
            'harga' => $harga,
            'customer_id' => $telp,
            'product_code' => $product_code,
            'product_description' => $product_description,
            'product_nominal' => $product_nominal,
            'status' => $arr_body->data->status,
            'sign_key' => $sign_key
        ];

        if($arr_body->data->status == 2){
            return response()->json($arr_body, 400);
        }else{
            DB::table('transaksi')->insert($data_insert);
        }

        $data = [
            'telp' => $request->telp,
            'harga' => $request->harga,
            'product_code' => $product_code,
            'product_description' => $product_description,
            'product_nominal' => $product_nominal,
            'product_details' => $product_details,
            'user_id' => $user_id,
            'transaksi_id' => $transaksi_id,
            'data' => $arr_body->data
        ];

        return response()->json($data);
    }

    public function dataTransaksi(Request $request){
        $request->validate([
            'user_id' => 'required|numeric'
        ]);

        $user_id = $request->user_id;

        $data_transaksi_pending =  DB::table('transaksi')->where('user_id',$user_id)->where('status',0)->get();

        $username = env('USERNAME_IAK_ID');
        $dev_key = env('DEVELOPMENT_KEY_IAK_ID');

        foreach($data_transaksi_pending as $p){
            $url = 'https://prepaid.iak.dev/api/check-status';

            $client = new Client(['verify' => false]);
            
            $res = $client->request('POST', $url, [
                'headers' => [
                    'Content-Type'     => 'application/json',
                ],
                'body' => "{\n  \"username\": \"$username\",\n  \"ref_id\": \"$p->transaksi_id\",\n  \"sign\": \"$p->sign_key\"\n}"
            ]);

            $response = json_decode($res->getBody());
            if($response->data->status != 0){
                DB::table('transaksi')->where('transaksi_id',$p->transaksi_id)->update(['status' => $response->data->status]);
            }         
        }

        $data_transaksi = DB::table('transaksi')->where('user_id',$user_id)->orderBy('id','desc')->get();

        $data = [
            'data' => $data_transaksi
        ];

        return response()->json($data);
    }
}
