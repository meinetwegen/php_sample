<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature = 'shop:create-admin {email} {--name=Admin} {--password=}';

    protected $description = 'Create an administrator account (does nothing if the email already exists)';

    public function handle(): int
    {
        $email = strtolower($this->argument('email'));

        if (User::where('email', $email)->exists()) {
            $this->info("User {$email} already exists.");

            return self::SUCCESS;
        }

        $password = $this->option('password') ?: Str::random(16);

        $user = new User(['name' => $this->option('name'), 'email' => $email, 'password' => $password]);
        $user->role = 'admin';
        $user->save();

        $this->info("Admin {$email} created".($this->option('password') ? '.' : " with password: {$password}"));

        return self::SUCCESS;
    }
}
