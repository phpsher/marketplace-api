<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GenerateJwtToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:generate {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate JWT token for admin user';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $email    = $this->argument('email')    ?? 'admin@example.com';
        $password = $this->argument('password') ?? 'admin123';

        try {
            DB::beginTransaction();

            // Check if admin role exists, create if not
            $role = Role::firstOrCreate(
                ['role' => 'admin']
            );

            // Create admin user
            $user           = new User();
            $user->email    = $email;
            $user->name     = 'Admin';
            $user->password = Hash::make($password);
            $user->role_id  = $role->id;
            $user->save();

            // Generate new token
            $token = $user->createToken('admin-token')->plainTextToken;

            DB::commit();

            $this->info('Admin user created successfully');
            $this->info("Email: $email");
            $this->info("Password: $password");
            $this->info("Token: $token");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to create admin user: ' . $e->getMessage());
        }
    }
}
