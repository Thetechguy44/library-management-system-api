<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BorrowRecord;

class BorrowRecordController extends Controller
{
    #This fuction return all borrowed books info (for Admin and Librarian)
    public function index()
    {
        $borrowRecords = BorrowRecord::with(['user', 'book'])->paginate(15);
        return response()->json($borrowRecords);
    }

    #This fuction return a selected borrowed book info (for Admin and Librarian)
    public function show(BorrowRecord $borrowRecord)
    {
        return response()->json($borrowRecord->load(['user', 'book']));
    }
}
