<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'read user']);
        Permission::create(['name' => 'create user']);

        Permission::create(['name' => 'delete role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'read role']);
        Permission::create(['name' => 'create role']);

        Permission::create(['name' => 'delete permission']);
        Permission::create(['name' => 'update permission']);
        Permission::create(['name' => 'read permission']);
        Permission::create(['name' => 'create permission']);

        // Permissions for Event
        Permission::create(['name' => 'delete event']);
        Permission::create(['name' => 'update event']);
        Permission::create(['name' => 'read event']);
        Permission::create(['name' => 'create event']);

        // Permissions for Candidate
        Permission::create(['name' => 'create candidate']);
        Permission::create(['name' => 'read candidate']);
        Permission::create(['name' => 'update candidate']);
        Permission::create(['name' => 'delete candidate']);
    }
}
