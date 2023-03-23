<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount' => $this->faker->numberBetween(1000, 100000),
            'term' => $this->faker->numberBetween(1, 12),
            'state' => $this->faker->randomElement(['PENDING', 'APPROVED', 'REJECTED']),
            'user_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
