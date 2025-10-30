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
    
    // Laporan Routes - DIPINDAH ke group terpisah untuk menghindari konflik
    Route::prefix('laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('admin.laporan.index');
        Route::get('/spp/export/excel/{tahun}', [LaporanController::class, 'exportSppExcel'])->name('admin.laporan.export.spp.excel');
        Route::get('/spp/export/pdf/{tahun}', [LaporanController::class, 'exportSppPdf'])->name('admin.laporan.export.spp.pdf');
        Route::get('/pengeluaran/export/excel/{tahun}', [LaporanController::class, 'exportPengeluaranExcel'])->name('admin.laporan.export.pengeluaran.excel');
        Route::get('/pengeluaran/export/pdf/{tahun}', [LaporanController::class, 'exportPengeluaranPdf'])->name('admin.laporan.export.pengeluaran.pdf');
    });
});

// Murid Routes
Route::middleware(['auth', 'murid'])->prefix('murid')->group(function () {
    Route::get('/dashboard', [MuridController::class, 'dashboard'])->name('murid.dashboard');
    Route::get('/tagihan', [MuridController::class, 'tagihanIndex'])->name('murid.tagihan.index');
    Route::get('/bayar-spp', [MuridController::class, 'showBayarSpp'])->name('murid.bayar.spp.page');
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