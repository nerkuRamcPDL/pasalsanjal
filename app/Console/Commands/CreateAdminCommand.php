<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'mvmarket:create-admin
                            {--name= : Full name}
                            {--email= : Email address}
                            {--password= : Password (prompted securely if omitted)}';

    protected $description = 'Create (or promote) the MVMarket super administrator account';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Full name');
        $email = $this->option('email') ?: $this->ask('Email address');
        $password = $this->option('password') ?: $this->secret('Password (min 10 chars, upper/lower/number/symbol)');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:150'],
                'email' => ['required', 'email', 'max:190'],
                'password' => [
                    'required', 'string',
                    'min:'.config('security.password_min_length', 10),
                    'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/',
                ],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if (! $superAdminRole) {
            $this->error('The super-admin role does not exist yet — run `php artisan db:seed` first.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'user_type' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $user->roles()->syncWithoutDetaching([$superAdminRole->id]);

        $this->info("Super admin ready: {$email}");

        return self::SUCCESS;
    }
}
