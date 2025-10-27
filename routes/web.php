<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MuridController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Admin Routes - Group yang benar
    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Murid Management
    Route::get('/murid', [AdminController::class, 'muridIndex'])->name('admin.murid.index');
    Route::get('/murid/create', [AdminController::class, 'muridCreate'])->name('admin.murid.create');
    Route::post('/murid', [AdminController::class, 'muridStore'])->name('admin.murid.store');
    Route::get('/murid/{id}/edit', [AdminController::class, 'muridEdit'])->name('admin.murid.edit');
    Route::put('/murid/{id}', [AdminController::class, 'muridUpdate'])->name('admin.murid.update');
    Route::post('/murid/{id}/toggle', [AdminController::class, 'muridToggle'])->name('admin.murid.toggle');
    Route::post('/murid/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.murid.reset-password');
    
    // Tagihan Management
    Route::get('/tagihan', [AdminController::class, 'tagihanIndex'])->name('admin.tagihan.index');
    Route::get('/tagihan/create', [AdminController::class, 'tagihanCreate'])->name('admin.tagihan.create');
    Route::post('/tagihan', [AdminController::class, 'tagihanStore'])->name('admin.tagihan.store');
    Route::delete('/tagihan/{tagihan}', [AdminController::class, 'tagihanDestroy'])->name('admin.tagihan.destroy');
    
    // Pengeluaran Management
    Route::get('/pengeluaran', [AdminController::class, 'pengeluaranIndex'])->name('admin.pengeluaran.index');
    Route::get('/pengeluaran/create', [AdminController::class, 'pengeluaranCreate'])->name('admin.pengeluaran.create');
    Route::post('/pengeluaran', [AdminController::class, 'pengeluaranStore'])->name('admin.pengeluaran.store');
    Route::get('/pengeluaran/{id}/edit', [AdminController::class, 'pengeluaranEdit'])->name('admin.pengeluaran.edit');
    Route::put('/pengeluaran/{id}', [AdminController::class, 'pengeluaranUpdate'])->name('admin.pengeluaran.update');
    Route::delete('/pengeluaran/{id}', [AdminController::class, 'pengeluaranDestroy'])->name('admin.pengeluaran.destroy');
    
    // Pembayaran Management
    Route::get('/pembayaran', [AdminController::class, 'pembayaranIndex'])->name('admin.pembayaran.index');
    Route::get('/pembayaran/history', [AdminController::class, 'pembayaranHistory'])->name('admin.pembayaran.history');
    Route::get('/pembayaran/{id}', [AdminController::class, 'showPembayaran'])->name('admin.pembayaran.show');
    Route::post('/pembayaran/{id}/approve', [AdminController::class, 'approvePembayaran'])->name('admin.pembayaran.approve');
    Route::post('/pembayaran/{id}/reject', [AdminController::class, 'rejectPembayaran'])->name('admin.pembayaran.reject');
    
    // Pembayaran Manual Tagihan
   // Pembayaran Manual
    Route::get('/admin/pembayaran/manual/create', [AdminController::class, 'pembayaranManualCreate'])->name('admin.pembayaran.manual.create');
    Route::post('/admin/pembayaran/manual/store', [AdminController::class, 'pembayaranManualStore'])->name('admin.pembayaran.manual.store');
    
    // SPP Setting
    Route::get('/spp-setting', [AdminController::class, 'sppSetting'])->name('admin.spp-setting');
    Route::post('/spp-setting', [AdminController::class, 'updateSppSetting'])->name('admin.spp-setting.update');
    
    // Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    
    // Kuitansi & Laporan PDF
    Route::get('/kuitansi/{pembayaranId}', [AdminController::class, 'generateKuitansi'])->name('admin.kuitansi.pdf');
    Route::get('/laporan-keuangan-pdf', [AdminController::class, 'laporanKeuanganPdf'])->name('admin.laporan.keuangan.pdf');
    Route::get('/rekap-spp/{muridId}', [AdminController::class, 'rekapSppMurid'])->name('admin.rekap.spp.murid');
    
    // Laporan & Export
    Route::get('/laporan', [AdminController::class, 'laporanIndex'])->name('admin.laporan.index');
    Route::get('/export/tagihan', [AdminController::class, 'exportTagihan'])->name('admin.export.tagihan');
    Route::get('/export/pembayaran', [AdminController::class, 'exportPembayaran'])->name('admin.export.pembayaran');
    Route::get('/export/murid', [AdminController::class, 'exportMurid'])->name('admin.export.murid');
    
    // Backup Database
    Route::get('/backup', [AdminController::class, 'backupIndex'])->name('admin.backup.index');
    Route::post('/backup/create', [AdminController::class, 'createBackup'])->name('admin.backup.create');
    Route::get('/backup/download/{file}', [AdminController::class, 'downloadBackup'])->name('admin.backup.download');
    Route::delete('/backup/delete/{file}', [AdminController::class, 'deleteBackup'])->name('admin.backup.delete');
});
});

// Murid Routes

// Routes Murid
Route::middleware(['auth', 'murid'])->prefix('murid')->group(function () {
    Route::get('/dashboard', [MuridController::class, 'dashboard'])->name('murid.dashboard');
    Route::get('/tagihan', [MuridController::class, 'tagihanIndex'])->name('murid.tagihan.index');
    Route::get('/bayar-spp', [MuridController::class, 'showBayarSpp'])->name('murid.bayar.spp.page');
    Route::post('/bayar-spp', [MuridController::class, 'bayarSpp'])->name('murid.bayar.spp');
    Route::post('/upload-bukti/{id}', [MuridController::class, 'uploadBukti'])->name('murid.upload.bukti');
    Route::get('/pembayaran/history', [MuridController::class, 'pembayaranHistory'])->name('murid.pembayaran.history');
    Route::get('/profile', [MuridController::class, 'profile'])->name('murid.profile');
    Route::post('/profile', [MuridController::class, 'updateProfile'])->name('murid.profile.update');
    // Routes untuk Murid
    Route::get('/murid/kuitansi/{pembayaranId}', [MuridController::class, 'generateKuitansi'])->name('murid.kuitansi.pdf');
    Route::get('/murid/rekap-spp', [MuridController::class, 'rekapSppSaya'])->name('murid.rekap.spp');

    Route::post('/murid/pembayaran/{id}/upload-ulang', [MuridController::class, 'uploadUlangTagihan'])->name('murid.pembayaran.upload-ulang');
    Route::post('/murid/spp/{id}/upload-ulang', [MuridController::class, 'uploadUlangSpp'])->name('murid.spp.upload-ulang');
    
});

// Laporan & Export
Route::post('/admin/export/tagihan', [AdminController::class, 'exportTagihan'])->name('admin.export.tagihan');
Route::post('/admin/export/pembayaran', [AdminController::class, 'exportPembayaran'])->name('admin.export.pembayaran');
Route::post('/admin/export/murid', [AdminController::class, 'exportMurid'])->name('admin.export.murid');
Route::get('/admin/laporan', [AdminController::class, 'laporanIndex'])->name('admin.laporan.index');

// Backup
Route::get('/admin/backup', [AdminController::class, 'backupIndex'])->name('admin.backup.index');
Route::post('/admin/backup', [AdminController::class, 'createBackup'])->name('admin.backup.create');
Route::get('/admin/backup/download/{file}', [AdminController::class, 'downloadBackup'])->name('admin.backup.download');
Route::delete('/admin/backup/{file}', [AdminController::class, 'deleteBackup'])->name('admin.backup.delete');