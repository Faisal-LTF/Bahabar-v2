<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Superadmin role with all permissions
        $superadmin = Role::create([
            'name'          => 'superadmin'
        ]);
        $superadmin->givePermissionTo([
            'delete user',
            'update user',
            'read user',
            'create user',
            'delete role',
            'update role',
            'read role',
            'create role',
            'delete permission',
            'update permission',
            'read permission',
            'create permission',
            'delete event',
            'update event',
            'read event',
            'create event'
        ]);

        // Admin role with specific permissions
        $admin = Role::create([
            'name'          => 'admin'
        ]);
        $admin->givePermissionTo([
            'delete user',
            'update user',
            'read user',
            'create user',
            'read role',
            'read permission',
            'read event',
            'create event',
            'update event'
        ]);

        // Operator role with limited permissions
        $operator = Role::create([
            'name'          => 'operator'
        ]);
        $operator->givePermissionTo([
            'read user',
            'create user',
            'read role',
            'read permission',
            'read event'
        ]);
    }
}
