<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerateSppTagihan::class,
        \App\Console\Commands\BackupDatabase::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Generate tagihan SPP setiap tanggal 1
        $schedule->command('spp:generate')->monthlyOn(1, '00:00');
        
        // Backup database setiap minggu
        $schedule->command('backup:run')->weekly()->at('02:00');
        
        // Clean old backups (keep only 30 days)
        $schedule->command('backup:clean')->daily()->at('01:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}