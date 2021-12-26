<?php

namespace Controllers\Voucher;

use App\Models\User;
use App\Models\Voucher;
use Tests\TestCase;

class ShowTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_unauthenticated_request()
    {
        $voucher = $this->createVoucher();
        $this->json('get', route('vouchers.show', $voucher->id))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_check_non_admin_user_request()
    {
        $user    = User::factory()->user()->create();
        $voucher = $this->createVoucher();
        $this->actingAs($user)->json('get', route('vouchers.show', $voucher->id))->assertForbidden();
    }


    /**
     * @test
     */
    public function it_check_not_exists_voucher()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user)->json('get', route('vouchers.show', 1))->assertNotFound();
    }

    /**
     * @test
     */
    public function it_check_admin_user_request()
    {
        $user    = User::factory()->admin()->create();
        $voucher = $this->createVoucher();
        $this->actingAs($user)->json('get', route('vouchers.show', $voucher->id))->assertOk();
    }

    /**
     * @test
     */
    public function it_check_show_structure()
    {
        $user     = User::factory()->admin()->create();
        $voucher  = $this->createVoucher();
        $response = $this->actingAs($user)->json('get', route('vouchers.show', $voucher->id));
        $this->assertCount(12, $response->json('data'));
        // Check structure simple paginate
        $response->assertJsonStructure([
            'data' => [
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
        ]);
    }

    protected function createVoucher(int $userId = null): Voucher
    {
        $factory = Voucher::factory();
        return $userId ? $factory->create(['user_id' => $userId]) : $factory->create();
    }
}
