<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Fine;
use App\Models\BorrowRecord;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fine>
 */
class FineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Fine::class;
    
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'borrow_record_id' => BorrowRecord::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 100), // Fine amount between 1 and 100
            'paid' => $this->faker->boolean,  // Randomly set 'paid' to true or false
        ];
    }
}
