<?php

namespace Services;

use App\Models\Transaction;
use App\Models\User;
use App\Services\Wallet\TransactionService;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_deposit_transaction()
    {
        $user = User::factory()->balance()->create();
        app(TransactionService::class)->setUser($user)->deposit(100);
        /** @var \App\Models\Transaction $transaction */
        $transaction = $user->transactions()->first();
        $this->assertEquals(100, $transaction->amount);
        $this->assertEquals(100, $user->refresh()->balance);
        $this->assertEquals(Transaction::DEPOSIT_TYPE, $transaction->type);
        $this->assertTrue($transaction->confirmed);
    }

    /**
     * @test
     */
    public function it_check_fails_deposit_transaction()
    {
        $user = User::factory()->balance()->create();
        app(TransactionService::class)->setUser($user)->failDeposit(100);
        /** @var \App\Models\Transaction $transaction */
        $transaction = $user->transactions()->first();
        $this->assertEquals(100, $transaction->amount);
        $this->assertEquals(0, $user->balance);
        $this->assertEquals(Transaction::DEPOSIT_TYPE, $transaction->type);
        $this->assertFalse($transaction->confirmed);
    }


    /**
     * @test
     */
    public function it_check_not_confirmed_withdraw_transaction()
    {
        $user = User::factory()->balance()->create();
        app(TransactionService::class)->setUser($user)->withdraw(100);
        /** @var \App\Models\Transaction $transaction */
        $transaction = $user->transactions()->first();
        $this->assertEquals(100, $transaction->amount);
        $this->assertEquals(0, $user->balance);
        $this->assertEquals(Transaction::WITHDRAW_TYPE, $transaction->type);
        $this->assertFalse($transaction->confirmed);
    }

    /**
     * @test
     */
    public function it_check_confirmed_withdraw_transaction()
    {
        $user = User::factory()->balance(100)->create();
        app(TransactionService::class)->setUser($user)->withdraw(100);
        /** @var \App\Models\Transaction $transaction */
        $transaction = $user->transactions()->first();
        $this->assertEquals(100, $transaction->amount);
        $this->assertEquals(0, $user->refresh()->balance);
        $this->assertEquals(Transaction::WITHDRAW_TYPE, $transaction->type);
        $this->assertTrue($transaction->confirmed);
    }

        /**
     * @test
     */
    public function it_check_force_withdraw_transaction()
    {
        $user = User::factory()->balance()->create();
        app(TransactionService::class)->setUser($user)->forceWithdraw(100);
        /** @var \App\Models\Transaction $transaction */
        $transaction = $user->transactions()->first();
        $this->assertEquals(100, $transaction->amount);
        $this->assertEquals(-100, $user->refresh()->balance);
        $this->assertEquals(Transaction::WITHDRAW_TYPE, $transaction->type);
        $this->assertTrue($transaction->confirmed);
    }
}
