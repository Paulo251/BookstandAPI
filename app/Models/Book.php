<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        "title",
        "author",
        "synopsis",
        "realease_data",
        "comunity_note",
    ];
}
