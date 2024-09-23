<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fine;
use App\Models\User;
use App\Models\BorrowRecord;

class FineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('role', 'Member')->get();
        $borrowRecords = BorrowRecord::all();

        if ($users->isEmpty() || $borrowRecords->isEmpty()) {
            $this->command->info('No users or borrow records found. Seeder skipped.');
            return;
        }

        foreach ($users as $user) {
            foreach ($borrowRecords->random(3) as $borrowRecord) { // Assign 3 random borrow records to each user
                Fine::create([
                    'user_id' => $user->id,
                    'borrow_record_id' => $borrowRecord->id,
                    'amount' => rand(5, 100), // Random fine amount between $5 and $100
                    'paid' => rand(0, 1) == 1 ? true : false, // Randomly set whether the fine is paid
                ]);
            }
        }
    }
}
