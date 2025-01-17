<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Seed roles and permissions into the database.
     *
     * Creates roles (`admin`, `employee`, `customer`) and assigns relevant permissions to each role. 
     * Admin gets all permissions, employees receive specific permissions, 
     * and customers have no permissions assigned.
     *
     * @return void
     */
    public function run(): void
    {
        // Define roles
        $roles = ['admin', 'employee', 'customer'];
        $roleInstances = [];

        // Create roles
        foreach ($roles as $roleName) {
            $roleInstances[$roleName] = Role::create(['name' => $roleName]);
        }

        // Define permissions
        $permissions = [
            'add-employee',
            'add-customer',
            'assign-customer',
            'add-action',
            'delete-customer',
            'delete-employee',
        ];

        // Create permissions
        $permissionInstances = [];
        foreach ($permissions as $permissionName) {
            $permissionInstances[$permissionName] = Permission::create(['name' => $permissionName]);
        }

        // Assign permissions to roles
        $roleInstances['admin']->givePermissionTo($permissions); // Admin gets all permissions
        $roleInstances['employee']->givePermissionTo([ // Employee gets specific permissions
            'add-customer',
            'add-action',
        ]);

    }
}
