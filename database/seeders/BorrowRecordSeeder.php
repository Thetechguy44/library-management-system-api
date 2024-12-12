<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BorrowRecord;
use App\Models\User;
use App\Models\Book;
use Carbon\Carbon;

class BorrowRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            $this->command->info('No users or books found. Seeder skipped.');
            return;
        }

        foreach ($users as $user) {
            foreach ($books->random(3) as $book) { // Assign 3 random books to each user
                BorrowRecord::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'borrowed_at' => Carbon::now()->subDays(rand(1, 10)), // Random borrow date in the past
                    'due_at' => Carbon::now()->addDays(rand(7, 14)), // Random due date in the future
                    'returned_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 10)) : null, // Random return date or null
                ]);
            }
        }
    }
}
