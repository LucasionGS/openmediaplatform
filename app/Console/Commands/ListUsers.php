<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Video;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list {--role= : Filter by role (admin, moderator, user)} {--limit=20 : Number of users to show} {--with-storage : Show storage usage} {--sort=ID : Sort by column (ID, Name, Email, Role, Created, Storage Used)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List users with their roles and optionally their storage usage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $role = $this->option('role');
        $limit = (int) $this->option('limit');
        $withStorage = $this->option('with-storage');
        $sortBy = $this->option('sort');

        // Validate sort option
        $validSortColumns = ['ID', 'Name', 'Email', 'Role', 'Created'];
        if ($withStorage) {
            $validSortColumns[] = 'Storage Used';
        }

        if (!in_array($sortBy, $validSortColumns)) {
            $this->error('Invalid sort column. Valid options: ' . implode(', ', $validSortColumns));
            return 1;
        }

        $query = User::orderBy('created_at', 'desc');

        if ($role) {
            if (!in_array($role, ['admin', 'moderator', 'user'])) {
                $this->error('Invalid role. Must be one of: admin, moderator, user');
                return 1;
            }
            $query->where('role', $role);
        }

        // Apply database-level sorting for non-storage columns
        if ($sortBy !== 'Storage Used') {
            $dbColumn = $this->mapSortColumnToDatabase($sortBy);
            $query = User::orderBy($dbColumn, 'asc');
            
            // Apply role filter after reordering query
            if ($role) {
                $query->where('role', $role);
            }
        }

        $users = $query->limit($limit)->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');
            return 0;
        }

        $headers = ['ID', 'Name', 'Email', 'Role', 'Created'];
        if ($withStorage) {
            $headers[] = 'Storage Used';
        }

        $rows = $users->map(function ($user) use ($withStorage) {
            $row = [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->created_at->format('Y-m-d H:i:s'),
            ];

            if ($withStorage) {
                $storageUsed = $this->calculateUserStorageUsage($user);
                $row[] = $this->formatBytes($storageUsed);
                // Store raw bytes for sorting
                $row['_storage_bytes'] = $storageUsed;
            }

            return $row;
        })->toArray();

        // Apply sorting for storage column (requires post-processing)
        if ($sortBy === 'Storage Used' && $withStorage) {
            usort($rows, function ($a, $b) {
                return $a['_storage_bytes'] <=> $b['_storage_bytes'];
            });
            
            // Remove the temporary storage bytes column
            $rows = array_map(function ($row) {
                unset($row['_storage_bytes']);
                return array_values($row);
            }, $rows);
        } else {
            // For non-storage sorting, ensure rows are properly indexed arrays
            $rows = array_map('array_values', $rows);
        }

        $this->table($headers, $rows);

        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $moderatorCount = User::where('role', 'moderator')->count();
        $userCount = User::where('role', 'user')->count();

        $this->info("Total Users: {$totalUsers} | Admins: {$adminCount} | Moderators: {$moderatorCount} | Users: {$userCount}");

        return 0;
    }

    /**
     * Calculate the total storage usage for a user's videos and thumbnails
     */
    private function calculateUserStorageUsage(User $user): int
    {
        $totalSize = 0;
        
        // Get all videos for this user
        $videos = Video::where('user_id', $user->id)->get();
        
        foreach ($videos as $video) {
            // Calculate video file size
            $videoPath = storage_path('app/public/videos/' . $video->vid);
            if (file_exists($videoPath)) {
                $totalSize += filesize($videoPath);
            }
            
            // Calculate thumbnail file size
            $thumbnailPath = storage_path('app/public/thumbnails/' . $video->vid . '.jpg');
            if (file_exists($thumbnailPath)) {
                $totalSize += filesize($thumbnailPath);
            }
        }
        
        return $totalSize;
    }

    /**
     * Format bytes into human readable format
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes == 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor(log($bytes, 1024));
        
        if ($factor >= count($units)) {
            $factor = count($units) - 1;
        }
        
        $size = round($bytes / pow(1024, $factor), 2);
        
        return $size . ' ' . $units[$factor];
    }

    /**
     * Map display column names to database column names
     */
    private function mapSortColumnToDatabase(string $sortColumn): string
    {
        $mapping = [
            'ID' => 'id',
            'Name' => 'name',
            'Email' => 'email',
            'Role' => 'role',
            'Created' => 'created_at'
        ];

        return $mapping[$sortColumn] ?? 'id';
    }
}
