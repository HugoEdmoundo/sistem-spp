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

    // Admin Routes - tambah pengeluaran
    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
        
        // Pengeluaran Management
        Route::get('/pengeluaran', [AdminController::class, 'pengeluaranIndex'])->name('admin.pengeluaran.index');
        Route::get('/pengeluaran/create', [AdminController::class, 'pengeluaranCreate'])->name('admin.pengeluaran.create');
        Route::post('/pengeluaran', [AdminController::class, 'pengeluaranStore'])->name('admin.pengeluaran.store');
        Route::get('/pengeluaran/{id}/edit', [AdminController::class, 'pengeluaranEdit'])->name('admin.pengeluaran.edit');
        Route::put('/pengeluaran/{id}', [AdminController::class, 'pengeluaranUpdate'])->name('admin.pengeluaran.update');
        Route::delete('/pengeluaran/{id}', [AdminController::class, 'pengeluaranDestroy'])->name('admin.pengeluaran.destroy');
    });

    
    // Murid Management
    Route::get('/murid', [AdminController::class, 'muridIndex'])->name('admin.murid.index');
    Route::get('/murid/create', [AdminController::class, 'muridCreate'])->name('admin.murid.create');
    Route::post('/murid', [AdminController::class, 'muridStore'])->name('admin.murid.store');
    Route::get('/murid/{id}/edit', [AdminController::class, 'muridEdit'])->name('admin.murid.edit');
    Route::put('/murid/{id}', [AdminController::class, 'muridUpdate'])->name('admin.murid.update');
    Route::post('/murid/{id}/toggle', [AdminController::class, 'muridToggle'])->name('admin.murid.toggle');
    Route::post('/murid/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.murid.reset-password');
    
    // Tagihan Management
    Route::get('/tagihan', [AdminController::class, 'tagihanIndex'])->name('tagihan.index');
    Route::get('/tagihan/create', [AdminController::class, 'tagihanCreate'])->name('tagihan.create');
    Route::post('/tagihan', [AdminController::class, 'tagihanStore'])->name('tagihan.store');
    Route::get('/tagihan/{tagihan}/edit', [AdminController::class, 'tagihanEdit'])->name('tagihan.edit');
    Route::put('/tagihan/{tagihan}', [AdminController::class, 'tagihanUpdate'])->name('tagihan.update');
    Route::delete('/tagihan/{tagihan}', [AdminController::class, 'tagihanDestroy'])->name('tagihan.destroy');
    
    // Pembayaran Management
    Route::get('/pembayaran', [AdminController::class, 'pembayaranIndex'])->name('admin.pembayaran.index');
    Route::get('/pembayaran/history', [AdminController::class, 'pembayaranHistory'])->name('admin.pembayaran.history');
    Route::get('/pembayaran/{id}', [AdminController::class, 'showPembayaran'])->name('admin.pembayaran.show');
    Route::post('/pembayaran/{id}/approve', [AdminController::class, 'approvePembayaran'])->name('admin.pembayaran.approve');
    Route::post('/pembayaran/{id}/reject', [AdminController::class, 'rejectPembayaran'])->name('admin.pembayaran.reject');
    
    // SPP Setting
    Route::get('/spp-setting', [AdminController::class, 'sppSetting'])->name('admin.spp-setting');
    Route::post('/spp-setting', [AdminController::class, 'updateSppSetting'])->name('admin.spp-setting.update');
    
    // Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');

    // Pembayaran Manual
    Route::get('/admin/pembayaran/manual/create', [AdminController::class, 'pembayaranManualCreate'])->name('admin.pembayaran.manual.create');
    Route::post('/admin/pembayaran/manual/store', [AdminController::class, 'pembayaranManualStore'])->name('admin.pembayaran.manual.store');
    
    // Laporan & Export
    Route::get('/laporan', [AdminController::class, 'laporanIndex'])->name('admin.laporan.index');
    Route::post('/export/tagihan', [AdminController::class, 'exportTagihan'])->name('admin.export.tagihan');
    Route::post('/export/pembayaran', [AdminController::class, 'exportPembayaran'])->name('admin.export.pembayaran');
    Route::post('/export/murid', [AdminController::class, 'exportMurid'])->name('admin.export.murid');
    
    // Backup
    Route::get('/backup', [AdminController::class, 'backupIndex'])->name('admin.backup.index');
    Route::post('/backup', [AdminController::class, 'createBackup'])->name('admin.backup.create');
    Route::get('/backup/download/{file}', [AdminController::class, 'downloadBackup'])->name('admin.backup.download');
    Route::delete('/backup/{file}', [AdminController::class, 'deleteBackup'])->name('admin.backup.delete');
});

// Murid Routes
Route::middleware(['auth', 'murid'])->prefix('murid')->group(function () {
    Route::get('/dashboard', [MuridController::class, 'dashboard'])->name('murid.dashboard');
    Route::get('/tagihan', [MuridController::class, 'tagihanIndex'])->name('murid.tagihan.index');
    Route::post('/upload-bukti/{id}', [MuridController::class, 'uploadBukti'])->name('murid.upload.bukti');
    Route::get('/pembayaran/history', [MuridController::class, 'pembayaranHistory'])->name('murid.pembayaran.history');
    Route::get('/profile', [MuridController::class, 'profile'])->name('murid.profile');
    Route::post('/profile', [MuridController::class, 'updateProfile'])->name('murid.profile.update');

    // Murid Routes - tambah bayar SPP fleksibel
    Route::middleware(['auth', 'murid'])->prefix('murid')->group(function () {
        // ... routes lainnya ...
        Route::post('/bayar-spp', [MuridController::class, 'bayarSpp'])->name('murid.bayar.spp');
    });
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