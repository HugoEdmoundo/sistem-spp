<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Murid Management
    Route::get('/murid', [AdminController::class, 'muridIndex'])->name('admin.murid.index');
    Route::get('/murid/create', [AdminController::class, 'muridCreate'])->name('admin.murid.create');
    Route::post('/murid', [AdminController::class, 'muridStore'])->name('admin.murid.store');
    Route::get('/murid/{id}/edit', [AdminController::class, 'muridEdit'])->name('admin.murid.edit');
    Route::put('/murid/{id}', [AdminController::class, 'muridUpdate'])->name('admin.murid.update');
    Route::post('/murid/{id}/toggle', [AdminController::class, 'muridToggle'])->name('admin.murid.toggle');
    Route::post('/murid/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.murid.reset-password');
    Route::get('/murid/{id}/tagihan', [AdminController::class, 'muridTagihanDetail'])->name('admin.murid.tagihan.detail');
    Route::get('/murid/{id}/pembayaran', [AdminController::class, 'muridPembayaran'])->name('admin.murid.pembayaran');
    
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
    
    // Pembayaran Manual
    Route::get('/pembayaran/manual/create', [AdminController::class, 'pembayaranManualCreate'])->name('admin.pembayaran.manual.create');
    Route::post('/pembayaran/manual/store', [AdminController::class, 'pembayaranManualStore'])->name('admin.pembayaran.manual.store');
    
    // SPP Setting
    Route::get('/spp-setting', [AdminController::class, 'sppSetting'])->name('admin.spp-setting');
    Route::post('/spp-setting', [AdminController::class, 'updateSppSetting'])->name('admin.spp-setting.update');
    
    // Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');

    Route::get('/kuitansi/{pembayaranId}', [AdminController::class, 'generateKuitansi'])->name('admin.kuitansi.pdf');
    // Riwayat Pembayaran
    Route::get('/pembayaran/history', [AdminController::class, 'pembayaranHistory'])->name('admin.pembayaran.history');
    Route::get('/admin/kuitansi/{pembayaran}/pdf', [AdminController::class, 'generateKuitansi'])->name('admin.kuitansi.pdf');
    
    // Laporan Routes
    Route::get('/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
    
    // Export SPP
    Route::get('/laporan/export/spp/excel/{tahun}', [LaporanController::class, 'exportSppExcel'])->name('admin.laporan.export.spp.excel');
    Route::get('/laporan/export/spp/pdf/{tahun}', [LaporanController::class, 'exportSppPdf'])->name('admin.laporan.export.spp.pdf');
    
    // Export Tagihan
    Route::get('/laporan/export/tagihan/excel/{tahun}', [LaporanController::class, 'exportTagihanExcel'])->name('admin.laporan.export.tagihan.excel');
    Route::get('/laporan/export/tagihan/pdf/{tahun}', [LaporanController::class, 'exportTagihanPdf'])->name('admin.laporan.export.tagihan.pdf');
    
    // Export Pengeluaran
    Route::get('/laporan/export/pengeluaran/excel/{tahun}', [LaporanController::class, 'exportPengeluaranExcel'])->name('admin.laporan.export.pengeluaran.excel');
    Route::get('/laporan/export/pengeluaran/pdf/{tahun}', [LaporanController::class, 'exportPengeluaranPdf'])->name('admin.laporan.export.pengeluaran.pdf');
    
    // Debug route (opsional)
    Route::get('/laporan/debug/{userId?}/{tahun?}', [LaporanController::class, 'debugData'])->name('admin.laporan.debug');
    Route::get('/admin/laporan/debug-spp/{tahun?}', [LaporanController::class, 'debugSpp'])
    ->name('admin.laporan.debug.spp');

    Route::get('/get-spp-cicilan/{userId}', [AdminController::class, 'getSppCicilan'])->name('admin.get.spp.cicilan');
});

// routes/web.php

// Laporan Routes
Route::prefix('laporan')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('admin.laporan.index');
    
    // Export SPP
    Route::get('/spp/excel/{tahun}', [LaporanController::class, 'exportSppExcel'])->name('admin.laporan.export.spp.excel');
    Route::get('/spp/pdf/{tahun}', [LaporanController::class, 'exportSppPdf'])->name('admin.laporan.export.spp.pdf');
    
    // Export Tagihan
    Route::get('/tagihan/excel/{tahun}', [LaporanController::class, 'exportTagihanExcel'])->name('admin.laporan.export.tagihan.excel');
    Route::get('/tagihan/pdf/{tahun}', [LaporanController::class, 'exportTagihanPdf'])->name('admin.laporan.export.tagihan.pdf');
    
    // Export Pengeluaran
    Route::get('/pengeluaran/excel/{tahun}', [LaporanController::class, 'exportPengeluaranExcel'])->name('admin.laporan.export.pengeluaran.excel');
    Route::get('/pengeluaran/pdf/{tahun}', [LaporanController::class, 'exportPengeluaranPdf'])->name('admin.laporan.export.pengeluaran.pdf');

    Route::get('/admin/check-spp-payment', [AdminController::class, 'checkSppPayment'])->name('admin.check.spp.payment');
});

// Murid Routes
Route::middleware(['auth', 'murid'])->prefix('murid')->group(function () {
    Route::get('/dashboard', [MuridController::class, 'dashboard'])->name('murid.dashboard');
    Route::get('/tagihan', [MuridController::class, 'tagihanIndex'])->name('murid.tagihan.index');
    Route::post('/tagihan/upload-bukti/{tagihan}', [MuridController::class, 'uploadBukti'])->name('murid.tagihan.upload-bukti');
    Route::get('/bayar-spp', [MuridController::class, 'showBayarSpp'])->name('murid.bayar.spp.page');
    Route::post('/pembayaran/upload/{tagihan}', [MuridController::class, 'uploadBukti'])->name('murid.pembayaran.upload');
    Route::post('/bayar-spp', [MuridController::class, 'bayarSpp'])->name('murid.bayar.spp');
    Route::post('/upload-bukti/{id}', [MuridController::class, 'uploadBukti'])->name('murid.upload.bukti');
    Route::get('/pembayaran/history', [MuridController::class, 'pembayaranHistory'])->name('murid.pembayaran.history');
    Route::get('/profile', [MuridController::class, 'profile'])->name('murid.profile');
    Route::post('/profile', [MuridController::class, 'updateProfile'])->name('murid.profile.update');
    
    // Fixed routes - hapus prefix murid yang berulang
    Route::get('/kuitansi/{pembayaranId}', [MuridController::class, 'generateKuitansi'])->name('murid.kuitansi.pdf');
    Route::get('/rekap-spp', [MuridController::class, 'rekapSppSaya'])->name('murid.rekap.spp');
    Route::post('/pembayaran/{id}/upload-ulang', [MuridController::class, 'uploadUlangTagihan'])->name('murid.pembayaran.upload-ulang');
    Route::post('/spp/{id}/upload-ulang', [MuridController::class, 'uploadUlangSpp'])->name('murid.spp.upload-ulang');

    // Route untuk upload bukti tagihan biasa
    Route::post('/tagihan/upload-bukti/{id}', [MuridController::class, 'uploadBukti'])
        ->name('murid.tagihan.upload-bukti');

    // Route untuk upload bukti SPP  
    Route::post('/spp/upload-bukti', [MuridController::class, 'uploadBuktiSpp'])
        ->name('murid.spp.upload-bukti');

    Route::get('/laporan', [MuridController::class, 'laporanIndex'])->name('murid.laporan.index');
    Route::get('/laporan/export/{tahun}', [MuridController::class, 'exportLaporan'])->name('murid.laporan.export');
    
});

// Validation Route - PERBAIKI: tambahkan use statement untuk Request
Route::get('/validate-spp', function (Illuminate\Http\Request $request) {
    $user = auth()->user();
    $tahun = $request->tahun;
    $bulanMulai = $request->bulan_mulai;
    $bulanAkhir = $request->bulan_akhir;

    $validasi = $user->bisaBayarSpp($tahun, $bulanMulai, $bulanAkhir);
    
    $bulanNames = array_map(function($bulan) {
        return \App\Models\User::getNamaBulanStatic($bulan);
    }, $validasi['sudah_dibayar']);

    return response()->json([
        'bisa_dibayar' => $validasi['bisa_dibayar'],
        'sudah_dibayar' => $validasi['sudah_dibayar'],
        'sudah_dibayar_names' => implode(', ', $bulanNames),
        'bisa_proses' => $validasi['bisa_proses']
    ]);
})->middleware('auth');