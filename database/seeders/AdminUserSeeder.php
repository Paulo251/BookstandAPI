<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->firstOrCreate(["name" => "admin"]);

        User::query()->updateOrCreate(
            ["email" => "admin"],
            [
                "name" => "Administrador",
                "password" => "12345678",
                "role_id" => $adminRole->id,
            ],
        );
    }
}
