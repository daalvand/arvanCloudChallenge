<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $mobile = $this->faker->unique()->numberBetween('9100000000', '9399999999');
        return [
            'name'               => $this->faker->name,
            'mobile'             => $mobile,
            'password'           => Hash::make('password'),
            'balance'            => 0,
            'type'               => $this->faker->randomElement(User::TYPES),
            'mobile_verified_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'mobile_verified_at' => null,
            ];
        });
    }

    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => User::ADMIN_TYPE,
            ];
        });
    }

    public function user(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => User::USER_TYPE,
            ];
        });
    }

    public function balance(int $balance = 0): static
    {
        return $this->state(function (array $attributes) use ($balance) {
            return ['balance' => $balance,];
        });
    }
}
