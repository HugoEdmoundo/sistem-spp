<?php
namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\SppSetting;
use App\Helpers\NumberHelper;
use App\Events\PembayaranDibuat;
use App\Mail\PembayaranNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\TahunTrait; // ← ADD THIS

class MuridController extends Controller
{
    use TahunTrait; // ← ADD THIS
    public function dashboard()
    {   
        $user = auth()->user();
        
        // Hitung statistik
        $totalTagihan = Tagihan::where('user_id', $user->id)
            ->whereIn('status', ['unpaid', 'rejected'])
            ->sum('jumlah');
            
        $totalDibayar = Pembayaran::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->sum('jumlah');
            
        $tagihanPending = Tagihan::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Hitung PEMBAYARAN yang rejected
        $pembayaranRejectedCount = Pembayaran::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        // Ambil pembayaran pending dan rejected untuk ditampilkan
        $pembayaranPending = Pembayaran::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $pembayaranRejected = Pembayaran::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->latest()
            ->take(5)
            ->get();

        // Ambil nominal SPP saat ini
        $sppSetting = SppSetting::latest()->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;

        // Ambil tagihan terbaru untuk ditampilkan
        $tagihanTerbaru = Tagihan::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Ambri riwayat pembayaran terbaru
        $riwayatPembayaran = Pembayaran::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('murid.dashboard', [
            'totalTagihan' => $totalTagihan,
            'totalDibayar' => $totalDibayar,
            'tagihanPending' => $tagihanPending,
            'tagihanRejected' => $pembayaranRejectedCount,
            'pembayaranPending' => $pembayaranPending,
            'pembayaranRejected' => $pembayaranRejected,
            'nominalSpp' => $nominalSpp,
            'tagihan' => $tagihanTerbaru,
            'riwayatPembayaran' => $riwayatPembayaran
        ]);
    }


    // METHOD REKAP SPP YANG DIPERLUKAN
    public function rekapSppSaya()
    {
        $user = auth()->user();
        $tahun = request('tahun', $this->getTahunSekarang());
        
        // GUNAKAN TRAIT METHOD
        $tahunTersedia = $this->getTahunTersedia($user->id);
        
        $statusSpp = $user->getStatusSppTahunan($tahun);

        return view('murid.rekap-spp', compact(
            'statusSpp',
            'tahun',
            'tahunTersedia',
            'user'
        ));
    }
    
    // METHOD GENERATE KUITANSI YANG DIPERLUKAN
    public function generateKuitansi($pembayaranId)
{
    try {
        $pembayaran = Pembayaran::where('user_id', auth()->id())
            ->with(['user', 'admin', 'tagihan'])
            ->findOrFail($pembayaranId);

        if ($pembayaran->status !== 'accepted') {
            return redirect()->route('murid.pembayaran.history')
                ->with('error', '❌ Kuitansi hanya bisa diunduh untuk pembayaran yang sudah diterima.');
        }

        $data = [
            'pembayaran' => $pembayaran,
            'tanggal_sekarang' => now()->format('d F Y'),
            'jam_sekarang' => now()->format('H:i:s'),
        ];

        // Generate PDF dengan setting A4
        $pdf = Pdf::loadView('murid.kuitansi-pdf', $data)
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
        \Log::error('Error generating kuitansi: ' . $e->getMessage());
        return redirect()->route('murid.pembayaran.history')
            ->with('error', '❌ Gagal generate kuitansi. Silakan coba lagi.');
    }
}

    // Tambahkan method helper untuk terbilang
    private function terbilang($angka)
    {
        return $this->convertToTerbilang($angka);
    }

    // Method konversi angka ke terbilang
    private function convertToTerbilang($angka) 
    {
        $angka = abs($angka);
        $bilangan = array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas');
        
        if ($angka < 12) {
            return $bilangan[$angka];
        } else if ($angka < 20) {
            return $bilangan[$angka - 10] . ' Belas';
        } else if ($angka < 100) {
            $hasil_bagi = floor($angka / 10);
            $hasil_mod = $angka % 10;
            return trim(sprintf('%s Puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
        } else if ($angka < 200) {
            return sprintf('Seratus %s', $this->convertToTerbilang($angka - 100));
        } else if ($angka < 1000) {
            $hasil_bagi = floor($angka / 100);
            $hasil_mod = $angka % 100;
            return trim(sprintf('%s Ratus %s', $bilangan[$hasil_bagi], $this->convertToTerbilang($hasil_mod)));
        } else if ($angka < 2000) {
            return trim(sprintf('Seribu %s', $this->convertToTerbilang($angka - 1000)));
        } else if ($angka < 1000000) {
            $hasil_bagi = floor($angka / 1000);
            $hasil_mod = $angka % 1000;
            return sprintf('%s Ribu %s', $this->convertToTerbilang($hasil_bagi), $this->convertToTerbilang($hasil_mod));
        } else if ($angka < 1000000000) {
            $hasil_bagi = floor($angka / 1000000);
            $hasil_mod = $angka % 1000000;
            return trim(sprintf('%s Juta %s', $this->convertToTerbilang($hasil_bagi), $this->convertToTerbilang($hasil_mod)));
        }
        
        return 'Angka terlalu besar';
    }
    // METHOD TAGIHAN INDEX YANG DIPERLUKAN
    public function tagihanIndex()
    {
        $user = auth()->user();
        $tagihan = Tagihan::where('user_id', $user->id)
            ->latest()
            ->paginate(15);
        
        return view('murid.tagihan.index', compact('tagihan'));
    }

    // METHOD PEMBAYARAN HISTORY YANG DIPERLUKAN
    public function pembayaranHistory()
    {
        $user = auth()->user();
        $pembayaran = Pembayaran::where('user_id', $user->id)
            ->with(['tagihan', 'admin'])
            ->latest()
            ->paginate(15);
        
        return view('murid.pembayaran.history', compact('pembayaran'));
    }

    // METHOD PROFILE YANG DIPERLUKAN
    public function profile()
    {
        $user = auth()->user();
        return view('murid.profile', compact('user'));
    }

    // METHOD SHOW BAYAR SPP YANG DIPERLUKAN
    public function showBayarSpp()
    {
        $sppSetting = SppSetting::latest()->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;
        
        $tahun = $this->getTahunSekarang();
        $bulanSekarang = date('n');
        
        // TAMBAHKAN TAHUN UNTUK SELECT
        $tahunUntukSelect = $this->getTahunUntukSelect(2024, 2030);

        return view('murid.bayar-spp', compact(
            'nominalSpp', 
            'tahun', 
            'bulanSekarang',
            'tahunUntukSelect' // ← ADD THIS
        ));
    }


    public function bayarSpp(Request $request)
    {
        $request->validate([
            'bulan_mulai' => 'required|integer|min:1|max:12',
            'bulan_akhir' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|string',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'keterangan' => 'required|string'
        ]);

        // Validasi bulan
        if ($request->bulan_mulai > $request->bulan_akhir) {
            return back()->withErrors(['bulan_akhir' => 'Bulan akhir harus lebih besar atau sama dengan bulan mulai.'])->withInput();
        }

        $user = auth()->user();
        
        // VALIDASI: Cek apakah ada bulan yang sudah dibayar
        $validasiPembayaran = $user->bisaBayarSpp($request->tahun, $request->bulan_mulai, $request->bulan_akhir);
        
        if (!$validasiPembayaran['bisa_proses']) {
            $bulanSudahDibayar = $validasiPembayaran['sudah_dibayar'];
            $bulanNames = array_map(function($bulan) {
                return $this->getNamaBulan($bulan);
            }, $bulanSudahDibayar);
            
            return back()->withErrors([
                'bulan_mulai' => 'Bulan ' . implode(', ', $bulanNames) . ' sudah dibayar. Silakan pilih bulan lain.'
            ])->withInput();
        }

        // Lanjutkan proses pembayaran...
        $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');

        $pembayaran = Pembayaran::create([
            'tagihan_id' => null,
            'user_id' => $user->id,
            'metode' => $request->metode,
            'bukti' => $buktiPath,
            'jumlah' => $request->jumlah,
            'status' => 'pending',
            'keterangan' => $request->keterangan,
            'jenis_bayar' => 'lunas',
            'tanggal_upload' => now(),
            'tahun' => $request->tahun,
            'bulan_mulai' => $request->bulan_mulai,
            'bulan_akhir' => $request->bulan_akhir
        ]);

        return redirect()->route('murid.bayar.spp.page')->with('success', 'Pembayaran SPP berhasil diupload. Menunggu verifikasi admin.');
    }

    private function getNamaBulan($bulan): string
    {
        $bulanArr = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanArr[$bulan] ?? '';
    }
        
    public function uploadBukti(Request $request, $id)
    {
        $tagihan = Tagihan::where('user_id', auth()->id())->findOrFail($id);
        
        if ($tagihan->status !== 'unpaid' && $tagihan->status !== 'rejected') {
            return back()->with('error', 'Tagihan tidak dapat diproses.');
        }

        $request->validate([
            'metode' => 'required',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');

        // Hapus pembayaran sebelumnya jika ada (untuk rejected)
        Pembayaran::where('tagihan_id', $tagihan->id)->delete();

        $pembayaran = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'user_id' => auth()->id(),
            'metode' => $request->metode,
            'bukti' => $buktiPath,
            'jumlah' => $tagihan->jumlah,
            'status' => 'pending',
            'keterangan' => "Bayar tagihan: {$tagihan->keterangan}",
            'jenis_bayar' => 'lunas',
            'tanggal_upload' => now()
        ]);

        $tagihan->update(['status' => 'pending']);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    // app/Http/Controllers/MuridController.php

    // Method untuk upload ulang pembayaran SPP yang direject
    public function uploadUlangSpp(Request $request, $id)
    {
        $pembayaranLama = Pembayaran::where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->findOrFail($id);

        $request->validate([
            'metode' => 'required|string',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'keterangan' => 'required|string'
        ]);

        $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');

        // Buat pembayaran baru berdasarkan yang direject
        Pembayaran::create([
            'tagihan_id' => $pembayaranLama->tagihan_id,
            'user_id' => auth()->id(),
            'metode' => $request->metode,
            'bukti' => $buktiPath,
            'jumlah' => $pembayaranLama->jumlah,
            'status' => 'pending',
            'alasan_reject' => null,
            'keterangan' => $request->keterangan,
            'jenis_bayar' => 'lunas',
            'tanggal_upload' => now(),
            'tanggal_bayar' => now(),
            'tahun' => $pembayaranLama->tahun,
            'bulan_mulai' => $pembayaranLama->bulan_mulai,
            'bulan_akhir' => $pembayaranLama->bulan_akhir
        ]);

        return redirect()->route('murid.pembayaran.history')
            ->with('success', 'Bukti pembayaran SPP berhasil diupload ulang. Menunggu verifikasi admin.');
    }

    public function uploadUlangTagihan(Request $request, $id)
    {
        $pembayaranLama = Pembayaran::where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->findOrFail($id);

        $request->validate([
            'metode' => 'required|string',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');

        // Buat pembayaran baru
        Pembayaran::create([
            'tagihan_id' => $pembayaranLama->tagihan_id,
            'user_id' => auth()->id(),
            'metode' => $request->metode,
            'bukti' => $buktiPath,
            'jumlah' => $pembayaranLama->jumlah,
            'status' => 'pending',
            'alasan_reject' => null,
            'keterangan' => $pembayaranLama->keterangan,
            'jenis_bayar' => 'lunas',
            'tanggal_upload' => now(),
            'tanggal_bayar' => now()
        ]);

        // Update status tagihan menjadi pending
        if ($pembayaranLama->tagihan) {
            $pembayaranLama->tagihan->update(['status' => 'pending']);
        }

        return redirect()->route('murid.pembayaran.history')
            ->with('success', 'Bukti pembayaran tagihan berhasil diupload ulang. Menunggu verifikasi admin.');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Prepare data untuk update
        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'username' => $request->username,
        ];
        
        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle upload foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists('public/' . $user->foto)) {
                Storage::delete('public/' . $user->foto);
            }
            
            // Simpan foto baru
            $fotoPath = $request->file('foto')->store('profiles', 'public');
            $data['foto'] = $fotoPath;
        }

        // Update user
        $user->update($data);

        return redirect()->route('murid.profile')->with('success', 'Profile berhasil diperbarui!');
    }
}