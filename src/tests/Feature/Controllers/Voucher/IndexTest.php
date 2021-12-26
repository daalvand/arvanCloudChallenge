<?php

namespace Controllers\Voucher;

use App\Models\User;
use App\Models\Voucher;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_unauthenticated_request()
    {
        $this->json('get', route('vouchers.index'))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_check_non_admin_user_request()
    {
        $user = User::factory()->user()->create();
        $this->actingAs($user)->json('get', route('vouchers.index'))->assertForbidden();
    }

    /**
     * @test
     */
    public function it_check_admin_user_request()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user)->json('get', route('vouchers.index'))->assertOk();
    }

    /**
     * @test
     */
    public function it_check_index_structure()
    {
        $user = User::factory()->admin()->create();
        Voucher::factory()->count(10)->create();
        $response = $this->actingAs($user)->json('get', route('vouchers.index'), ['per_page' => 5]);
        $this->assertCount(5, $response->json('data'));
        // Check structure simple paginate
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    "id",
                    "title",
                    "code",
                    "user_id",
                    "max_uses",
                    "used_count",
                    "type",
                    "amount",
                    "starts_at",
                    "expires_at",
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
