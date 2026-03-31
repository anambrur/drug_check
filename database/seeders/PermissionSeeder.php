<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run: php artisan db:seed --class=PermissionSeeder
     *
     * PERMISSION NAMING CONVENTION
     * ----------------------------
     * Format:  "{module} {action}"
     * Example: "menu create", "blog delete", "client profile view_all"
     *
     * This MUST match the permission strings used in web.php middleware calls.
     * e.g.  ->middleware('permission:menu create')
     *       ->middleware('permission:client profile view|client profile view_all')
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ----------------------------------------------------------------
        // 1. Define all modules
        //
        //    Each module name here must EXACTLY match the prefix used in
        //    web.php permission middleware strings.
        //    e.g. "dot supervisor training" → 'permission:dot supervisor training create'
        // ----------------------------------------------------------------
        $modules = [
            // CMS / Site builder
            'upload',
            'page builder',
            'menu',
            'section',
            'service',
            'background',
            'portfolio',
            'team',
            'gallery',
            'career',
            'page',
            'blog',
            'plan',           // ← was missing in original seeder
            'setting',

            // Business features
            'client profile',
            'result recording',
            'random selection',
            'lab admin',
            'clearing house',
            'random consortium',
            'dot supervisor training',  // ← was 'dot supervisor training' in routes
            'quest-site',
            'quest-order',
            'dot-test',

            // Utility
            'contact message',
            'subscribe',
            'language',
            'clear cache',
            'report',
        ];

        // ----------------------------------------------------------------
        // 2. Standard CRUD actions applied to every module
        // ----------------------------------------------------------------
        $basicActions = ['view', 'create', 'edit', 'delete'];

        // ----------------------------------------------------------------
        // 3. Extra actions for specific modules
        //    (Used for granular ownership scoping, etc.)
        // ----------------------------------------------------------------
        $specialActions = [
            'client profile' => [
                'view_all',
                'create_all',
                'edit_all',
                'delete_all',
            ],
        ];

        // ----------------------------------------------------------------
        // 4. Create permissions (idempotent — safe to run multiple times)
        // ----------------------------------------------------------------
        $existingPermissions = Permission::pluck('name')->flip(); // flip for O(1) lookup

        foreach ($modules as $module) {
            foreach ($basicActions as $action) {
                $name = "$module $action";
                if (! isset($existingPermissions[$name])) {
                    Permission::create(['name' => $name, 'guard_name' => 'web']);
                }
            }

            if (isset($specialActions[$module])) {
                foreach ($specialActions[$module] as $action) {
                    $name = "$module $action";
                    if (! isset($existingPermissions[$name])) {
                        Permission::create(['name' => $name, 'guard_name' => 'web']);
                    }
                }
            }
        }

        // ----------------------------------------------------------------
        // 5. Roles
        // ----------------------------------------------------------------
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $roleAdmin      = Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        $roleCompany    = Role::firstOrCreate(['name' => 'company',     'guard_name' => 'web']);
        $roleEmployee   = Role::firstOrCreate(['name' => 'employee',    'guard_name' => 'web']);

        // Super Admin gets everything
        $roleSuperAdmin->syncPermissions(Permission::all());

        // Admin: CMS management
        $adminPermissions = Permission::where(function ($q) {
            $q->where('name', 'like', 'blog%')
                ->orWhere('name', 'like', 'section%')
                ->orWhere('name', 'like', 'menu%')
                ->orWhere('name', 'like', 'page%')
                ->orWhere('name', 'like', 'setting%')
                ->orWhere('name', 'like', 'contact message%')
                ->orWhere('name', 'like', 'subscribe%');
        })->get();
        $roleAdmin->syncPermissions($adminPermissions);

        // Company: own client profile + results
        $companyPermissions = Permission::where(function ($q) {
            $q->where('name', 'like', 'client profile%')
                ->orWhere('name', 'like', 'result recording%');
        })->get();
        $roleCompany->syncPermissions($companyPermissions);

        // Employee: read-only result access
        $employeePermissions = Permission::whereIn('name', [
            'result recording view',
        ])->get();
        $roleEmployee->syncPermissions($employeePermissions);

        // ----------------------------------------------------------------
        // 6. Demo / seed users
        // ----------------------------------------------------------------
        $this->seedUser('superadmin@gmail.com', 'Super-Admin User', 0, $roleSuperAdmin);
        $this->seedUser('admin@gmail.com',      'Admin User',       1, $roleAdmin);
        $this->seedUser('company@gmail.com',    'Company User',     2, $roleCompany);
        $this->seedUser('employee@gmail.com',   'Employee User',    3, $roleEmployee);

        // ----------------------------------------------------------------
        // 7. Optional: remove orphaned permissions no longer in the list
        // ----------------------------------------------------------------
        $this->cleanupOrphanedPermissions($modules, $basicActions, $specialActions);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    protected function seedUser(string $email, string $name, int $type, Role $role): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => $name,
                'password' => Hash::make('12345678'),
                'type'     => $type,
                'status'   => 1,
            ]
        );
        $user->syncRoles([$role]);
    }

    protected function cleanupOrphanedPermissions(
        array $modules,
        array $basicActions,
        array $specialActions
    ): void {
        $valid = [];

        foreach ($modules as $module) {
            foreach ($basicActions as $action) {
                $valid[] = "$module $action";
            }
            foreach ($specialActions[$module] ?? [] as $action) {
                $valid[] = "$module $action";
            }
        }

        // Only delete permissions not assigned to any role
        Permission::whereNotIn('name', $valid)
            ->get()
            ->each(function (Permission $p) {
                if ($p->roles()->count() === 0) {
                    $p->delete();
                }
            });
    }
}
