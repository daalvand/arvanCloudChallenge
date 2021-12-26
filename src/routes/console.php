<?php

use App\Models\Voucher;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    DB::enableQueryLog();
//    $v = Voucher::factory()->charge()->started()->expiresAtFuture()->create();
    //$v = Voucher::factory()->count(10)->create();

    $v = \App\Models\Voucher::usable()->get();
//    dd($v);
    dd(DB::getQueryLog());
})->purpose('Display an inspiring quote');
