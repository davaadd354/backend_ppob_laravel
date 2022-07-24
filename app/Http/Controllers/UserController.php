<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Image;
use File;

class UserController extends Controller
{
    public function save_edit_user(Request $request){
      
                $request->validate([
                    'telp' => 'required|string',
                    'nama_lengkap' => 'required|string',
                    'jenis_kelamin' => 'required|string',
                    'imageUrl' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048'
                ]);

                $user_id = Auth::user()->id;

                $data_user = DB::table('users')->where('id',$user_id)->first();
        
                $data_update = [
                    'name' => $request->nama_lengkap,
                    'telp' => $request->telp,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'alamat' => $request->alamat
                ];

                if($request->imageUrl){
                    return response()->json('testing dulu');
                    $file = $request->file('imageUrl');
                    $nama_asli = $file->getClientOriginalName();
                    $nama_custom = time().'_'.$nama_asli;
                    $image = Image::make($file);
                    $image->save(public_path('gambar_asli/'.$nama_custom));
                    $image->resize(300,300);
                    $image->save(public_path('gambar_custom/'.$nama_custom));

                   if($data_user->imageUrl){
                    File::delete(public_path('gambar_asli/'.$data_user->imageUrl));
                    File::delete(public_path('gambar_custom/'.$data_user->imageUrl));
                   }

                    $data_update['imageUrl'] = $nama_custom;
                    $data_update['path_image_asli'] = 'gambar_asli/';
                    $data_update['path_image_custom'] = 'gambar_custom/'; 
                }
        
        
                DB::table('users')->where('id',$user_id)->update($data_update);

                $user = DB::table('users')->where('id',$user_id)->first();

                $data = [
                    'status' => 'berhasil',
                    'data' => $user
                ];

                return response()->json($data,200);
      
    }
}
