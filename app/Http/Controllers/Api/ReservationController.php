<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Book;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['user', 'book'])->paginate(15);
        return response()->json($reservations);
    }

    public function show(Reservation $reservation)
    {
        return response()->json($reservation->load(['user', 'book']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'reserved_from' => 'required|date|after:today',
            'reserved_to' => 'required|date|after:reserved_from',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        if ($book->status !== 'Available') {
            return response()->json(['message' => 'Book is not available for reservation'], 400);
        }

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'book_id' => $validated['book_id'],
            'reserved_from' => $validated['reserved_from'],
            'reserved_to' => $validated['reserved_to'],
        ]);

        return response()->json($reservation, 201);
    }
}
