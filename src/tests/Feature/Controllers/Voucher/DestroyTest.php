<?php

namespace Controllers\Voucher;

use App\Models\User;
use App\Models\Voucher;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_unauthenticated_request()
    {
        $voucher = $this->createVoucher();
        $this->json('delete', route('vouchers.destroy', $voucher->id))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_check_non_admin_user_request()
    {
        $user    = User::factory()->user()->create();
        $voucher = $this->createVoucher();
        $this->actingAs($user)->json('delete', route('vouchers.destroy', $voucher->id))->assertForbidden();
    }


    /**
     * @test
     */
    public function it_check_not_exists_voucher()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user)->json('delete', route('vouchers.destroy', 1))->assertNotFound();
    }

    /**
     * @test
     */
    public function it_check_admin_user_request()
    {
        $user    = User::factory()->admin()->create();
        $voucher = $this->createVoucher();
        $this->actingAs($user)->json('delete', route('vouchers.destroy', $voucher->id))->assertNoContent();
    }


    protected function createVoucher(int $userId = null): Voucher
    {
        $factory = Voucher::factory();
        return $userId ? $factory->create(['user_id' => $userId]) : $factory->create();
    }
}
