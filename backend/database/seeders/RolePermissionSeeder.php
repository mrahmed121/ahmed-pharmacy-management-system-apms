<?php
namespace Database\Seeders;
use App\Domains\Shared\Models\Permission;
use App\Domains\Shared\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RolePermissionSeeder extends Seeder
{
    public const PERMISSIONS = [
        ['slug' => 'dashboard.view', 'name' => 'View dashboard'],
        ['slug' => 'pos.use', 'name' => 'Use POS terminal'],
        ['slug' => 'inventory.view', 'name' => 'View inventory'],
        ['slug' => 'inventory.manage', 'name' => 'Manage inventory'],
        ['slug' => 'purchases.view', 'name' => 'View purchases'],
        ['slug' => 'purchases.manage', 'name' => 'Manage purchases'],
        ['slug' => 'returns.view', 'name' => 'View returns'],
        ['slug' => 'returns.manage', 'name' => 'Manage returns'],
        ['slug' => 'returns.approve', 'name' => 'Approve returns'],
        ['slug' => 'reports.view', 'name' => 'View reports'],
        ['slug' => 'shifts.manage', 'name' => 'Manage shifts'],
        ['slug' => 'users.view', 'name' => 'View users'],
        ['slug' => 'users.manage', 'name' => 'Manage users'],
        ['slug' => 'settings.view', 'name' => 'View settings'],
        ['slug' => 'settings.manage', 'name' => 'Manage settings'],
        ['slug' => 'audit.view', 'name' => 'View audit logs'],
    ];
    public const ROLES = [
        'super-admin' => ['name' => 'Super Admin', 'permissions' => '*'],
        'owner' => ['name' => 'Owner', 'permissions' => ['dashboard.view','pos.use','inventory.view','inventory.manage','purchases.view','purchases.manage','returns.view','returns.manage','returns.approve','reports.view','shifts.manage','users.view','users.manage','settings.view','settings.manage','audit.view']],
        'manager' => ['name' => 'Branch Manager', 'permissions' => ['dashboard.view','pos.use','inventory.view','inventory.manage','purchases.view','purchases.manage','returns.view','returns.manage','returns.approve','reports.view','shifts.manage','audit.view']],
        'pharmacist' => ['name' => 'Pharmacist', 'permissions' => ['dashboard.view','pos.use','inventory.view','returns.view']],
        'cashier' => ['name' => 'Cashier', 'permissions' => ['dashboard.view','pos.use']],
        'inventory-manager' => ['name' => 'Inventory Manager', 'permissions' => ['dashboard.view','inventory.view','inventory.manage','purchases.view','purchases.manage']],
        'accountant' => ['name' => 'Accountant', 'permissions' => ['dashboard.view','reports.view','purchases.view','audit.view']],
        'auditor' => ['name' => 'Auditor', 'permissions' => ['dashboard.view','inventory.view','purchases.view','returns.view','reports.view','audit.view']],
    ];
    public function run(): void
    {
        DB::transaction(function () {
            foreach (self::PERMISSIONS as $p) Permission::firstOrCreate(['slug' => $p['slug']], ['name' => $p['name']]);
            $all = Permission::pluck('slug')->all();
            foreach (self::ROLES as $slug => $def) {
                $role = Role::firstOrCreate(['slug' => $slug], ['name' => $def['name']]);
                $slugs = $def['permissions'] === '*' ? $all : $def['permissions'];
                $role->permissions()->sync(Permission::whereIn('slug', $slugs)->pluck('id'));
            }
        });
    }
}
