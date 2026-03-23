<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BooksController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/user", function (Request $request) {
    return $request->user();
})->middleware("auth:sanctum");

Route::post("/auth/login", [AuthController::class, "login"]);
Route::post("/auth/register", [AuthController::class, "register"]);
Route::get("/auth/me", [AuthController::class, "me"])->middleware(
    "auth:sanctum",
);

// ----- livros ----- \\
Route::get("/book", [BooksController::class, "index"]);
Route::post("/book", [BooksController::class, "store"]);

// ---- Reviews ---- \\
Route::get("/review", [ReviewController::class, "index"]);
Route::post("/review", [ReviewController::class, "store"]);
