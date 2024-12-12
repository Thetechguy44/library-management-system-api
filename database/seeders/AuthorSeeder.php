<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a few sample authors
        Author::create([
            'name' => 'J.K. Rowling',
            'bio' => 'British author, best known for the Harry Potter series.',
            'birthdate' => '1965-07-31',
        ]);

        Author::create([
            'name' => 'George R.R. Martin',
            'bio' => 'American novelist and short story writer, known for the A Song of Ice and Fire series.',
            'birthdate' => '1948-09-20',
        ]);

        Author::create([
            'name' => 'J.R.R. Tolkien',
            'bio' => 'English writer, best known for The Hobbit and The Lord of the Rings.',
            'birthdate' => '1892-01-03',
        ]);

        Author::create([
            'name' => 'Agatha Christie',
            'bio' => 'English writer known for her detective novels, including Hercule Poirot and Miss Marple.',
            'birthdate' => '1890-09-15',
        ]);

        Author::create([
            'name' => 'Stephen King',
            'bio' => 'American author known for his horror novels, including The Shining and It.',
            'birthdate' => '1947-09-21',
        ]);
    }
}
