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

    // php artisan db:seed --class=PermissionSeeder
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();


        // Clear all permission-related data and demo users
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        Permission::truncate();
        Role::truncate();

        // Delete the demo users if they exist
        User::whereIn('email', [
            'superadmin@gmail.com',
            'admin@gmail.com',
            'company@gmail.com',
            'employee@gmail.com'
        ])->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Define the modules
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
            'clear cache'
        ];

        // Define actions
        $actions = ['create', 'view', 'edit', 'delete'];

        // Create permissions dynamically
        $permissions = [];
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissions["$module $action"] = Permission::firstOrCreate(['name' => "$module $action"]);
            }
        }

        // Create roles
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleCompany = Role::firstOrCreate(['name' => 'company']);
        $roleEmployee = Role::firstOrCreate(['name' => 'employee']);

        // Assign all permissions to Super Admin
        $roleSuperAdmin->syncPermissions(array_values($permissions));

        // Assign permissions to Admin
        $adminPermissions = [
            'upload view',
            'upload create',
            'upload edit',
            'upload delete',
            'blog view',
            'blog create',
            'blog edit',
            'blog delete',
            'menu view',
            'menu create',
            'menu edit',
            'menu delete',
            'setting view',
            'setting edit',
            'language view',
            'language edit',
            'clear cache view',
            'clear cache edit'
        ];
        $roleAdmin->syncPermissions(array_intersect_key($permissions, array_flip($adminPermissions)));

        // Assign permissions to Company
        $companyPermissions = [
            'portfolio view',
            'portfolio create',
            'portfolio edit',
            'portfolio delete',
            'team view',
            'team create',
            'team edit',
            'team delete',
            'gallery view',
            'gallery create',
            'gallery edit',
            'gallery delete',
            'career view',
            'career create',
            'career edit',
            'career delete',
            'page view',
            'page create',
            'page edit',
            'page delete'
        ];
        $roleCompany->syncPermissions(array_intersect_key($permissions, array_flip($companyPermissions)));

        // Assign permissions to Employee
        $employeePermissions = [
            'contact message view',
            'contact message create',
            'contact message edit',
            'contact message delete',
            'subscribe view',
            'subscribe create',
            'subscribe edit',
            'subscribe delete'
        ];
        $roleEmployee->syncPermissions(array_intersect_key($permissions, array_flip($employeePermissions)));

        // Create demo users
        $superAdminUser = User::factory()->create([
            'name' => 'Super-Admin User',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('12345678'),
            'type' => 0,
            'status' => 1
        ]);
        $superAdminUser->assignRole($roleSuperAdmin);

        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'type' => 1,
            'status' => 1
        ]);
        $adminUser->assignRole($roleAdmin);

        $companyUser = User::factory()->create([
            'name' => 'Company User',
            'email' => 'company@gmail.com',
            'password' => Hash::make('12345678'),
            'type' => 2,
            'status' => 1
        ]);
        $companyUser->assignRole($roleCompany);

        $employeeUser = User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@gmail.com',
            'password' => Hash::make('12345678'),
            'type' => 3,
            'status' => 1
        ]);
        $employeeUser->assignRole($roleEmployee);
    }
}
