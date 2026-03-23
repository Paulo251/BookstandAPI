<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (["admin", "usuario"] as $roleName) {
            Role::query()->firstOrCreate(["name" => $roleName]);
        }
    }
}
