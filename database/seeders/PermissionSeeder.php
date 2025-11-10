<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // php artisan db:seed --class=PermissionSeeder
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define modules and actions
        $modules = [
            'upload',
            'page builder',
            'menu',
            'blog',
            'client profile',
            'result recording',
            'random selection',
            'lab admin',
            'section',
            'service',
            'background',
            'portfolio',
            'test',
            'team',
            'gallery',
            'career',
            'page',
            'contact message',
            'subscribe',
            'setting',
            'language',
            'clear cache',
            'clearing house',
            'random consortium',
            'dot supervisor training',
            'quest-site',
            'dot-test', 
            'report'
            // Add new modules here as needed
        ];

        // Define basic actions
        $basicActions = ['create', 'view', 'edit', 'delete'];

        // Define special actions for specific modules
        $specialActions = [
            'client profile' => ['view_all', 'create_all', 'edit_all', 'delete_all',],
            // Add more special actions as needed
        ];

        // Get existing permissions to avoid duplicates
        $existingPermissions = Permission::pluck('name')->toArray();

        // Create permissions (only new ones)
        foreach ($modules as $module) {
            // Create basic permissions
            foreach ($basicActions as $action) {
                $permissionName = "$module $action";

                if (!in_array($permissionName, $existingPermissions)) {
                    Permission::firstOrCreate([
                        'name' => $permissionName,
                        'guard_name' => 'web'
                    ]);
                }
            }

            // Create special permissions if defined for this module
            if (isset($specialActions[$module])) {
                foreach ($specialActions[$module] as $action) {
                    $permissionName = "$module $action";

                    if (!in_array($permissionName, $existingPermissions)) {
                        Permission::firstOrCreate([
                            'name' => $permissionName,
                            'guard_name' => 'web'
                        ]);
                    }
                }
            }
        }

        // Create or get roles
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $roleCompany = Role::firstOrCreate(['name' => 'company', 'guard_name' => 'web']);
        $roleEmployee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Assign all permissions to Super Admin
        $roleSuperAdmin->syncPermissions(Permission::all());

        // Assign specific permissions to admin role (example)
        $adminPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', 'blog%')
                ->orWhere('name', 'like', 'contact message%')
                ->orWhere('name', 'like', 'setting%');
        })->get();
        $roleAdmin->syncPermissions($adminPermissions);

        // Assign specific permissions to company role (example)
        $companyPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', 'client profile%')
                ->orWhere('name', 'like', 'result recording%');
        })->get();
        $roleCompany->syncPermissions($companyPermissions);

        // Create demo users only if they don't exist
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super-Admin User',
                'password' => Hash::make('12345678'),
                'type' => 0,
                'status' => 1
            ]
        );
        $superAdminUser->syncRoles([$roleSuperAdmin]);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345678'),
                'type' => 1,
                'status' => 1
            ]
        );
        $adminUser->syncRoles([$roleAdmin]);

        $companyUser = User::firstOrCreate(
            ['email' => 'company@gmail.com'],
            [
                'name' => 'Company User',
                'password' => Hash::make('12345678'),
                'type' => 2,
                'status' => 1
            ]
        );
        $companyUser->syncRoles([$roleCompany]);

        $employeeUser = User::firstOrCreate(
            ['email' => 'employee@gmail.com'],
            [
                'name' => 'Employee User',
                'password' => Hash::make('12345678'),
                'type' => 3,
                'status' => 1
            ]
        );
        $employeeUser->syncRoles([$roleEmployee]);

        // Optional: Clean up orphaned permissions (permissions that don't belong to any module)
        $this->cleanupOrphanedPermissions($modules, $basicActions, $specialActions);
    }

    /**
     * Remove permissions that are no longer in the defined modules
     */
    protected function cleanupOrphanedPermissions(array $modules, array $basicActions, array $specialActions): void
    {
        $validPermissions = [];

        foreach ($modules as $module) {
            // Add basic permissions
            foreach ($basicActions as $action) {
                $validPermissions[] = "$module $action";
            }

            // Add special permissions if defined for this module
            if (isset($specialActions[$module])) {
                foreach ($specialActions[$module] as $action) {
                    $validPermissions[] = "$module $action";
                }
            }
        }

        // Get all permissions that don't match our current structure
        $orphanedPermissions = Permission::whereNotIn('name', $validPermissions)->get();

        foreach ($orphanedPermissions as $permission) {
            // Only delete if not assigned to any role
            if ($permission->roles()->count() === 0) {
                $permission->delete();
            }
        }
    }
}
