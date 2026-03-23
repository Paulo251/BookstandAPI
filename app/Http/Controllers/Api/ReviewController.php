<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Http\Requests\ReviewRequest;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::paginate(10);

        return response()->json($reviews, 200);
    }

    public function store(ReviewRequest $request)
    {
        $data = $request->validated();
        Review::create($request->validated());
        return response()->json($data, 200);
    }
}
