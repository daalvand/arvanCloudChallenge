<?php

namespace Controllers\Voucher;

use App\Helpers\Str;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Voucher;
use Tests\TestCase;

class ChargeTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_unauthenticated_request()
    {
        $this->json('post', route('vouchers.charge'))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_check_invalid_code_user_request()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user)
            ->json('post', route('vouchers.charge'), ['code' => Str::unique()])
            ->assertJsonValidationErrors('code');
    }

    /**
     * @test
     */
    public function it_check_expired_code_user_request()
    {
        $user    = User::factory()->admin()->create();
        $voucher = Voucher::factory()->expired()->create();
        $this->actingAs($user)
            ->json('post', route('vouchers.charge'), ['code' => $voucher->code])
            ->assertJsonValidationErrors('code');
    }

    /**
     * @test
     */
    public function it_check_not_started_code_user_request()
    {
        $user    = User::factory()->admin()->create();
        $voucher = Voucher::factory()->startsAtFuture()->create();
        $this->actingAs($user)
            ->json('post', route('vouchers.charge'), ['code' => $voucher->code])
            ->assertJsonValidationErrors('code');
    }

    /**
     * @test
     */
    public function it_check_is_full_code_user_request()
    {
        $user    = User::factory()->admin()->create();
        $voucher = Voucher::factory()->isFull()->create();
        $this->actingAs($user)
            ->json('post', route('vouchers.charge'), ['code' => $voucher->code])
            ->assertJsonValidationErrors('code');
    }

    /**
     * @test
     */
    public function it_check_discount_code()
    {
        $user    = User::factory()->admin()->create();
        $voucher = Voucher::factory()->discount()->create();
        $this->actingAs($user)
            ->json('post', route('vouchers.charge'), ['code' => $voucher->code])
            ->assertJsonValidationErrors('code');
    }

    /**
     * @test
     */
    public function it_check_duplicate_request()
    {
        $user    = User::factory()->admin()->create();
        $voucher = Voucher::factory()->charge()->started()->expiresAtFuture()->create();
        $this->actingAs($user);
        $this->json('post', route('vouchers.charge'), ['code' => $voucher->code])->assertOk();
        $this->json('post', route('vouchers.charge'), ['code' => $voucher->code])->assertJsonValidationErrors('code');
    }


    /**
     * @test
     */
    public function it_check_transaction_amount()
    {
        $user    = User::factory()->admin()->create();
        $voucher = Voucher::factory()->charge()->started()->expiresAtFuture()->create();
        $this->actingAs($user)->json('post', route('vouchers.charge'), ['code' => $voucher->code])->assertOk();
        /** @var \App\Models\Transaction $transaction */
        $transaction = $user->transactions()->first();
        $this->assertEquals($voucher->amount, $transaction->amount);
    }

    /**
     * @test
     */
    public function it_check_balance_amount()
    {
        $user    = User::factory()->admin()->create();
        $voucher = Voucher::factory()->charge()->started()->expiresAtFuture()->create();
        $this->actingAs($user)->json('post', route('vouchers.charge'), ['code' => $voucher->code])->assertOk();
        $this->assertEquals($user->balance, $voucher->amount);
    }

    /**
     * @test
     */
    public function it_check_transaction_type()
    {
        $user    = User::factory()->admin()->create();
        $voucher = Voucher::factory()->charge()->started()->expiresAtFuture()->create();
        $this->actingAs($user)->json('post', route('vouchers.charge'), ['code' => $voucher->code])->assertOk();
        /** @var \App\Models\Transaction $transaction */
        $transaction = $user->transactions()->first();
        $this->assertEquals(Transaction::DEPOSIT_TYPE, $transaction->type);
    }

    /**
     * @test
     */
    public function it_check_redeemed_voucher()
    {
        $user    = User::factory()->admin()->create();
        $voucher = Voucher::factory()->charge()->started()->expiresAtFuture()->create();
        $this->actingAs($user)->json('post', route('vouchers.charge'), ['code' => $voucher->code])->assertOk();
        $redeemedVoucher = $user->redeemedVouchers->first();
        $this->assertEquals($redeemedVoucher->id, $voucher->id);
    }
}
