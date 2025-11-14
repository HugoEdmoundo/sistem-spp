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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\TahunTrait; // ← ADD THIS


class AdminController extends Controller
{
    use TahunTrait;
   public function dashboard()
    {
        // Hitung total murid aktif
        $totalMurid = User::where('role', 'murid')->where('aktif', true)->count();
        
        // Total tagihan bulan ini (yang belum dibayar)
        $totalTagihanBulanIni = Tagihan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'unpaid')
            ->sum('jumlah');

        // Total pembayaran yang sudah diterima
        $totalPembayaran = Pembayaran::where('status', 'accepted')->sum('jumlah');
        
        // Total pengeluaran
        $totalPengeluaran = Pengeluaran::sum('jumlah');
        
        // Total akhir = Pembayaran - Pengeluaran
        $totalAkhir = $totalPembayaran - $totalPengeluaran;
        
        // Pembayaran pending
        $pembayaranPending = Pembayaran::where('status', 'pending')->count();

        // TOTAL TAGIHAN YANG BELUM DIBAYAR (ini yang diperlukan)
        $totalTagihan = Tagihan::where('status', 'unpaid')->sum('jumlah');

        return view('admin.dashboard', compact(
            'totalMurid', 
            'totalTagihanBulanIni', 
            'totalAkhir',
            'totalPembayaran',
            'totalPengeluaran',
            'pembayaranPending',
            'totalTagihan' // ← TAMBAHKAN INI
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
        
        // TAMBAHKAN TAHUN UNTUK SELECT
        $tahunUntukSelect = $this->getTahunUntukSelect(2024, 2030);
        
        return view('admin.tagihan.create', compact('murid', 'tahunUntukSelect'));
    }
    
    // Di Controller
    public function tagihanStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'keterangan' => 'required',
            'jumlah' => 'required|numeric|min:0'
            // Hapus validasi untuk 'jenis'
        ]);

        Tagihan::create([
            'user_id' => $request->user_id,
            'jenis' => 'custom', // Set otomatis sebagai custom
            'keterangan' => $request->keterangan,
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
        // Pastikan menggunakan paginate(), bukan get()
        $pembayaran = Pembayaran::with(['user', 'tagihan'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20); // <- PASTIKAN INI paginate() BUKAN get()

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    // Di method pembayaranHistory
    public function pembayaranHistory()
    {
        $query = Pembayaran::with(['user', 'tagihan', 'admin'])
            ->whereIn('status', ['accepted', 'rejected']);

        // Filter status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter metode
        if (request('metode')) {
            $query->where('metode', request('metode'));
        }

        // Filter search
        if (request('search')) {
            $search = request('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $pembayaran = $query->latest()->paginate(20);

        // Hitung jumlah pembayaran pending
        $pembayaranPending = Pembayaran::where('status', 'pending')->count();

        return view('admin.pembayaran.history', compact('pembayaran', 'pembayaranPending'));
    }

    public function rejectPembayaran(Request $request, $id)
    {
        $request->validate([
            'alasan_reject' => 'required|string|min:5|max:500'
        ]);

        try {
            DB::beginTransaction();

            $pembayaran = Pembayaran::with('tagihan')->findOrFail($id);
            
            // Update pembayaran
            $pembayaran->update([
                'status' => 'rejected',
                'alasan_reject' => $request->alasan_reject,
                'tanggal_proses' => now(),
                'admin_id' => auth()->id()
            ]);

            // Update status tagihan jika ada
            if ($pembayaran->tagihan) {
                $pembayaran->tagihan->update(['status' => 'unpaid']);
            }

            // Buat notifikasi untuk murid
            Notification::create([
                'user_id' => $pembayaran->user_id,
                'type' => 'pembayaran_ditolak',
                'title' => 'Pembayaran Ditolak',
                'message' => "Pembayaran Anda sebesar Rp " . number_format($pembayaran->jumlah, 0, ',', '.') . " ditolak. Alasan: " . $request->alasan_reject,
                'data' => [
                    'pembayaran_id' => $pembayaran->id,
                    'jumlah' => $pembayaran->jumlah,
                    'status' => 'rejected',
                    'alasan_reject' => $request->alasan_reject
                ],
                'related_type' => 'App\Models\Pembayaran',
                'related_id' => $pembayaran->id
            ]);

            DB::commit();

            return back()->with('success', 'Pembayaran berhasil ditolak.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting payment: ' . $e->getMessage());
            return back()->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
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

        // Untuk TAGIHAN
        if ($pembayaran->tagihan) {
            $tagihan = $pembayaran->tagihan;
            
            // Hitung TOTAL semua pembayaran yang sudah accepted
            $totalDibayar = $tagihan->pembayaran()
                ->where('status', 'accepted')
                ->sum('jumlah');
            
            // Tentukan jenis bayar berdasarkan apakah sudah lunas atau masih cicilan
            if ($totalDibayar >= $tagihan->jumlah) {
                // SUDAH LUNAS - Total semua pembayaran >= jumlah tagihan
                $pembayaran->update(['jenis_bayar' => 'lunas']);
                $tagihan->update(['status' => 'success']);
            } else {
                // MASIH CICILAN - Total pembayaran masih kurang
                $pembayaran->update(['jenis_bayar' => 'cicilan']);
                $tagihan->update(['status' => 'unpaid']); // Tetap unpaid sampai lunas
            }
        }
        // Untuk SPP
        else {
            $sppSetting = SppSetting::latest()->first();
            $nominalSppPerBulan = $sppSetting ? $sppSetting->nominal : 0;
            $jumlahBulan = ($pembayaran->bulan_akhir - $pembayaran->bulan_mulai) + 1;
            $totalHarusBayar = $nominalSppPerBulan * $jumlahBulan;
            
            if ($pembayaran->jumlah >= $totalHarusBayar) {
                $pembayaran->update(['jenis_bayar' => 'lunas']);
            } else {
                $pembayaran->update(['jenis_bayar' => 'cicilan']);
            }
        }

        // Buat notifikasi
        $this->createPembayaranNotification($pembayaran);
    });

    return back()->with('success', 'Pembayaran berhasil disetujui.');
}

private function createPembayaranNotification($pembayaran)
{
    $jenis = $pembayaran->tagihan_id ? 'tagihan' : 'SPP';
    $status = $pembayaran->isLunas() ? 'LUNAS' : 'CICILAN';
    
    if ($pembayaran->tagihan) {
        $tagihan = $pembayaran->tagihan;
        $sisa = $tagihan->sisa_tagihan;
        
        $message = "Pembayaran {$jenis} sebesar Rp " . number_format($pembayaran->jumlah, 0, ',', '.') . 
                  " telah diterima. Status: {$status}. Sisa: Rp " . number_format($sisa, 0, ',', '.');
    } else {
        $message = "Pembayaran {$jenis} sebesar Rp " . number_format($pembayaran->jumlah, 0, ',', '.') . 
                  " telah diterima. Status: {$status}";
    }

    Notification::create([
        'user_id' => $pembayaran->user_id,
        'type' => 'pembayaran_diterima',
        'title' => 'Pembayaran Diterima',
        'message' => $message,
        'data' => [
            'pembayaran_id' => $pembayaran->id,
            'jumlah' => $pembayaran->jumlah,
            'jenis_bayar' => $pembayaran->jenis_bayar
        ],
        'related_type' => 'App\Models\Pembayaran',
        'related_id' => $pembayaran->id
    ]);
}

    // Detail pembayaran
    public function showPembayaran($id)
    {
        $pembayaran = Pembayaran::with(['user', 'tagihan', 'admin'])->findOrFail($id);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function generateKuitansi($pembayaranId)
    {
        try {
            $pembayaran = Pembayaran::with(['user', 'admin', 'tagihan'])
                ->findOrFail($pembayaranId);

            // Admin bisa akses semua kuitansi, tidak perlu cek user_id seperti di murid

            $data = [
                'pembayaran' => $pembayaran,
                'tanggal_sekarang' => now()->format('d F Y'),
                'jam_sekarang' => now()->format('H:i:s'),
            ];

            $pdf = Pdf::loadView('admin.kuitansi-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'Times New Roman',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'chroot' => public_path(),
                    'dpi' => 150
                ]);
            
            $filename = 'Kuitansi-' . $pembayaran->user->nama . '-' . now()->format('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error generating kuitansi admin: ' . $e->getMessage());
            return redirect()->route('admin.pembayaran.history')
                ->with('error', '❌ Gagal generate kuitansi: ' . $e->getMessage());
        }
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
        
        // Ambil tagihan yang BELUM LUNAS saja (bisa dicicil)
        $tagihan = Tagihan::whereIn('status', ['unpaid', 'pending'])
            ->with(['user', 'pembayaran' => function($query) {
                $query->where('status', 'accepted');
            }])
            ->get()
            ->map(function($tagihan) {
                // Hitung total dibayar dan sisa
                $tagihan->total_dibayar = $tagihan->pembayaran->sum('jumlah');
                $tagihan->sisa_tagihan = $tagihan->jumlah - $tagihan->total_dibayar;
                return $tagihan;
            })
            ->filter(function($tagihan) {
                // Hanya tampilkan tagihan yang masih ada sisanya
                return $tagihan->sisa_tagihan > 0;
            });
        
        $sppSetting = SppSetting::orderBy('berlaku_mulai', 'desc')->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;
        
        $tahunUntukSelect = $this->getTahunUntukSelect(2024, 2030);
        
        return view('admin.pembayaran.manual-create', compact(
            'murid', 
            'tagihan', 
            'nominalSpp',
            'tahunUntukSelect'
        ));
    }
    
    // Di AdminController.php - Method pembayaranManualStore
    public function pembayaranManualStore(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipe_pembayaran' => 'required|in:spp,tagihan',
            'jumlah' => 'required|numeric|min:1000',
            'metode' => 'required|in:Tunai,Transfer,QRIS',
            'keterangan' => 'required|string',
            'tanggal_bayar' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->user_id);

            if ($request->tipe_pembayaran == 'tagihan') {
                $request->validate([
                    'tagihan_id' => 'required|exists:tagihans,id'
                ]);

                $tagihan = Tagihan::find($request->tagihan_id);
                
                // Validasi: pastikan tagihan milik user yang dipilih
                if ($tagihan->user_id != $request->user_id) {
                    return back()->with('error', '❌ Tagihan tidak sesuai dengan murid yang dipilih!')->withInput();
                }

                // Validasi jumlah bayar tidak melebihi sisa tagihan
                if ($request->jumlah > $tagihan->sisa_tagihan) {
                    return back()->with('error', '❌ Jumlah bayar melebihi sisa tagihan! Sisa: Rp ' . number_format($tagihan->sisa_tagihan, 0, ',', '.'))->withInput();
                }

                // Tentukan jenis bayar
                $akanLunas = $tagihan->akanLunasDenganJumlah($request->jumlah);
                $jenisBayar = $akanLunas ? 'lunas' : 'cicilan';

                // Buat pembayaran
                $pembayaran = Pembayaran::create([
                    'tagihan_id' => $tagihan->id,
                    'user_id' => $request->user_id,
                    'metode' => $request->metode,
                    'bukti' => null, // Manual payment no bukti
                    'jumlah' => $request->jumlah,
                    'status' => 'accepted', // Langsung accepted karena manual
                    'keterangan' => $request->keterangan,
                    'jenis_bayar' => $jenisBayar,
                    'tanggal_upload' => now(),
                    'tanggal_bayar' => $request->tanggal_bayar,
                    'tanggal_proses' => now(),
                    'admin_id' => auth()->id()
                ]);

                // Update status tagihan
                if ($akanLunas) {
                    $tagihan->update(['status' => 'success']);
                } else {
                    $tagihan->update(['status' => 'unpaid']); // Tetap unpaid karena masih cicilan
                }

            } else {
                // PEMBAYARAN SPP MANUAL
                $request->validate([
                    'bulan_mulai' => 'required|integer|min:1|max:12',
                    'bulan_akhir' => 'required|integer|min:1|max:12',
                    'tahun' => 'required|integer'
                ]);

                // Validasi range bulan
                if ($request->bulan_mulai > $request->bulan_akhir) {
                    return back()->with('error', '❌ Bulan akhir harus lebih besar atau sama dengan bulan mulai!')->withInput();
                }

                // Cek apakah sudah ada tagihan SPP untuk periode tersebut
                $existingTagihan = Tagihan::where('user_id', $request->user_id)
                    ->where('jenis', 'spp')
                    ->where('keterangan', 'like', '%' . $request->tahun . '%')
                    ->get();

                $bulanSudahAda = [];
                foreach ($existingTagihan as $tagihan) {
                    // Extract bulan dari keterangan
                    preg_match_all('/\b(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\b/', $tagihan->keterangan, $matches);
                    
                    foreach ($matches[0] as $namaBulan) {
                        $bulan = array_search($namaBulan, [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ]);
                        if ($bulan) {
                            $bulanSudahAda[] = $bulan;
                        }
                    }
                }

                // Filter bulan yang overlap dengan request
                $bulanOverlap = [];
                for ($bulan = $request->bulan_mulai; $bulan <= $request->bulan_akhir; $bulan++) {
                    if (in_array($bulan, $bulanSudahAda)) {
                        $bulanOverlap[] = $bulan;
                    }
                }

                if (!empty($bulanOverlap)) {
                    $bulanNames = array_map(function($bulan) {
                        return User::getNamaBulanStatic($bulan);
                    }, $bulanOverlap);
                    
                    return back()->with('error', '❌ Bulan ' . implode(', ', $bulanNames) . ' sudah memiliki tagihan SPP!')->withInput();
                }

                // Generate tagihan SPP
                $tagihanSpp = Tagihan::generateTagihanSpp(
                    $request->user_id,
                    $request->tahun,
                    $request->bulan_mulai,
                    $request->bulan_akhir
                );

                // Tentukan jenis bayar
                $sppSetting = SppSetting::latest()->first();
                $nominalSppPerBulan = $sppSetting ? $sppSetting->nominal : 0;
                $jumlahBulan = ($request->bulan_akhir - $request->bulan_mulai) + 1;
                $totalHarusBayar = $nominalSppPerBulan * $jumlahBulan;

                $jenisBayar = $request->jumlah >= $totalHarusBayar ? 'lunas' : 'cicilan';

                // Buat pembayaran
                $pembayaran = Pembayaran::create([
                    'tagihan_id' => $tagihanSpp->id,
                    'user_id' => $request->user_id,
                    'metode' => $request->metode,
                    'bukti' => null,
                    'jumlah' => $request->jumlah,
                    'status' => 'accepted',
                    'keterangan' => $request->keterangan,
                    'jenis_bayar' => $jenisBayar,
                    'tanggal_upload' => now(),
                    'tanggal_bayar' => $request->tanggal_bayar,
                    'tanggal_proses' => now(),
                    'admin_id' => auth()->id(),
                    'tahun' => $request->tahun,
                    'bulan_mulai' => $request->bulan_mulai,
                    'bulan_akhir' => $request->bulan_akhir
                ]);

                // Update status tagihan SPP
                if ($jenisBayar == 'lunas') {
                    $tagihanSpp->update(['status' => 'success']);
                } else {
                    $tagihanSpp->update(['status' => 'unpaid']);
                }
            }

            // Buat notifikasi untuk murid
            Notification::create([
                'user_id' => $request->user_id,
                'type' => 'pembayaran_manual',
                'title' => 'Pembayaran Manual',
                'message' => "Admin mencatat pembayaran manual sebesar Rp " . number_format($request->jumlah, 0, ',', '.') . " untuk " . ($request->tipe_pembayaran == 'spp' ? 'SPP' : 'tagihan'),
                'data' => [
                    'pembayaran_id' => $pembayaran->id,
                    'jumlah' => $request->jumlah,
                    'tipe' => $request->tipe_pembayaran
                ],
                'related_type' => 'App\Models\Pembayaran',
                'related_id' => $pembayaran->id
            ]);

            DB::commit();

            return redirect()->route('admin.pembayaran.history')
                ->with('success', '✅ Pembayaran manual berhasil dicatat!')
                ->with('info', '💰 Pembayaran telah otomatis diverifikasi dan tercatat dalam sistem.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal mencatat pembayaran manual: ' . $e->getMessage())->withInput();
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
                    'keterangan' => 'SPP Bulan ' . User::getNamaBulanStatic($bulan) . ' ' . $tahun,
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
    
    // ==================== MURID PEMBAYARAN ====================

   public function muridPembayaran($id)
    {
        $murid = User::where('role', 'murid')->findOrFail($id);
        $tahun = request('tahun', date('Y'));
        
        // Debug: Lihat data pembayaran yang ada
        $allPembayaran = Pembayaran::where('user_id', $id)->get();
        
        // Ambil tahun-tahun yang tersedia dari berbagai sumber
        $tahunDariPembayaran = Pembayaran::where('user_id', $id)
            ->whereNotNull('tahun')
            ->distinct()
            ->pluck('tahun')
            ->toArray();

        $tahunDariTanggal = Pembayaran::where('user_id', $id)
            ->selectRaw('YEAR(tanggal_upload) as tahun')
            ->distinct()
            ->pluck('tahun')
            ->toArray();

        $tahunDariTagihan = Tagihan::where('user_id', $id)
            ->whereNotNull('tahun')
            ->distinct()
            ->pluck('tahun')
            ->toArray();

        // Gabungkan semua tahun yang mungkin
        $tahunTersedia = array_unique(array_merge(
            $tahunDariPembayaran,
            $tahunDariTanggal,
            $tahunDariTagihan
        ));

        // Jika tidak ada data, tambahkan tahun saat ini dan sebelumnya
        if (empty($tahunTersedia)) {
            $tahunTersedia = [date('Y'), date('Y') - 1];
        }

        // Urutkan tahun descending (terbaru dulu)
        rsort($tahunTersedia);

        // Ambil semua pembayaran murid untuk riwayat
        $pembayaran = Pembayaran::where('user_id', $id)
            ->with(['admin', 'tagihan'])
            ->latest()
            ->get();

        // Gunakan method yang sudah diperbaiki
        $statusSpp = $murid->getStatusSppTahunan($tahun);
        
        // Hitung total yang sudah dibayar untuk tahun yang dipilih
        $totalDibayar = Pembayaran::where('user_id', $id)
            ->where('status', 'accepted')
            ->where(function($query) use ($tahun) {
                $query->where('tahun', $tahun)
                    ->orWhereYear('tanggal_upload', $tahun);
            })
            ->sum('jumlah');
        
        // Hitung pembayaran pending untuk badge
        $pembayaranPendingCount = Pembayaran::where('status', 'pending')->count();

        return view('admin.murid.pembayaran', compact(
            'murid',
            'pembayaran',
            'statusSpp',
            'tahun',
            'tahunTersedia',
            'totalDibayar',
            'pembayaranPendingCount'
        ));
    }
}