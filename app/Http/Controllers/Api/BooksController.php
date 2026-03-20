<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Requests\BooksRequest;

class BooksController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::paginate(10);
        return response()->json(
            [
                "success" => true,
                "data" => $books,
            ],
            200,
        );
    }

    public function store(BooksRequest $request)
    {
        $data = $request->validated();
        Book::create($request->validated());
        return response()->json($data, 200);
    }
}
