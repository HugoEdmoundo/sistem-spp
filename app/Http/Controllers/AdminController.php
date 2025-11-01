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

        // Debug: Cek data yang diterima
        logger('Reject Request Data:', $request->all());
        logger('Auth User:', [auth()->user() ? auth()->user()->id : 'No user']);

        $pembayaran = Pembayaran::with('tagihan')->find($id);
        
        if (!$pembayaran) {
            logger('Pembayaran not found:', ['id' => $id]);
            return back()->with('error', 'Pembayaran tidak ditemukan.');
        }

        logger('Pembayaran before update:', $pembayaran->toArray());

        // Update langsung tanpa transaction dulu
        $pembayaran->status = 'rejected';
        $pembayaran->alasan_reject = $request->alasan_reject;
        $pembayaran->tanggal_proses = now();
        $pembayaran->admin_id = auth()->id();
        
        $saved = $pembayaran->save();
        
        logger('Pembayaran after update:', [
            'saved' => $saved,
            'pembayaran' => $pembayaran->fresh()->toArray()
        ]);

        if ($saved) {
            // Update tagihan jika ada
            if ($pembayaran->tagihan) {
                $pembayaran->tagihan->update(['status' => 'unpaid']);
                logger('Tagihan updated:', ['tagihan_id' => $pembayaran->tagihan->id]);
            }

            return back()->with('success', 'Pembayaran berhasil ditolak dengan alasan.');
        } else {
            logger('Failed to save pembayaran');
            return back()->with('error', 'Gagal menyimpan perubahan.');
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
            if (class_exists('App\Events\StatusPembayaranDiupdate')) {
                broadcast(new \App\Events\StatusPembayaranDiupdate($pembayaran));
            }
        });

        return back()->with('success', 'Pembayaran berhasil disetujui.');
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
        $tagihan = Tagihan::where('status', 'unpaid')->get();
        
        $sppSetting = SppSetting::orderBy('berlaku_mulai', 'desc')->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;
        
        // TAMBAHKAN INI UNTUK PASS TAHUN KE VIEW
        $tahunUntukSelect = $this->getTahunUntukSelect(2024, 2030);
        
        return view('admin.pembayaran.manual-create', compact(
            'murid', 
            'tagihan', 
            'nominalSpp',
            'tahunUntukSelect' // ← ADD THIS
        ));
    }
    
    // Di AdminController.php - Method pembayaranManualStore
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

        if ($request->tipe_pembayaran == 'tagihan') {
            $request->validate([
                'tagihan_id' => 'required|exists:tagihans,id'
            ]);

            // Validasi: cek apakah tagihan sudah lunas
            $tagihan = Tagihan::find($request->tagihan_id);
            if ($tagihan->status == 'success') {
                return back()->with('error', '❌ Tagihan ini sudah lunas! Tidak bisa melakukan pembayaran manual.')->withInput();
            }
        } else {
            $request->validate([
                'bulan_mulai' => 'required|integer|min:1|max:12',
                'bulan_akhir' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer'
            ]);

            // Validasi range bulan
            if ($request->bulan_mulai > $request->bulan_akhir) {
                return back()->with('error', '❌ Bulan akhir harus lebih besar atau sama dengan bulan mulai!')->withInput();
            }

            // VALIDASI: Cek duplikasi SPP
            $user = User::find($request->user_id);
            $validasiPembayaran = $user->bisaBayarSpp($request->tahun, $request->bulan_mulai, $request->bulan_akhir);
            
            if (!$validasiPembayaran['bisa_proses']) {
                $bulanSudahDibayar = $validasiPembayaran['sudah_dibayar'];
                $bulanNames = array_map(function($bulan) {
                    return User::getNamaBulanStatic($bulan);
                }, $bulanSudahDibayar);
                
                $errorMessage = '❌ Tidak bisa melakukan pembayaran manual karena bulan ' . implode(', ', $bulanNames) . ' sudah dibayar. Silakan pilih bulan lain.';
                return back()->with('error', $errorMessage)->withInput();
            }

            // Validasi jumlah SPP
            $sppSetting = SppSetting::latest()->first();
            $nominalSppPerBulan = $sppSetting ? $sppSetting->nominal : 0;
            $jumlahBulan = ($request->bulan_akhir - $request->bulan_mulai) + 1;
            $jumlahSeharusnya = $nominalSppPerBulan * $jumlahBulan;
            
            // Beri toleransi ±10% untuk pembayaran manual
            $toleransi = $jumlahSeharusnya * 0.1;
            $minimal = $jumlahSeharusnya - $toleransi;
            $maksimal = $jumlahSeharusnya + $toleransi;
            
            if ($request->jumlah < $minimal || $request->jumlah > $maksimal) {
                return back()->with('error', "❌ Jumlah pembayaran tidak sesuai! Untuk $jumlahBulan bulan seharusnya Rp " . number_format($jumlahSeharusnya, 0, ',', '.') . " (±10%)")->withInput();
            }
        }
        
        try {
            DB::beginTransaction();

            $pembayaran = Pembayaran::create([
                'user_id' => $request->user_id,
                'tagihan_id' => $request->tipe_pembayaran == 'tagihan' ? $request->tagihan_id : null,
                'jenis_bayar' => 'lunas',
                'keterangan' => $request->keterangan,
                'jumlah' => $request->jumlah,
                'metode' => $request->metode,
                'bukti' => null,
                'status' => 'accepted',
                'tanggal_proses' => now(),
                'admin_id' => auth()->id(),
                'tanggal_upload' => now(),
                'tahun' => $request->tahun ?? null,
                'bulan_mulai' => $request->bulan_mulai ?? null,
                'bulan_akhir' => $request->bulan_akhir ?? null
            ]);

            if ($request->tipe_pembayaran == 'spp') {
                $this->handleSPP($request);
            } else {
                $this->handleTagihan($request);
            }

            // Buat notifikasi
            Notification::create([
                'user_id' => $request->user_id,
                'type' => 'pembayaran_manual',
                'title' => 'Pembayaran Manual',
                'message' => "Pembayaran manual sebesar Rp " . number_format($request->jumlah, 0, ',', '.') . " telah dicatat",
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