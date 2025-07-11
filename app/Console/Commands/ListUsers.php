<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list {--role= : Filter by role (admin, moderator, user)} {--limit=20 : Number of users to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List users with their roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $role = $this->option('role');
        $limit = (int) $this->option('limit');

        $query = User::orderBy('created_at', 'desc');

        if ($role) {
            if (!in_array($role, ['admin', 'moderator', 'user'])) {
                $this->error('Invalid role. Must be one of: admin, moderator, user');
                return 1;
            }
            $query->where('role', $role);
        }

        $users = $query->limit($limit)->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');
            return 0;
        }

        $headers = ['ID', 'Name', 'Email', 'Role', 'Created'];
        $rows = $users->map(function ($user) {
            return [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();

        $this->table($headers, $rows);

        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $moderatorCount = User::where('role', 'moderator')->count();
        $userCount = User::where('role', 'user')->count();

        $this->info("Total Users: {$totalUsers} | Admins: {$adminCount} | Moderators: {$moderatorCount} | Users: {$userCount}");

        return 0;
    }
}
