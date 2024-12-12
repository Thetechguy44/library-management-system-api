<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Author;

/**
 * @OA\Schema(
 *   schema="Author",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="Author ID"),
 *   @OA\Property(property="name", type="string", description="Author's name"),
 *   @OA\Property(property="bio", type="string", description="Author's biography", nullable=true),
 *   @OA\Property(property="birthdate", type="string", format="date", description="Author's birthdate", nullable=true)
 * )
 */

class AuthorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/authors",
     *     tags={"Authors"},
     *     summary="Get all authors",
     *     @OA\Response(
     *         response=200,
     *         description="A list of authors",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Author"))
     *     )
     * )
     */
    #this function is for display all the authors in the database
    public function index()
    {
        $authors = Author::paginate(15);
        return response()->json($authors);
    }

    /**
     * @OA\Get(
     *     path="/api/authors/{author}",
     *     tags={"Authors"},
     *     summary="Get a specific author",
     *     @OA\Parameter(
     *         name="author",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Author details",
     *         @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Author not found"
     *     )
     * )
     */
    #this function is for show a particular author from the database
    public function show(Author $author)
    {
        return response()->json($author);
    }

    /**
     * @OA\Post(
     *     path="/api/authors",
     *     tags={"Authors"},
     *     summary="Create a new author",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Author created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Author")
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/authors/{author}",
     *     tags={"Authors"},
     *     summary="Update an existing author",
     *     @OA\Parameter(
     *         name="author",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Author updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Author not found"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/authors/{author}",
     *     tags={"Authors"},
     *     summary="Delete an author",
     *     @OA\Parameter(
     *         name="author",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Author deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Author not found"
     *     )
     * )
     */
    #this function is for deleting an author from the database
    public function destroy(Author $author)
    {
        $author->delete();
        return response()->json([
            'message' => 'Author deleted successfully'
        ], 200);
    }
}
