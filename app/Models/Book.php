<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Author;
use App\Models\BorrowRecord;
use App\Models\Reservation;
use App\Models\Review;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'published_date',
        'author_id',
        'status',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
