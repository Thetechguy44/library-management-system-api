<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Author;

class AuthorController extends Controller
{
    #this function is for display all the authors in the database
    public function index()
    {
        $authors = Author::paginate(15);
        return response()->json($authors);
    }

    #this function is for show a particular author from the database
    public function show(Author $author)
    {
        return response()->json($author);
    }

    #this function is for store or save a newly added or created author in the database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'birthdate' => 'nullable|date',
        ]);

        $author = Author::create($validated);
        return response()->json($author, 201);
    }

    #this function is for update the info of a particular author in the database
    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'bio' => 'nullable|string',
            'birthdate' => 'nullable|date',
        ]);

        $author->update($validated);
        return response()->json($author);
    }

    #this function is for deleting an author from the database
    public function destroy(Author $author)
    {
        $author->delete();
        return response()->json([
            'message' => 'Author deleted successfully'
        ], 200);
    }
}
