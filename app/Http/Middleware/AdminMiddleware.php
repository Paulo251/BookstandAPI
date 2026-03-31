<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        $user = $request->user()?->loadMissing("role");

        if (!$user || $user->role?->name !== "admin") {
            return response()->json([
                "success" => false,
                "message" => "Apenas administradores podem cadastrar livros.",
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
