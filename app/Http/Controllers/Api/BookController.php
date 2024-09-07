<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    #This fuction returns all the books in the database with both search query by title, isnb, author or name 
    public function index(Request $request)
    {
        $query = Book::query();

        #query search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%$search%")
                  ->orWhere('isbn', 'like', "%$search%")
                  ->orWhereHas('author', function ($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
        }

        $books = $query->with('author')->paginate(15);
        return response()->json($books);
    }

    #This fuction return a particular book selected
    public function show(Book $book)
    {
        return response()->json($book->load('author'));
    }

    #This fuction store newly added or created books in the database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books',
            'published_date' => 'required|date',
            'author_id' => 'required|exists:authors,id',
            'status' => 'required|in:Available,Borrowed',
        ]);

        $book = Book::create($validated);
        return response()->json($book, 201);
    }

    #This fuction update a particular selected book
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'isbn' => 'sometimes|required|string|unique:books,isbn,' . $book->id,
            'published_date' => 'sometimes|required|date',
            'author_id' => 'sometimes|required|exists:authors,id',
            'status' => 'sometimes|required|in:Available,Borrowed',
        ]);

        $book->update($validated);
        return response()->json($book);
    }

    #This fuction deletes a selected book
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json([
            'message' => 'Book deleted successfully'
        ], 200);
    }

    #This fuction is use to borrow a book if it's available (for members only)
    public function borrow(Request $request, Book $book)
    {
        if ($book->status !== 'Available') {
            return response()->json(['message' => 'Book is not available for borrowing'], 400);
        }

        $book->status = 'Borrowed';
        $book->save();

        $borrowRecord = $book->borrowRecords()->create([
            'user_id' => $request->user()->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14), // 2 weeks borrowing period
        ]);

        return response()->json($borrowRecord, 201);
    }

    #This fuction return a borrowed book if it's borrowed (for members only)
    public function return(Book $book)
    {
        if ($book->status !== 'Borrowed') {
            return response()->json(['message' => 'Book is not borrowed'], 400);
        }

        $book->status = 'Available';
        $book->save();

        $borrowRecord = $book->borrowRecords()->latest()->first();
        $borrowRecord->returned_at = now();
        $borrowRecord->save();

        return response()->json($borrowRecord);
    }
}
