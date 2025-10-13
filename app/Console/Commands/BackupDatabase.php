<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup-manual';
    protected $description = 'Create manual database backup';

    public function handle()
    {
        $this->info('Starting database backup...');
        
        $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i-s') . ".sql";
        $path = storage_path('app/backups/' . $filename);
        
        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $path
        );
        
        $returnVar = NULL;
        $output = NULL;
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->info("Backup created successfully: {$filename}");
            
            // Optional: Upload to cloud storage
            // Storage::disk('s3')->put("backups/{$filename}", file_get_contents($path));
            
        } else {
            $this->error('Backup failed!');
        }
        
        return $returnVar;
    }
}