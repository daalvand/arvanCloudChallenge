<?php

namespace Controllers\Voucher;

use App\Models\User;
use App\Models\Voucher;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_unauthenticated_request()
    {
        $voucher = $this->createVoucher();
        $this->json('patch', route('vouchers.update', $voucher->id))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_check_non_admin_user_request()
    {
        $user    = User::factory()->user()->create();
        $voucher = $this->createVoucher();
        $this->actingAs($user)->json('patch', route('vouchers.update', $voucher->id))->assertForbidden();
    }


    /**
     * @test
     */
    public function it_check_not_exists_voucher()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user)->json('patch', route('vouchers.update', 1))->assertNotFound();
    }

    /**
     * @test
     */
    public function it_check_admin_user_request()
    {
        $user    = User::factory()->admin()->create();
        $voucher = $this->createVoucher();
        $this->actingAs($user)->json('patch', route('vouchers.update', $voucher->id))->assertOk();
    }



    /**
     * @test
     */
    public function it_check_update_structure()
    {
        $user     = User::factory()->admin()->create();
        $voucher  = $this->createVoucher();
        $response = $this->actingAs($user)->json('patch', route('vouchers.update', $voucher->id));
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

        /**
     * @dataProvider validationDataProvider
     * @test
     * @return void
     */
    public function it_check_validation_update($data, $invalidParams)
    {
        $user = User::factory()->admin()->create();
        $voucher  = $this->createVoucher();
        $this->actingAs($user);
        if ($invalidParams) {
            $this->json('PATCH', route('vouchers.update', $voucher->id), $data)
                ->assertStatus(422)
                ->assertJsonValidationErrors($invalidParams);
        } else {
            $this->json('PATCH', route('vouchers.update', $voucher->id), $data)
                ->assertOk();
        }
    }

    /**
     * this provide data for validation
     */
    public function validationDataProvider(): array
    {
        return [
            [
                [
                    'title'      => null,
                    'type'       => 'invalid_type',
                    'expires_at' => now()->subDay()->format('Y-m-d H:i:s'),
                    'starts_at'  => now()->subDay()->format('Y-m-d H:i:s'),
                    'amount'     => 'invalid_amount',
                    'max_uses'   => 'invalid_max_uses',
                ],
                ['title', 'type', 'expires_at', 'starts_at', 'amount', 'max_uses']
            ],
            [$this->validData(), []],
        ];
    }

    protected function validData()
    {
        return [
            'title'      => 'title',
            'type'       => 'discount',
            'expires_at' => now()->addMonth()->format('Y-m-d H:i:s'),
            'starts_at'  => now()->addDay()->format('Y-m-d H:i:s'),
            'amount'     => 10,
            'max_uses'   => 10
        ];
    }
}
