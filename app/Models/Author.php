<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BorrowRecord;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'bio', 'birthdate'];

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }
}
