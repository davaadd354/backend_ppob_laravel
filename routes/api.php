<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Auth::routes();

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login_api'])->name('login_api');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register_api'])->name('register_api');




Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::post('/getDataOperator',[OperatorController::class,'getDataOperator']);
    Route::post('/checkout',[CheckoutController::class,'checkout']);
    Route::get('/dataTransaksi',[CheckoutController::class,'dataTransaksi']);
    Route::post('/save_edit_user',[UserController::class,'save_edit_user']);
});
