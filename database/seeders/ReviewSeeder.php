<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Book;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all users and books
        $users = User::all();
        $books = Book::all();

        // Ensure we have enough data to seed reviews
        if ($users->isEmpty() || $books->isEmpty()) {
            $this->command->info('No users or books available for reviews.');
            return;
        }

        // Loop through each user and create reviews for random books
        foreach ($users as $user) {
            foreach ($books->random(3) as $book) { // Each user reviews random 3 books
                Review::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'comment' => $faker->sentence(10), // Generate random comments
                    'rating' => $faker->numberBetween(1, 5), // Random rating between 1 and 5
                ]);
            }
        }
    }
}
