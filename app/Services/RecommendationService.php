<?php
namespace App\Services;

use App\Models\User;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function getRecommendationsForUser(User $user, $limit = 5)
    {
        // Get titles of books the user has borrowed
        $userTitle = $user->borrowRecords()
            ->join('books', 'borrow_records.book_id', '=', 'books.id')
            ->pluck('books.author')
            ->unique();

        // Get popular books in those genres that the user hasn't borrowed yet
        $recommendedBooks = Book::whereIn('author', $userTitle)
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('book_id')
                    ->from('borrow_records')
                    ->where('user_id', $user->id);
            })
            ->withCount('borrowRecords')
            ->orderByDesc('borrow_records_count')
            ->limit($limit)
            ->get();

        return $recommendedBooks;
    }
}