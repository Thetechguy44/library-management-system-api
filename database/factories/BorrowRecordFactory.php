<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Models\BorrowRecord;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BorrowRecord>
 */
class BorrowRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),  // Create a user
            'book_id' => \App\Models\Book::factory(),  // Create a book
            'borrowed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'due_at' => Carbon::parse($this->faker->dateTimeBetween('now', '+1 month')),
            'returned_at' => $this->faker->date, // You can also set a random return date if needed
        ];
    }
}
