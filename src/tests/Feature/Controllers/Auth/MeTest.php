<?php

namespace Controllers\Auth;

use App\Models\User;
use Tests\TestCase;

class MeTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_unauthorized_user()
    {
        $this->json('get', route('auth.me'))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_check_user_info()
    {
        $user = User::factory()->user()->create();
        $response = $this->actingAs($user)->json('get', route('auth.me'));
        $response->assertOk();
        $json = $response->json();
        $expectedJson = $user->toArray();
        sort($json);
        sort($expectedJson);
        $this->assertEquals($expectedJson, $json);
    }

}
