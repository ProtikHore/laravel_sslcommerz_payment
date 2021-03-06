<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('clear', function(){
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
});


Route::get('/', [PaymentController::class, 'index']);
Route::post('/save/payment', [PaymentController::class, 'save']);
Route::post('/payment/success', [PaymentController::class, 'success']);
Route::post('/payment/cancel', [PaymentController::class, 'cancel']);
Route::post('/payment/fail', [PaymentController::class, 'fail']);