<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            "email" => ["required", "string"],
            "password" => ["required", "string"],
        ]);

        $user = User::where("email", $credentials["email"])->first();

        if (!$user || !Hash::check($credentials["password"], $user->password)) {
            throw ValidationException::withMessages([
                "email" => ["Credenciais invalidas."],
            ]);
        }

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json(
            [
                "success" => true,
                "message" => "Login realizado com sucesso.",
                "token" => $token,
                "user" => $user->load("role"),
            ],
            200,
        );
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "email", "max:255", "unique:users,email"],
            "password" => ["required", "string", "min:8"]
        ]);

        $defaultRole = Role::query()->firstOrCreate(["name" => "usuario"]);
        $data["role_id"] = $defaultRole->id;

        $user = User::create($data);
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json(
            [
                "success" => true,
                "message" => "Usuario cadastrado com sucesso.",
                "token" => $token,
                "user" => $user->load("role"),
            ],
            201,
        );
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(
            [
                "success" => true,
                "user" => $request->user()?->load("role"),
            ],
            200,
        );
    }
}
