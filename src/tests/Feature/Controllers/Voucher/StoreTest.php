<?php

namespace Controllers\Voucher;

use App\Models\User;
use App\Models\Voucher;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_unauthenticated_request()
    {
        $this->json('post', route('vouchers.store'))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_check_non_admin_user_request()
    {
        $user = User::factory()->user()->create();
        $this->actingAs($user)->json('post', route('vouchers.store'))->assertForbidden();
    }

    /**
     * @test
     */
    public function it_check_admin_user_request()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user)->json('post', route('vouchers.store'), $this->validData())->assertCreated();
    }

    /**
     * @test
     */
    public function it_check_store_structure()
    {

        $user     = User::factory()->admin()->create();
        $response = $this->actingAs($user)->json('post', route('vouchers.store'), $this->validData());
        $this->assertCount(11, $response->json('data'));
        // Check structure simple paginate
        $response->assertJsonStructure([
            'data' => [
                "id",
                "title",
                "code",
                "user_id",
                "max_uses",
                "type",
                "amount",
                "starts_at",
                "expires_at",
                "created_at",
                "updated_at",
            ]
        ]);
    }

    /**
     * @dataProvider validationDataProvider
     * @test
     * @return void
     */
    public function it_check_validation_store($data, $invalidParams)
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);
        if ($invalidParams) {
            $this->json('POST', route('vouchers.store'), $data)
                ->assertStatus(422)
                ->assertJsonValidationErrors($invalidParams);
        } else {
            $this->json('POST', route('vouchers.store'), $data)
                ->assertStatus(201);
        }
    }

    /**
     * this provide data for validation
     * $data, $invalidParams
     * $data = [title => string, type => between('charge', 'discount'), expires_at => datetime (after now and after starts_at), starts_at => datetime(after now), amount => integer, max_uses => integer]
     * $invalidParams =
     */
    public function validationDataProvider()
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
