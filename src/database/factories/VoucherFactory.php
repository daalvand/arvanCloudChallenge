<?php

namespace Database\Factories;

use App\Helpers\Str;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startsAt  = $this->faker->dateTimeBetween('+1 hours', '+1 weeks');
        $expiresAt = $this->faker->dateTimeBetween($startsAt, '+1 months');
        return [
            'code'       => Str::unique(),
            'title'      => $this->faker->sentence,
            'user_id'    => User::factory()->admin(),
            'max_uses'   => $this->faker->numberBetween(10, 100),
            'used_count' => 0,
            'type'       => $this->faker->randomElement(Voucher::TYPES),
            'amount'     => $this->faker->numberBetween(100, 100000),
            'starts_at'  => $startsAt->format('Y-m-d H:i:s'),
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ];
    }

    public function expired($time = '1months'): static
    {
        return $this->state(function () use ($time) {
            return [
                'expires_at' => now()->sub($time)->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function started(string $time = '1months'): static
    {
        return $this->state(function () use ($time) {
            return [
                'starts_at' => now()->sub($time)->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function expiresAtFuture($time = '1months'): static
    {
        return $this->state(function () use ($time) {
            return [
                'expires_at' => now()->add($time)->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function startsAtFuture(string $time = '1months'): static
    {
        return $this->state(function () use ($time) {
            return [
                'expires_at' => now()->add($time)->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function isFull(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'used_count' => $attributes['max_uses'],
            ];
        });
    }

    public function discount(): static
    {
        return $this->state(function () {
            return [
                'type' => Voucher::DISCOUNT_TYPE,
            ];
        });
    }

    public function charge(): static
    {
        return $this->state(function () {
            return [
                'type' => Voucher::CHARGE_TYPE,
            ];
        });
    }
}
