<?php

namespace Controllers\Transaction;

use App\Models\User;
use App\Models\Transaction;
use Tests\TestCase;

class ShowTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_unauthenticated_request()
    {
        $transaction = $this->createTransaction();
        $this->json('get', route('transactions.show', $transaction->id))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_check_not_exists_transaction()
    {
        $user = User::factory()->user()->create();
        $this->actingAs($user)->json('get', route('transactions.show', 1))->assertNotFound();
    }

    /**
     * @test
     */
    public function it_check_user_request()
    {
        $user        = User::factory()->user()->create();
        $transaction = $this->createTransaction($user->id);
        $this->actingAs($user)->json('get', route('transactions.show', $transaction->id))->assertOk();
    }

    /**
     * @test
     */
    public function it_check_show_structure()
    {
        $user        = User::factory()->user()->create();
        $transaction = $this->createTransaction($user->id);
        $response    = $this->actingAs($user)->json('get', route('transactions.show', $transaction->id));
        $this->assertCount(8, $response->json('data'));
        // Check structure simple paginate
        $response->assertJsonStructure([
            'data' => [
                "id",
                "user_id",
                "amount",
                "type",
                "confirmed",
                "meta",
                "created_at",
                "updated_at",
            ]
        ]);
    }

    protected function createTransaction(int $userId = null): Transaction
    {
        $factory = Transaction::factory();
        return $userId ? $factory->create(['user_id' => $userId]) : $factory->create();
    }
}
