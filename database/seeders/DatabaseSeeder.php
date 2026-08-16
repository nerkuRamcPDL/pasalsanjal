<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            LookupDataSeeder::class,
        ]);

        // Super admin creation is interactive: php artisan mvmarket:create-admin
        // (kept out of the seeder so a re-run of `migrate:fresh --seed`
        // never silently creates a second admin with a throwaway password).
    }
}
