<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API_DepositDetailsController;
use App\Http\Controllers\AuthApiController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/userApi', function (Request $request) {
    return $request->user();
});


Route::post('/Apilogin', AuthApiController::class)->middleware('guest:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customer-report/{customerId}', [API_DepositDetailsController::class, 'getReport']);
});
