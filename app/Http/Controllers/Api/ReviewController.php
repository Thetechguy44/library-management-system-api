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

    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = $book->reviews()->create([
            'user_id'=>$request->user()->id,
            'comment'=>$validated['comment'],
            'rating'=>$validated['rating'],
        ]);

        return response()->json($review, 201);
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review->update($validated);

        return response()->json($review);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json(null, 204);
    }
}
