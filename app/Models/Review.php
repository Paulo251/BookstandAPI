<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ["book_id", "user_id", "note", "considerations"];

    public function books(): belongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }
}
