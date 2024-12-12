<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BorrowRecord;

/**
 * @OA\Schema(
 *   schema="BorrowRecord",
 *   type="object",
 *   @OA\Property(property="id", type="integer", format="int64", description="Record ID"),
 *   @OA\Property(property="user_id", type="integer", format="int64", description="User ID"),
 *   @OA\Property(property="book_id", type="integer", format="int64", description="Book ID"),
 *   @OA\Property(property="borrowed_at", type="string", format="date-time", description="Borrowed date and time"),
 *   @OA\Property(property="due_at", type="string", format="date-time", description="Due date and time"),
 *   @OA\Property(property="returned_at", type="string", format="date-time", description="Return date and time")
 * )
 */

class BorrowRecordController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/borrow-records",
     *     tags={"BorrowRecord"},
     *     summary="Retrieve all borrowed book records",
     *     @OA\Response(
     *         response=200,
     *         description="List of borrowed book records",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Current page"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/BorrowRecord")),
     *             @OA\Property(property="total", type="integer", description="Total number of records")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        $borrowRecords = BorrowRecord::with(['user', 'book'])->paginate(15);
        return response()->json($borrowRecords);
    }

    /**
     * @OA\Get(
     *     path="/api/borrow-records/{id}",
     *     tags={"BorrowRecord"},
     *     summary="Retrieve a specific borrowed book record",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the borrowed record",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Borrowed book record",
     *         @OA\JsonContent(ref="#/components/schemas/BorrowRecord")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Borrowed record not found"
     *     )
     * )
     */
    public function show(BorrowRecord $borrowRecord)
    {
        return response()->json($borrowRecord->load(['user', 'book']));
    }
}
