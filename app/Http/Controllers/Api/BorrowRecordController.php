<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BorrowRecord;

class BorrowRecordController extends Controller
{
    public function index()
    {
        $borrowRecords = BorrowRecord::with(['user', 'book'])->paginate(15);
        return response()->json($borrowRecords);
    }

    public function show(BorrowRecord $borrowRecord)
    {
        return response()->json($borrowRecord->load(['user', 'book']));
    }
}
