<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use IakID\IakApiPHP\Services\IAKPrepaid;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (preg_match('/api/', $request->server('REQUEST_URI'))) {
            return response()->json(['success' => true, 'message' => 'ini halaman home'], 200);
        }
        return view('home');
    }
}
