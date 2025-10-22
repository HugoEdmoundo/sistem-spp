<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\SppSetting;
use App\Models\Pengeluaran;
use App\Exports\TagihanExport;
use App\Exports\PembayaranExport;
use App\Exports\MuridExport;
use App\Models\Notification;
use App\Events\PembayaranDibuat;
use App\Events\StatusPembayaranDiupdate;
use App\Mail\PembayaranNotification;
use App\Mail\StatusPembayaranNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;


class AdminController extends Controller
{
    public function dashboard()
    {
        $totalMurid = User::where('role', 'murid')->where('aktif', true)->count();
        $totalTagihanBulanIni = Tagihan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah');
        
        // TOTAL AKHIR = Total Pembayaran Diterima - Total Pengeluaran
        $totalPembayaran = Pembayaran::where('status', 'accepted')->sum('jumlah');
        $totalPengeluaran = Pengeluaran::sum('jumlah');
        $totalAkhir = $totalPembayaran - $totalPengeluaran;
        
        $pembayaranPending = Pembayaran::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'totalMurid', 
            'totalTagihanBulanIni', 
            'totalAkhir',
            'totalPembayaran',
            'totalPengeluaran',
            'pembayaranPending'
        ));
    }

    // ==================== MURID MANAGEMENT ====================
    public function muridIndex()
    {
        $murid = User::where('role', 'murid')->get();
        return view('admin.murid.index', compact('murid'));
    }

    public function muridCreate()
    {
        return view('admin.murid.create');
    }

    public function muridStore(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'username' => 'required|unique:users',
            'nip' => 'nullable'
        ]);

        User::create([
            'role' => 'murid',
            'nama' => $request->nama,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make('123456789'),
            'nip' => $request->nip,
            'aktif' => true
        ]);

        return redirect()->route('admin.murid.index')->with('success', 'Murid berhasil ditambahkan.');
    }

    public function muridEdit($id)
    {
        $murid = User::where('role', 'murid')->findOrFail($id);
        return view('admin.murid.edit', compact('murid'));
    }

    public function muridUpdate(Request $request, $id)
    {
        $murid = User::where('role', 'murid')->findOrFail($id);
        
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email,' . $murid->id,
            'username' => 'required|unique:users,username,' . $murid->id,
            'nip' => 'nullable'
        ]);

        $murid->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'username' => $request->username,
            'nip' => $request->nip
        ]);

        return redirect()->route('admin.murid.index')->with('success', 'Data murid berhasil diperbarui.');
    }

    public function muridToggle($id)
    {
        $murid = User::where('role', 'murid')->findOrFail($id);
        $murid->update(['aktif' => !$murid->aktif]);
        
        $status = $murid->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Murid berhasil $status.");
    }

    public function resetPassword($id)
    {
        $murid = User::findOrFail($id);
        $murid->update(['password' => Hash::make('123456789')]);
        
        return back()->with('success', 'Password berhasil direset ke 123456789');
    }

    // ==================== TAGIHAN MANAGEMENT ====================
    public function tagihanIndex()
    {
        $tagihan = Tagihan::with('user')->latest()->get();
        return view('admin.tagihan.index', compact('tagihan'));
    }
    
    public function tagihanCreate()
    {
        $murid = User::where('role', 'murid')->where('aktif', true)->get();
        return view('admin.tagihan.create', compact('murid'));
    }
    
    public function tagihanStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'jenis' => 'required',
            'keterangan' => 'required',
            'jumlah' => 'required|numeric|min:0'
        ]);

        Tagihan::create([
            'user_id' => $request->user_id,
            'jenis' => $request->jenis,
            'keterangan' => $request->keterangan,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'jumlah' => $request->jumlah,
            'status' => 'unpaid'
        ]);

        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil ditambahkan.');
    }
    
    public function tagihanDestroy(Tagihan $tagihan)
    {
        if ($tagihan->pembayaran) {
            return redirect()->route('admin.tagihan.index')
                ->with('error', 'Tidak dapat menghapus tagihan yang sudah ada pembayaran!');
        }

        $tagihan->delete();
        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil dihapus!');
    }
    
    // ==================== PENGELUARAN MANAGEMENT ====================
    public function pengeluaranIndex()
    {
        $pengeluaran = Pengeluaran::with('admin')->latest()->get();
        return view('admin.pengeluaran.index', compact('pengeluaran'));
    }

    public function pengeluaranCreate()
    {
        $kategori = [
            'Bayar Listrik',
            'Sarapan', 
            'Bayar WiFi',
            'Kebutuhan Osman',
            'Other'
        ];
        return view('admin.pengeluaran.create', compact('kategori'));
    }

    public function pengeluaranStore(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'keterangan' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date'
        ]);

        Pengeluaran::create([
            'kategori' => $request->kategori,
            'keterangan' => $request->kategori === 'Other' ? $request->keterangan_custom : $request->keterangan,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'admin_id' => auth()->id()
        ]);

        return redirect()->route('admin.pengeluaran.index')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function pengeluaranDestroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();

        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }
    // ==================== EDIT PENGELUARAN ====================
    public function pengeluaranEdit($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $kategori = [
            'Bayar Listrik',
            'Sarapan', 
            'Bayar WiFi',
            'Kebutuhan Osman',
            'Other'
        ];
        return view('admin.pengeluaran.edit', compact('pengeluaran', 'kategori'));
    }

    public function pengeluaranUpdate(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string',
            'keterangan' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date'
        ]);

        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->update([
            'kategori' => $request->kategori,
            'keterangan' => $request->kategori === 'Other' ? $request->keterangan_custom : $request->keterangan,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('admin.pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }


    // ==================== PEMBAYARAN MANAGEMENT ====================
    // Di method pembayaranIndex
    public function pembayaranIndex()
    {
        $pembayaran = Pembayaran::with(['user', 'tagihan', 'admin'])
            ->pending()
            ->latest()
            ->get();

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    // Di method pembayaranHistory
    public function pembayaranHistory()
    {
        $pembayaran = Pembayaran::with(['user', 'tagihan', 'admin'])
            ->whereIn('status', ['accepted', 'rejected'])
            ->latest()
            ->paginate(20);

        // Hitung jumlah pembayaran pending
        $pembayaranPending = Pembayaran::where('status', 'pending')->count();

        return view('admin.pembayaran.history', compact('pembayaran', 'pembayaranPending'));
    }

    public function approvePembayaran($id)
    {
        DB::transaction(function () use ($id) {
            $pembayaran = Pembayaran::with('tagihan')->findOrFail($id);
            
            $pembayaran->update([
                'status' => 'accepted',
                'tanggal_proses' => now(),
                'admin_id' => auth()->id()
            ]);

            // Jika ada tagihan terkait, update status tagihan
            if ($pembayaran->tagihan) {
                $pembayaran->tagihan->update(['status' => 'success']);
            }

            // Buat notifikasi untuk murid
            Notification::create([
                'user_id' => $pembayaran->user_id,
                'type' => 'pembayaran_diterima',
                'title' => 'Pembayaran Diterima',
                'message' => "Pembayaran Anda sebesar Rp " . number_format($pembayaran->jumlah, 0, ',', '.') . " telah diterima",
                'data' => [
                    'pembayaran_id' => $pembayaran->id,
                    'jumlah' => $pembayaran->jumlah,
                    'status' => 'accepted'
                ],
                'related_type' => 'App\Models\Pembayaran',
                'related_id' => $pembayaran->id
            ]);

            // Kirim event realtime ke murid
            broadcast(new StatusPembayaranDiupdate($pembayaran));
        });

        return back()->with('success', 'Pembayaran berhasil disetujui.');
    }

    public function rejectPembayaran($id)
    {
        $pembayaran = Pembayaran::with('tagihan')->findOrFail($id); // TAMBAH with('tagihan')
        
        $pembayaran->update([
            'status' => 'rejected',
               'tanggal_proses' => now(),
            'admin_id' => auth()->id()
        ]);

        // TAMBAH: Jika ada tagihan terkait, update status tagihan ke 'unpaid'
        if ($pembayaran->tagihan) {
            $pembayaran->tagihan->update(['status' => 'unpaid']);
        }

        // Buat notifikasi untuk murid
        Notification::create([
            'user_id' => $pembayaran->user_id,
            'type' => 'pembayaran_ditolak',
            'title' => 'Pembayaran Ditolak',
            'message' => "Pembayaran Anda sebesar Rp " . number_format($pembayaran->jumlah, 0, ',', '.') . " ditolak. Silakan hubungi admin.",
            'data' => [
                'pembayaran_id' => $pembayaran->id,
                'jumlah' => $pembayaran->jumlah,
                'status' => 'rejected'
            ],
            'related_type' => 'App\Models\Pembayaran',
            'related_id' => $pembayaran->id
        ]);

        // Kirim event realtime ke murid
        broadcast(new StatusPembayaranDiupdate($pembayaran));

        return back()->with('success', 'Pembayaran berhasil ditolak.');
    }

    // Detail pembayaran
    public function showPembayaran($id)
    {
        $pembayaran = Pembayaran::with(['user', 'tagihan', 'admin'])->findOrFail($id);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    // ==================== SPP SETTING ====================
    public function sppSetting()
    {
        $setting = SppSetting::latest()->first();
        return view('admin.spp-setting', compact('setting'));
    }

    public function updateSppSetting(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric',
            'berlaku_mulai' => 'required|date'
        ]);

        SppSetting::create($request->only(['nominal', 'berlaku_mulai']));

        return back()->with('success', 'Setting SPP berhasil diperbarui.');
    }

    // ==================== PROFILE ====================
    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|unique:users,username,' . $user->id,
            'password' => 'nullable|min:8|confirmed'
        ]);

        $data = $request->only(['nama', 'email', 'username']);
        
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('profiles', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profile berhasil diperbarui.');
    }

    // ==================== PEMBAYARAN MANUAL ====================
    public function pembayaranManualCreate()
    {
        $murid = User::where('role', 'murid')->where('aktif', true)->get();
        $tagihan = Tagihan::where('status', 'unpaid')->get();
        
        // Ambil nominal SPP dari settings
        $sppSetting = SppSetting::orderBy('berlaku_mulai', 'desc')->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;
        
        return view('admin.pembayaran.manual-create', compact('murid', 'tagihan', 'nominalSpp'));
    }
    public function pembayaranManualStore(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipe_pembayaran' => 'required|in:spp,tagihan',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|in:Tunai,Transfer,QRIS',
            'keterangan' => 'required|string',
        ]);

        // Validasi conditional - PERBAIKI NAMA TABEL
        if ($request->tipe_pembayaran == 'tagihan') {
            $request->validate([
                'tagihan_id' => 'required|exists:tagihans,id' // UBAH: tagihan -> tagihans
            ]);
        } else {
            $request->validate([
                'bulan_mulai' => 'required|integer|min:1|max:12',
                'bulan_akhir' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer'
            ]);
        }

        
        try {
            DB::beginTransaction();

            // Buat pembayaran - HAPUS FIELD YANG BELUM ADA DI DATABASE
            $pembayaran = Pembayaran::create([
                'user_id' => $request->user_id,
                'tagihan_id' => $request->tipe_pembayaran == 'tagihan' ? $request->tagihan_id : null,
                'jenis_bayar' => 'lunas',
                'keterangan' => $request->keterangan,
                'jumlah' => $request->jumlah,
                'metode' => $request->metode,
                'bukti' => null,
                'status' => 'accepted',
                // 'tanggal_bayar' => $request->tanggal_bayar, // SEMENTARA DIHAPUS
                'tanggal_proses' => now(),
                'admin_id' => auth()->id(),
                // 'catatan_admin' => $request->catatan_admin, // SEMENTARA DIHAPUS
                'tanggal_upload' => now()
            ]);

            // Handle berdasarkan tipe
            if ($request->tipe_pembayaran == 'spp') {
                $this->handleSPP($request);
            } else {
                $this->handleTagihan($request);
            }

            // Notifikasi
            Notification::create([
                'user_id' => $request->user_id,
                'type' => 'pembayaran_manual',
                'title' => 'Pembayaran Manual',
                'message' => "Pembayaran manual sebesar Rp " . number_format($request->jumlah, 0, ',', '.') . " telah dicatat",
                'related_type' => 'App\Models\Pembayaran',
                'related_id' => $pembayaran->id
            ]);

            DB::commit();

            return redirect()->route('admin.pembayaran.history')->with('success', 'Pembayaran manual berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    private function handleSPP($request)
    {
        $bulanMulai = $request->bulan_mulai;
        $bulanAkhir = $request->bulan_akhir;
        $tahun = $request->tahun;
        
        // Hitung jumlah bulan
        $jumlahBulan = ($bulanAkhir - $bulanMulai) + 1;
        $jumlahPerBulan = $request->jumlah / $jumlahBulan;

        for ($bulan = $bulanMulai; $bulan <= $bulanAkhir; $bulan++) {
            $tagihan = Tagihan::where('user_id', $request->user_id)
                ->where('jenis', 'spp')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            if (!$tagihan) {
                // Buat tagihan baru
                Tagihan::create([
                    'user_id' => $request->user_id,
                    'jenis' => 'spp',
                    'keterangan' => 'SPP Bulan ' . $this->getNamaBulan($bulan) . ' ' . $tahun,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'jumlah' => $jumlahPerBulan,
                    'status' => 'success'
                ]);
            } else {
                // Update tagihan existing
                $tagihan->update(['status' => 'success']);
            }
        }
    }

    private function handleTagihan($request)
    {
        $tagihan = Tagihan::find($request->tagihan_id);
        if ($tagihan) {
            $tagihan->update(['status' => 'success']);
        }
    }

    private function getNamaBulan($bulan)
    {
        $bulanArr = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanArr[$bulan] ?? '';
    }

    
    // ==================== LAPORAN & EXPORT ====================
    public function laporanIndex()
    {
        return view('admin.laporan.index');
    }

    public function exportTagihan(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $filename = 'laporan-tagihan-' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new TagihanExport($startDate, $endDate), $filename);
    }

    public function exportPembayaran(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $filename = 'laporan-pembayaran-' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new PembayaranExport($startDate, $endDate), $filename);
    }

    public function exportMurid()
    {
        $filename = 'data-murid-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new MuridExport(), $filename);
    }

    // ==================== BACKUP DATABASE ====================
    public function backupIndex()
    {
        $backups = [];
        $files = Storage::files('backups');
        
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => $this->formatBytes(Storage::size($file)),
                'date' => \Carbon\Carbon::createFromTimestamp(Storage::lastModified($file))->format('d/m/Y H:i')
            ];
        }
        
        // Sort by date descending
        usort($backups, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return view('admin.backup.index', compact('backups'));
    }

    public function createBackup()
    {
        \Artisan::call('backup:run');
        
        return back()->with('success', 'Backup database berhasil dibuat.');
    }

    public function downloadBackup($file)
    {
        $path = "backups/{$file}";
        
        if (!Storage::exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }
        
        return Storage::download($path);
    }

    public function deleteBackup($file)
    {
        $path = "backups/{$file}";
        
        if (Storage::exists($path)) {
            Storage::delete($path);
            return back()->with('success', 'Backup berhasil dihapus.');
        }
        
        return back()->with('error', 'File backup tidak ditemukan.');
    }

    private function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        }
        
        return '0 bytes';
    }
}