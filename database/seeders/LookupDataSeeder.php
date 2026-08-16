<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupDataSeeder extends Seeder
{
    public function run(): void
    {
        $nepal = Country::firstOrCreate(['iso2' => 'NP'], ['name' => 'Nepal', 'phone_code' => '+977']);
        Country::firstOrCreate(['iso2' => 'IN'], ['name' => 'India', 'phone_code' => '+91']);

        foreach ([
            'Koshi Province', 'Madhesh Province', 'Bagmati Province', 'Gandaki Province',
            'Lumbini Province', 'Karnali Province', 'Sudurpashchim Province',
        ] as $name) {
            Province::firstOrCreate(['country_id' => $nepal->id, 'name' => $name]);
        }

        $settings = [
            ['key' => 'site_name', 'value' => 'MVMarket', 'type' => 'string', 'group' => 'general'],
            ['key' => 'default_currency', 'value' => 'NPR', 'type' => 'string', 'group' => 'general'],
            ['key' => 'default_commission_rate', 'value' => '10.00', 'type' => 'string', 'group' => 'commission'],
            ['key' => 'require_product_approval', 'value' => '1', 'type' => 'boolean', 'group' => 'vendor'],
            ['key' => 'require_vendor_approval', 'value' => '1', 'type' => 'boolean', 'group' => 'vendor'],
            ['key' => 'require_review_approval', 'value' => '1', 'type' => 'boolean', 'group' => 'catalog'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting + ['updated_at' => now()]
            );
        }
    }
}
