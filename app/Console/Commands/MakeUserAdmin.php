<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {identifier} {--role=admin : The role to assign (admin, moderator, user)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user admin by ID or email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $identifier = $this->argument('identifier');
        $role = $this->option('role');

        // Validate role
        if (!in_array($role, ['admin', 'moderator', 'user'])) {
            $this->error('Invalid role. Must be one of: admin, moderator, user');
            return 1;
        }

        // Find user by ID or email
        $user = null;
        
        if (is_numeric($identifier)) {
            $user = User::find($identifier);
        } else {
            $user = User::where('email', $identifier)->first();
        }

        if (!$user) {
            $this->error("User not found with identifier: {$identifier}");
            return 1;
        }

        // Update user role
        $oldRole = $user->role;
        $user->role = $role;
        $user->save();

        $this->info("User '{$user->name}' ({$user->email}) role changed from '{$oldRole}' to '{$role}'");
        
        // Show user details
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $user->id],
                ['Name', $user->name],
                ['Email', $user->email],
                ['Role', $user->role],
                ['Created', $user->created_at->format('Y-m-d H:i:s')],
            ]
        );

        return 0;
    }
}
