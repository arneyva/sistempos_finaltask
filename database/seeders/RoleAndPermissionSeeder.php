<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        //{{==================================================================}}\\
        //{{=============================== Permissions ==============================}}\\
        //{{==================================================================}}\\

        //------------------------------- product --------------------------\\
        //------------------------------------------------------------------\\

        $ProductPermissions = [
            'create product',
            'edit product',
            'delete product',
            'view product',
        ];
        foreach ($ProductPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }
        // unit product
        $unitProductPermissions = [];

        //------------------------------- adjustment --------------------------\\
        //------------------------------------------------------------------\\

        $AdjustmentPermissions = [
            'create adjustment',
            'edit adjustment',
            'delete adjustment',
            'view adjustment',
        ];
        foreach ($AdjustmentPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        //------------------------------- Users --------------------------\\
        //------------------------------------------------------------------\\

        $UserPermissions = [
            'create user',
            'edit user',
            'delete user',
            'view user',
        ];
        foreach ($UserPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        //------------------------------- POS --------------------------\\
        //------------------------------------------------------------------\\

            Permission::firstOrCreate(['name' => 'cashier']);

        //{{=========================================================================}}\\
        //{{==================================================================}}\\
        //{{==========================================================}}\\

        //create role and give permission into it
        $role1 = Role::firstOrCreate(['name' => 'superadmin']);

        $role2 = Role::firstOrCreate(['name' => 'inventaris']);
        $role2->givePermissionTo([
            $ProductPermissions,
            $AdjustmentPermissions,
        ]);

        $role3 = Role::firstOrCreate(['name' => 'staff']);
        $role3->givePermissionTo([]);
    }
}
