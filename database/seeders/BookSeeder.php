<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a few sample books
        Book::create([
            'title' => 'The Hobbit',
            'isbn' => '978-0345339683',
            'author_id' => 1, // Assuming J.R.R. Tolkien has ID 1
            'published_date' => '1937-09-21',
            'status' => 'Available',
        ]);

        Book::create([
            'title' => 'Harry Potter and the Philosopher\'s Stone',
            'isbn' => '978-0747532699',
            'author_id' => 2, // Assuming J.K. Rowling has ID 2
            'published_date' => '1997-06-26',
            'status' => 'Available',
        ]);

        Book::create([
            'title' => 'A Game of Thrones',
            'isbn' => '978-0553103540',
            'author_id' => 3, // Assuming George R.R. Martin has ID 3
            'published_date' => '1996-08-06',
            'status' => 'Available',
        ]);

        Book::create([
            'title' => 'Murder on the Orient Express',
            'isbn' => '978-0062693662',
            'author_id' => 4, // Assuming Agatha Christie has ID 4
            'published_date' => '1934-01-01',
            'status' => 'Available',
        ]);

        Book::create([
            'title' => 'The Shining',
            'isbn' => '978-0307743657',
            'author_id' => 5, // Assuming Stephen King has ID 5
            'published_date' => '1977-01-28',
            'status' => 'Available',
        ]);
    }
}
