<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    private const MODULES = [
        'vendors', 'products', 'categories', 'brands', 'customers', 'orders', 'payments',
        'commissions', 'settlements', 'shipping', 'coupons', 'promotions', 'reports', 'cms',
        'roles', 'permissions', 'settings', 'audit_logs', 'notifications', 'support',
    ];

    private const ROLES = [
        ['name' => 'Super Administrator', 'slug' => 'super-admin', 'description' => 'Complete system control', 'is_system' => true],
        ['name' => 'Administrator', 'slug' => 'administrator', 'description' => 'Operational management', 'is_system' => true],
        ['name' => 'Vendor', 'slug' => 'vendor', 'description' => 'Manages individual marketplace store', 'is_system' => true],
        ['name' => 'Vendor Staff', 'slug' => 'vendor-staff', 'description' => 'Assists vendor operations', 'is_system' => true],
        ['name' => 'Customer', 'slug' => 'customer', 'description' => 'Browses and purchases products', 'is_system' => true],
        ['name' => 'Delivery Staff', 'slug' => 'delivery-staff', 'description' => 'Manages delivery operations', 'is_system' => true],
        ['name' => 'Accountant', 'slug' => 'accountant', 'description' => 'Manages financial operations', 'is_system' => true],
        ['name' => 'Support Staff', 'slug' => 'support-staff', 'description' => 'Handles customer support tickets', 'is_system' => true],
        ['name' => 'Content Manager', 'slug' => 'content-manager', 'description' => 'Manages CMS and marketing content', 'is_system' => true],
    ];

    public function run(): void
    {
        $roleIds = [];
        foreach (self::ROLES as $role) {
            $roleIds[$role['slug']] = Role::firstOrCreate(['slug' => $role['slug']], $role)->id;
        }

        $permissionIds = [];
        foreach (self::MODULES as $module) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $slug = "{$module}.{$action}";
                $permissionIds[$slug] = Permission::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => ucfirst($action).' '.str_replace('_', ' ', ucfirst($module)), 'module' => $module]
                )->id;
            }
        }

        // Administrator gets everything except role/permission management
        // (reserved for super-admin, which bypasses granular checks anyway).
        $administratorPermissions = collect($permissionIds)
            ->filter(fn ($id, $slug) => ! str_starts_with($slug, 'roles.') && ! str_starts_with($slug, 'permissions.'))
            ->values()
            ->all();

        Role::find($roleIds['administrator'])->permissions()->sync($administratorPermissions);
    }
}
