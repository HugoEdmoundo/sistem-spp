<?php
// app/Console/Commands/GenerateSppTagihan.php
namespace App\Console\Commands;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\SppSetting;
use Illuminate\Console\Command;

class GenerateSppTagihan extends Command
{
    protected $signature = 'spp:generate';
    protected $description = 'Generate tagihan SPP otomatis setiap tanggal 1';

    public function handle()
    {
        // Cek apakah hari ini tanggal 1
        if (now()->day != 1) {
            $this->info('Bukan tanggal 1, skip generate tagihan.');
            return;
        }

        $sppSetting = SppSetting::latest()->first();
        if (!$sppSetting) {
            $this->error('Setting SPP belum ada!');
            return;
        }

        $muridAktif = User::where('role', 'murid')->where('aktif', true)->get();
        $bulan = now()->month;
        $tahun = now()->year;

        $count = 0;
        foreach ($muridAktif as $murid) {
            // Cek apakah sudah ada tagihan SPP bulan ini
            $existingTagihan = Tagihan::where('user_id', $murid->id)
                ->where('jenis', 'spp')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            if (!$existingTagihan) {
                Tagihan::create([
                    'user_id' => $murid->id,
                    'jenis' => 'spp',
                    'keterangan' => 'SPP Bulan ' . now()->format('F Y'),
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'jumlah' => $sppSetting->nominal,
                    'status' => 'unpaid'
                ]);
                $count++;
            }
        }

        $this->info("Berhasil generate $count tagihan SPP untuk bulan $bulan $tahun");
    }
}