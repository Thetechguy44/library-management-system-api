<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fine;
use App\Models\BorrowRecord;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *   schema="Fine",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="Fine ID"),
 *   @OA\Property(property="user_id", type="integer", description="User ID"),
 *   @OA\Property(property="borrow_record_id", type="integer", description="Borrow Record ID"),
 *   @OA\Property(property="amount", type="number", format="float", description="Amount of fine"),
 *   @OA\Property(property="paid", type="boolean", description="Paid status")
 * )
 */

class FineController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/fines",
     *     tags={"Fines"},
     *     summary="Get a list of fines",
     *     @OA\Response(
     *         response=200,
     *         description="A list of fines",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Fine"))
     *     )
     * )
     */

    public function index()
    {
        $fines = Fine::with(['user', 'borrowRecord'])->paginate(15);
        return response()->json($fines);
    }

    /**
     * @OA\Get(
     *     path="/api/fines/{id}",
     *     tags={"Fines"},
     *     summary="Get a fine by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Fine ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fine details",
     *         @OA\JsonContent(ref="#/components/schemas/Fine")
     *     )
     * )
     */

    public function show(Fine $fine)
    {
        return response()->json($fine->load(['user', 'borrowRecord']));
    }

    /**
     * @OA\Post(
     *     path="/api/borrow-records/{borrowRecord}/calculate-fine",
     *     tags={"Fines"},
     *     summary="Calculate a fine for a borrow record",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Borrow Record ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Fine calculated",
     *         @OA\JsonContent(ref="#/components/schemas/Fine")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="No fine applicable",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No fine applicable")
     *         )
     *     )
     * )
     */
    public function calculateFine(BorrowRecord $borrowRecord)
    {
        $dueDate = Carbon::parse($borrowRecord->due_at);
        $returnDate = $borrowRecord->returned_at ? Carbon::parse($borrowRecord->returned_at) : Carbon::now();
        
        if ($returnDate->gt($dueDate)) {
            $daysLate = $returnDate->diffInDays($dueDate);
            $fineAmount = $daysLate * 1.00; // $1 per day late
            
            $fine = Fine::create([
                'user_id' => $borrowRecord->user_id,
                'borrow_record_id' => $borrowRecord->id,
                'amount' => $fineAmount,
            ]);
            
            return response()->json($fine, 201);
        }
        
        return response()->json(['message' => 'No fine applicable'], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/fines/{id}/pay",
     *     tags={"Fines"},
     *     summary="Pay a fine",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Fine ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fine paid",
     *         @OA\JsonContent(ref="#/components/schemas/FinePayment")
     *     )
     * )
     */

    /**
     * @OA\Schema(
     *   schema="FinePayment",
     *   type="object",
     *   @OA\Property(property="paid", type="boolean", description="Payment status")
     * )
     */

    public function payFine(Fine $fine)
    {
        $fine->paid = true;
        $fine->save();
        
        return response()->json($fine);
    }
}
