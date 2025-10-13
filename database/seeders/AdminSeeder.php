<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\SppSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Hapus user admin jika sudah ada (untuk avoid duplicate)
        User::where('email', 'admin@spp.app')->delete();

        // Create Admin
        User::create([
            'role' => 'admin',
            'nama' => 'Administrator',
            'email' => 'admin@spp.app',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Setting SPP default
        SppSetting::create([
            'nominal' => 500000,
            'berlaku_mulai' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info('Admin user created:');
        $this->command->info('Username: admin');
        $this->command->info('Password: admin123');
    }
}