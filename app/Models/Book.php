<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = "books";

    protected $fillable = ["title", "author", "synopsis", "release_date"];

    public function reviews(): hasMany
    {
        return $this->hasMany(Review::class);
    }
}
