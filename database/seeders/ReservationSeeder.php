<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Book;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with the "Member" role
        $members = User::where('role', 'Member')->get();

        // Get some books
        $books = Book::all();

        // Ensure we have enough data
        if ($members->isEmpty() || $books->isEmpty()) {
            $this->command->info('No members or books available for reservations.');
            return;
        }

        foreach ($members as $member) {
            foreach ($books->random(3) as $book) { // Reserve random books for each member
                Reservation::create([
                    'user_id' => $member->id,
                    'book_id' => $book->id,
                    'reserved_from' => Carbon::now(),
                    'reserved_to' => Carbon::now()->addDays(7), // 7-day reservation
                ]);
            }
        }
    }
}
