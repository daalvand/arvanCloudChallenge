<?php

use App\Models\Voucher;
use Tests\TestCase;

class VoucherCacheTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_usable_method_cache()
    {
        Voucher::factory()->create();
        DB::enableQueryLog();
        Voucher::usable()->get();
        $this->assertCount(1, DB::getQueryLog());
        DB::flushQueryLog();
        Voucher::usable()->get();
        $this->assertCount(0, DB::getQueryLog());
    }

    /**
     * @test
     */
    public function it_check_usable_method_cache_with_time_travel()
    {
        Voucher::factory()->create();
        DB::enableQueryLog();
        Voucher::usable()->get();
        $this->assertCount(1, DB::getQueryLog());
        DB::flushQueryLog();
        Voucher::usable()->get();
        $this->assertCount(0, DB::getQueryLog());
        $this->travelTo(now()->addMinute());
        DB::flushQueryLog();
        Voucher::usable()->get();
        $this->assertCount(1, DB::getQueryLog());
    }


    /**
     * @test
     */
    public function it_check_cache_after_update()
    {
        $voucher = Voucher::factory()->create();
        DB::enableQueryLog();
        Voucher::usable()->get();
        $this->assertCount(1, DB::getQueryLog());
        DB::flushQueryLog();
        Voucher::usable()->get();
        $this->assertCount(0, DB::getQueryLog());
        $voucher->update(['max_uses' => 100]);
        DB::flushQueryLog();
        Voucher::usable()->get();
        $this->assertCount(1, DB::getQueryLog());
    }
}
