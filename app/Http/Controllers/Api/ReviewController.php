<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Book;

/**
 * @OA\Schema(
 *   schema="Review",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="Review ID"),
 *   @OA\Property(property="book_id", type="integer", description="Book ID"),
 *   @OA\Property(property="user_id", type="integer", description="User ID"),
 *   @OA\Property(property="comment", type="string", description="User comment on the book"),
 *   @OA\Property(property="rating", type="integer", description="User rating of the book (1 to 5)")
 * )
 */
class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/books/{book_id}/reviews",
     *     tags={"Reviews"},
     *     summary="Get a list of reviews for a book",
     *     @OA\Parameter(
     *         name="book_id",
     *         in="path",
     *         description="Book ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of reviews for the book",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Review"))
     *     )
     * )
     */
    public function index(Book $book)
    {
        $reviews = $book->reviews()->with('user')->paginate(15);
        return response()->json($reviews);
    }

    /**
     * @OA\Post(
     *     path="/api/books/{book_id}/reviews",
     *     tags={"Reviews"},
     *     summary="Create a new review for a book",
     *     @OA\Parameter(
     *         name="book_id",
     *         in="path",
     *         description="Book ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="comment", type="string", description="User comment"),
     *             @OA\Property(property="rating", type="integer", description="Rating between 1 and 5", minimum=1, maximum=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Review created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Review")
     *     )
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/api/reviews/{review_id}",
     *     tags={"Reviews"},
     *     summary="Update a review",
     *     @OA\Parameter(
     *         name="review_id",
     *         in="path",
     *         description="Review ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="comment", type="string", description="Updated comment"),
     *             @OA\Property(property="rating", type="integer", description="Updated rating between 1 and 5", minimum=1, maximum=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Review")
     *     )
     * )
     */
    public function update(Request $request, Review $review)
    {
            // Check if the authenticated user is the owner of the review
        if ($request->user()->id !== $review->user_id) {
            return response()->json(['message' => 'Forbidden'], 403); // Return 403 if not authorized
        }

        $validated = $request->validate([
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review->update($validated);

        return response()->json($review);
    }

    /**
     * @OA\Delete(
     *     path="/api/reviews/{review_id}",
     *     tags={"Reviews"},
     *     summary="Delete a review",
     *     @OA\Parameter(
     *         name="review_id",
     *         in="path",
     *         description="Review ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Review deleted successfully"
     *     )
     * )
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json(null, 204);
    }
}
