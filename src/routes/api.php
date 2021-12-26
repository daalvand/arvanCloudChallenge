<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\VoucherController;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('register', [AuthController::class, 'register'])->name('auth.register');

Route::group(['middleware' => 'auth'], function () {
    Route::middleware('admin')->apiResource('vouchers', VoucherController::class);
    //add api for add voucher charge to user
    Route::post('vouchers/charge', [VoucherController::class, 'charge'])->name('vouchers.charge');

    Route::apiResource('transactions', TransactionController::class)->only(['index', 'show']);

    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    // watch user info and balance
    Route::get('me', [AuthController::class, 'me'])->name('auth.me');
});
