<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Book;

/**
 * @OA\Schema(
 *   schema="Reservation",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="Reservation ID"),
 *   @OA\Property(property="user_id", type="integer", description="User ID"),
 *   @OA\Property(property="book_id", type="integer", description="Book ID"),
 *   @OA\Property(property="reserved_from", type="string", format="date", description="Reservation start date"),
 *   @OA\Property(property="reserved_to", type="string", format="date", description="Reservation end date"),
 *   @OA\Property(property="status", type="string", description="Reservation status (Confirmed, Cancelled)")
 * )
 */
class ReservationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     tags={"Reservations"},
     *     summary="Get a list of reservations",
     *     @OA\Response(
     *         response=200,
     *         description="A list of reservations",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Reservation"))
     *     )
     * )
     */
    public function index()
    {
        $reservations = Reservation::with(['user', 'book'])->paginate(15);
        return response()->json($reservations);
    }

    /**
     * @OA\Get(
     *     path="/api/reservations/{id}",
     *     tags={"Reservations"},
     *     summary="Get a reservation by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Reservation ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation details",
     *         @OA\JsonContent(ref="#/components/schemas/Reservation")
     *     )
     * )
     */
    public function show(Reservation $reservation)
    {
        return response()->json($reservation->load(['user', 'book']));
    }

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     tags={"Reservations"},
     *     summary="Create a new reservation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="book_id", type="integer", description="Book ID"),
     *             @OA\Property(property="reserved_from", type="string", format="date", description="Reservation start date"),
     *             @OA\Property(property="reserved_to", type="string", format="date", description="Reservation end date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reservation created",
     *         @OA\JsonContent(ref="#/components/schemas/Reservation")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Book not available",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Book is not available for reservation")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'reserved_from' => 'required|date|after:today',
            'reserved_to' => 'required|date|after:reserved_from',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        if ($book->status !== 'Available') {
            return response()->json([
                'message' => 'Book is not available for reservation'
            ], 400);
        }

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'book_id' => $validated['book_id'],
            'reserved_from' => $validated['reserved_from'],
            'reserved_to' => $validated['reserved_to'],
        ]);

        return response()->json($reservation, 201);
    }

    /**
     * @OA\Patch(
     *     path="/api/reservations/{id}",
     *     tags={"Reservations"},
     *     summary="Update a reservation status",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Reservation ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", description="Reservation status (Confirmed, Cancelled)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation updated",
     *         @OA\JsonContent(ref="#/components/schemas/Reservation")
     *     )
     * )
     */
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => 'required|in:Confirmed,Cancelled',
        ]);

        $reservation->update($validated);

        if ($validated['status'] === 'Confirmed') {
            $reservation->book->update(['status' => 'Borrowed']);
        } elseif ($validated['status'] === 'Cancelled') {
            $reservation->book->update(['status' => 'Available']);
        }

        return response()->json($reservation);
    }
}
