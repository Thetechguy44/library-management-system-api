<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fine;
use App\Models\BorrowRecord;
use Carbon\Carbon;

class FineController extends Controller
{
    public function index()
    {
        $fines = Fine::with(['user', 'borrowRecord'])->paginate(15);
        return response()->json($fines);
    }

    public function show(Fine $fine)
    {
        return response()->json($fine->load(['user', 'borrowRecord']));
    }

    // public function calculateFine(BorrowRecord $borrowRecord)
    // {
    //     $dueDate = Carbon::parse($borrowRecord->due_at);
    //     $returnDate = $borrowRecord->returned_at ? Carbon::parse($borrowRecord->returned_at) : Carbon::now();
        
    //     if ($returnDate->gt($dueDate)) {
    //         $daysLate = $returnDate->diffInDays($dueDate);
    //         $fineAmount = $daysLate * 1.00; // $1 per day late
            
    //         $fine = Fine::create([
    //             'user_id' => $borrowRecord->user_id,
    //             'borrow_record_id' => $borrowRecord->id,
    //             'amount' => $fineAmount,
    //         ]);
            
    //         return response()->json($fine, 201);
    //     }
        
    //     return response()->json(['message' => 'No fine applicable'], 200);
    // }

    // public function payFine(Fine $fine)
    // {
    //     $fine->paid = true;
    //     $fine->save();
        
    //     return response()->json($fine);
    // }
}
