<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Book;

class ReviewController extends Controller
{
    public function index(Book $book)
    {
        $reviews = $book->reviews()->with('user')->paginate(15);
        return response()->json($reviews);
    }
}
