<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'    => User::factory(),
            'amount'     => $this->faker->numberBetween(1000, 100000),
            'type'       => $this->faker->randomElement(Transaction::TYPES),
            'confirmed'  => $this->faker->boolean,
            'meta'       => null,
        ];
    }
}
