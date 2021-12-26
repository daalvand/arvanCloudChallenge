<?php

namespace Controllers\Transaction;

use App\Models\User;
use App\Models\Transaction;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_unauthenticated_request()
    {
        $this->json('get', route('transactions.index'))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_check_admin_user_request()
    {
        $user = User::factory()->user()->create();
        $this->actingAs($user)->json('get', route('transactions.index'))->assertOk();
    }

    /**
     * @test
     */
    public function it_check_index_structure()
    {
        $user = User::factory()->user()->create();
        Transaction::factory()->count(10)->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->json('get', route('transactions.index'), ['per_page' => 5]);
        $this->assertCount(5, $response->json('data'));
        // Check structure simple paginate
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    "id",
                    "user_id",
                    "amount",
                    "type",
                    "confirmed",
                    "meta",
                    "created_at",
                    "updated_at",
                ]
            ],
            "current_page",
            "first_page_url",
            "from",
            "next_page_url",
            "path",
            "per_page",
            "prev_page_url",
            "to",
        ]);
    }
}
