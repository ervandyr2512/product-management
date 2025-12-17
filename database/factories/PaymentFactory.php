<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'success', 'failed', 'refunded']);

        return [
            'payment_gateway_id' => fake()->uuid(),
            'amount' => fake()->randomFloat(2, 100000, 500000),
            'status' => $status,
            'payment_method' => fake()->randomElement(['credit_card', 'bank_transfer', 'e-wallet']),
            'payment_details' => json_encode([
                'method' => fake()->creditCardType(),
                'last4' => fake()->numberBetween(1000, 9999),
            ]),
            'paid_at' => $status === 'success' ? fake()->dateTimeBetween('-30 days', 'now') : null,
        ];
    }
}
