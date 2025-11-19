<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\SppSetting;
use App\Models\User;
use App\Helpers\NumberHelper;
use App\Events\PembayaranDibuat;
use App\Mail\PembayaranNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\TahunTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Collection;

class MuridController extends Controller
{
    use TahunTrait;

    public function dashboard()
    {   
        $user = auth()->user();
        
        // Hitung statistik TAGIHAN
        $totalTagihan = Tagihan::where('user_id', $user->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->sum('jumlah');
            
        $tagihanUnpaidCount = Tagihan::where('user_id', $user->id)
            ->where('status', 'unpaid')
            ->count();
            
        $tagihanPartialCount = Tagihan::where('user_id', $user->id)
            ->where('status', 'partial')
            ->count();

        $totalTagihanNotif = $tagihanUnpaidCount + $tagihanPartialCount;

        // Hitung statistik PEMBAYARAN
        $totalDibayar = Pembayaran::where('user_id', $user->id)
            ->whereIn('status', ['accepted', 'partial'])
            ->sum('jumlah');
            
        $pembayaranPendingCount = Pembayaran::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $pembayaranRejectedCount = Pembayaran::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        $pembayaranPartialCount = Pembayaran::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->where('jenis_bayar', 'cicilan')
            ->count();

        // Total notifikasi
        $totalNotifikasi = $pembayaranPendingCount + $pembayaranRejectedCount + $pembayaranPartialCount + $totalTagihanNotif;

        // Ambil data untuk ditampilkan
        $tagihanUnpaid = Tagihan::where('user_id', $user->id)
            ->where('status', 'unpaid')
            ->latest()
            ->take(5)
            ->get();

        $tagihanPartial = Tagihan::where('user_id', $user->id)
            ->where('status', 'partial')
            ->with(['pembayaran' => function($query) {
                $query->whereIn('status', ['accepted', 'partial']);
            }])
            ->latest()
            ->take(5)
            ->get();

        // Ambil SPP cicilan
        $sppCicilanPayments = Pembayaran::where('user_id', $user->id)
            ->whereNull('tagihan_id')
            ->where('status', 'accepted')
            ->where('jenis_bayar', 'cicilan')
            ->latest()
            ->take(5)
            ->get();

        // Buat array sederhana untuk SPP cicilan
        $sppCicilanArray = [];
        foreach ($sppCicilanPayments as $sppPayment) {
            $sppAsTagihan = $this->createSimpleVirtualTagihan($sppPayment);
            if ($sppAsTagihan && ($sppAsTagihan['is_cicilan'] ?? false)) {
                $sppCicilanArray[] = $sppAsTagihan;
            }
        }

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

        // Gabungkan dengan cara manual
        $allCicilan = collect();
        
        // Tambahkan tagihan partial biasa
        foreach ($tagihanPartial as $tagihan) {
            $allCicilan->push($tagihan);
        }
        
        // Tambahkan SPP cicilan
        foreach ($sppCicilanArray as $sppCicilan) {
            $allCicilan->push((object)$sppCicilan);
        }
        
        // Ambil 5 teratas
        $allCicilan = $allCicilan->take(5);

        // Ambil nominal SPP saat ini
        $sppSetting = SppSetting::latest()->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;

        // Ambil riwayat pembayaran terbaru
        $riwayatPembayaran = Pembayaran::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Ambil tagihan untuk section tagihan terbaru
        $tagihanTerbaru = Tagihan::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('murid.dashboard', [
            'totalTagihan' => $totalTagihan,
            'totalDibayar' => $totalDibayar,
            'tagihanUnpaidCount' => $tagihanUnpaidCount,
            'tagihanPartialCount' => $tagihanPartialCount + count($sppCicilanArray),
            'totalTagihanNotif' => $totalTagihanNotif,
            'pembayaranPendingCount' => $pembayaranPendingCount,
            'pembayaranRejectedCount' => $pembayaranRejectedCount,
            'pembayaranPartialCount' => $pembayaranPartialCount,
            'totalNotifikasi' => $totalNotifikasi,
            'tagihanUnpaid' => $tagihanUnpaid,
            'tagihanPartial' => $allCicilan,
            'pembayaranPending' => $pembayaranPending,
            'pembayaranRejected' => $pembayaranRejected,
            'nominalSpp' => $nominalSpp,
            'riwayatPembayaran' => $riwayatPembayaran,
            'tagihanTerbaru' => $tagihanTerbaru
        ]);
    }

    private function createSimpleVirtualTagihan($sppPayment)
    {
        $sppSetting = SppSetting::latest()->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;
        
        $bulanMulai = $sppPayment->bulan_mulai;
        $bulanAkhir = $sppPayment->bulan_akhir;
        $tahun = $sppPayment->tahun;
        
        if (!$bulanMulai || !$bulanAkhir) {
            return null;
        }

        $jumlahBulan = ($bulanAkhir - $bulanMulai) + 1;
        $totalTagihan = $nominalSpp * $jumlahBulan;
        
        // Hitung total sudah dibayar untuk SPP periode ini
        $totalDibayar = Pembayaran::where('user_id', $sppPayment->user_id)
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->where('bulan_mulai', $bulanMulai)
            ->where('bulan_akhir', $bulanAkhir)
            ->where('status', 'accepted')
            ->sum('jumlah');

        $sisaTagihan = $totalTagihan - $totalDibayar;
        
        // Status berdasarkan kondisi aktual
        $isLunas = $sisaTagihan <= 0;
        $isPending = Pembayaran::where('user_id', $sppPayment->user_id)
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->where('bulan_mulai', $bulanMulai)
            ->where('bulan_akhir', $bulanAkhir)
            ->where('status', 'pending')
            ->exists();
            
        $isRejected = Pembayaran::where('user_id', $sppPayment->user_id)
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->where('bulan_mulai', $bulanMulai)
            ->where('bulan_akhir', $bulanAkhir)
            ->where('status', 'rejected')
            ->exists();
            
        $isCicilan = $totalDibayar > 0 && $totalDibayar < $totalTagihan;
        $bisaBayar = $sisaTagihan > 0 && !$isPending;

        $persentaseDibayar = $totalTagihan > 0 ? ($totalDibayar / $totalTagihan) * 100 : 0;
        $minimalBayar = max(1000, $sisaTagihan * 0.1);
        
        // Return array sederhana
        return [
            'id' => 'spp_' . $sppPayment->id,
            'jenis' => 'spp',
            'keterangan' => 'SPP ' . $jumlahBulan . ' bulan (' . 
                User::getNamaBulanStatic($bulanMulai) . ' - ' . 
                User::getNamaBulanStatic($bulanAkhir) . ' ' . $tahun . ')',
            'jumlah' => $totalTagihan,
            'status' => $isLunas ? 'success' : ($isRejected ? 'rejected' : 'unpaid'),
            'created_at' => $sppPayment->created_at,
            'is_virtual' => true,
            'total_dibayar' => $totalDibayar,
            'sisa_tagihan' => $sisaTagihan,
            'persentase_dibayar' => $persentaseDibayar,
            'is_lunas' => $isLunas,
            'is_pending' => $isPending,
            'is_cicilan' => $isCicilan,
            'is_rejected' => $isRejected,
            'bisa_bayar' => $bisaBayar,
            'minimal_bayar' => $minimalBayar,
            'jumlah_formatted' => 'Rp ' . number_format($totalTagihan, 0, ',', '.'),
            'total_dibayar_formatted' => 'Rp ' . number_format($totalDibayar, 0, ',', '.'),
            'sisa_tagihan_formatted' => 'Rp ' . number_format($sisaTagihan, 0, ',', '.')
        ];
    }

    public function rekapSppSaya()
    {
        $user = auth()->user();
        $tahun = request('tahun', $this->getTahunSekarang());
        
        // Gunakan trait method
        $tahunTersedia = $this->getTahunTersedia($user->id);
        
        $statusSpp = $user->getStatusSppTahunan($tahun);

        return view('murid.rekap-spp', compact(
            'statusSpp',
            'tahun',
            'tahunTersedia',
            'user'
        ));
    }
    
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

            // Generate PDF dengan setting yang lebih kompatibel
            $pdf = Pdf::loadView('murid.kuitansi-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'Times New Roman',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => true,
                    'chroot' => public_path(),
                    'dpi' => 150,
                    'fontHeightRatio' => 0.9,
                    'enable_font_subsetting' => true
                ]);
            
            $filename = 'Kuitansi-' . $pembayaran->user->nama . '-' . now()->format('Y-m-d') . '.pdf';
            
            // Coba beberapa cara download
            try {
                return $pdf->download($filename);
            } catch (Exception $e) {
                // Fallback: return sebagai stream
                return response($pdf->output(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

        } catch (Exception $e) {
            \Log::error('Error generating kuitansi: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('murid.pembayaran.history')
                ->with('error', '❌ Gagal generate kuitansi: ' . $e->getMessage());
        }
    }

    private function terbilang($angka)
    {
        return $this->convertToTerbilang($angka);
    }

    private function convertToTerbilang($angka) 
    {
        $angka = abs($angka);
        $bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        
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

    public function getVirtualTagihanAttributes($virtualTagihan)
    {
        $isLunas = $virtualTagihan->status === 'success';
        $isPending = $virtualTagihan->pembayaran->first()->status === 'pending';
        $isCicilan = $virtualTagihan->pembayaran->first()->jenis_bayar === 'cicilan';
        $bisaBayar = !$isLunas;
        
        return [
            'is_lunas' => $isLunas,
            'is_pending' => $isPending,
            'is_cicilan' => $isCicilan,
            'jumlah_formatted' => 'Rp ' . number_format($virtualTagihan->jumlah, 0, ',', '.'),
            'total_dibayar_formatted' => 'Rp ' . number_format($virtualTagihan->total_dibayar, 0, ',', '.'),
            'sisa_tagihan_formatted' => 'Rp ' . number_format($virtualTagihan->sisa_tagihan, 0, ',', '.'),
            'bisa_bayar' => $bisaBayar,
            'minimal_bayar' => max(1000, $virtualTagihan->sisa_tagihan * 0.1),
            'persentase_dibayar' => $virtualTagihan->jumlah > 0 ? ($virtualTagihan->total_dibayar / $virtualTagihan->jumlah) * 100 : 0
        ];
    }

    public function tagihanIndex()
    {
        $user = auth()->user();
        
        // 1. Ambil tagihan biasa (non-SPP) yang belum lunas
        $tagihanQuery = Tagihan::where('user_id', $user->id)
            ->with(['pembayaran' => function($query) {
                $query->whereIn('status', ['accepted', 'pending', 'rejected']);
            }])
            ->where('status', '!=', 'success');

        if (request('jenis') == 'spp') {
            $tagihanQuery->where('jenis', 'spp');
        } elseif (request('jenis') == 'non-spp') {
            $tagihanQuery->where('jenis', '!=', 'spp');
        }

        $tagihan = $tagihanQuery->get();

        // 2. Ambil SPP untuk periode yang unik saja
        $sppPeriods = Pembayaran::where('user_id', $user->id)
            ->whereNull('tagihan_id')
            ->whereNotNull('tahun')
            ->whereNotNull('bulan_mulai')
            ->whereNotNull('bulan_akhir')
            ->select('tahun', 'bulan_mulai', 'bulan_akhir')
            ->distinct()
            ->get();

        $allData = collect();
        
        // Tambahkan tagihan biasa
        $allData = $allData->merge($tagihan);
        
        // Buat virtual tagihan untuk setiap periode unik
        foreach ($sppPeriods as $period) {
            // Ambil salah satu pembayaran untuk periode ini (prioritaskan yang belum lunas)
            $samplePayment = Pembayaran::where('user_id', $user->id)
                ->whereNull('tagihan_id')
                ->where('tahun', $period->tahun)
                ->where('bulan_mulai', $period->bulan_mulai)
                ->where('bulan_akhir', $period->bulan_akhir)
                ->orderBy('status') // pending > rejected > accepted
                ->first();
                
            if ($samplePayment) {
                $sppAsTagihan = $this->convertSppToTagihanFormat($samplePayment);
                if ($sppAsTagihan && !$sppAsTagihan->is_lunas) {
                    $allData->push($sppAsTagihan);
                }
            }
        }

        // Pagination manual
        $page = request('page', 1);
        $perPage = 10;
        $paginatedData = new LengthAwarePaginator(
            $allData->forPage($page, $perPage),
            $allData->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('murid.tagihan.index', ['tagihan' => $paginatedData]);
    }

    private function convertSppToTagihanFormat($sppPayment)
    {
        $sppSetting = SppSetting::latest()->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;
        
        $bulanMulai = $sppPayment->bulan_mulai;
        $bulanAkhir = $sppPayment->bulan_akhir;
        $tahun = $sppPayment->tahun;
        
        if (!$bulanMulai || !$bulanAkhir) {
            return null;
        }

        $jumlahBulan = ($bulanAkhir - $bulanMulai) + 1;
        $totalTagihan = $nominalSpp * $jumlahBulan;
        
        // Hitung total sudah dibayar untuk SPP periode ini (hanya yang accepted)
        $totalDibayar = Pembayaran::where('user_id', $sppPayment->user_id)
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->where('bulan_mulai', $bulanMulai)
            ->where('bulan_akhir', $bulanAkhir)
            ->where('status', 'accepted')
            ->sum('jumlah');

        $sisaTagihan = $totalTagihan - $totalDibayar;
        
        // Cek status berdasarkan kondisi aktual
        $isLunas = $sisaTagihan <= 0;
        $isPending = Pembayaran::where('user_id', $sppPayment->user_id)
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->where('bulan_mulai', $bulanMulai)
            ->where('bulan_akhir', $bulanAkhir)
            ->where('status', 'pending')
            ->exists();
            
        $isRejected = Pembayaran::where('user_id', $sppPayment->user_id)
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->where('bulan_mulai', $bulanMulai)
            ->where('bulan_akhir', $bulanAkhir)
            ->where('status', 'rejected')
            ->exists();
            
        $isCicilan = $totalDibayar > 0 && $totalDibayar < $totalTagihan;
        
        // Bisa bayar jika masih ada sisa dan tidak ada yang pending
        $bisaBayar = $sisaTagihan > 0 && !$isPending;

        $persentaseDibayar = $totalTagihan > 0 ? ($totalDibayar / $totalTagihan) * 100 : 0;
        $minimalBayar = max(1000, $sisaTagihan * 0.1);
        
        // Buat object tagihan virtual
        $virtualTagihan = new \stdClass();
        $virtualTagihan->id = 'spp_' . $sppPayment->id;
        $virtualTagihan->jenis = 'spp';
        $virtualTagihan->keterangan = 'SPP ' . $jumlahBulan . ' bulan (' . 
            User::getNamaBulanStatic($bulanMulai) . ' - ' . 
            User::getNamaBulanStatic($bulanAkhir) . ' ' . $tahun . ')';
        $virtualTagihan->jumlah = $totalTagihan;
        $virtualTagihan->status = $isLunas ? 'success' : ($isRejected ? 'rejected' : 'unpaid');
        $virtualTagihan->created_at = $sppPayment->created_at;
        $virtualTagihan->is_virtual = true;
        
        // Ambil semua pembayaran untuk periode ini
        $virtualTagihan->pembayaran = Pembayaran::where('user_id', $sppPayment->user_id)
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->where('bulan_mulai', $bulanMulai)
            ->where('bulan_akhir', $bulanAkhir)
            ->get();
        
        // Set atribut
        $virtualTagihan->total_dibayar = $totalDibayar;
        $virtualTagihan->sisa_tagihan = $sisaTagihan;
        $virtualTagihan->persentase_dibayar = $persentaseDibayar;
        $virtualTagihan->is_lunas = $isLunas;
        $virtualTagihan->is_pending = $isPending;
        $virtualTagihan->is_cicilan = $isCicilan;
        $virtualTagihan->is_rejected = $isRejected;
        $virtualTagihan->bisa_bayar = $bisaBayar;
        $virtualTagihan->minimal_bayar = $minimalBayar;
        $virtualTagihan->jumlah_formatted = 'Rp ' . number_format($totalTagihan, 0, ',', '.');
        $virtualTagihan->total_dibayar_formatted = 'Rp ' . number_format($totalDibayar, 0, ',', '.');
        $virtualTagihan->sisa_tagihan_formatted = 'Rp ' . number_format($sisaTagihan, 0, ',', '.');
        
        return $virtualTagihan;
    }

    public function pembayaranHistory()
    {
        $user = auth()->user();
        $pembayaran = Pembayaran::where('user_id', $user->id)
            ->with(['tagihan', 'admin'])
            ->latest()
            ->paginate(15);
        
        return view('murid.pembayaran.history', compact('pembayaran'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('murid.profile', compact('user'));
    }

    public function showBayarSpp()
    {
        $sppSetting = SppSetting::latest()->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;
        
        $tahun = $this->getTahunSekarang();
        $bulanSekarang = date('n');
        
        $tahunUntukSelect = $this->getTahunUntukSelect(2024, 2030);

        return view('murid.bayar-spp', compact(
            'nominalSpp', 
            'tahun', 
            'bulanSekarang',
            'tahunUntukSelect'
        ));
    }

    public function bayarSpp(Request $request)
    {
        $request->validate([
            'bulan_mulai' => 'required|integer|min:1|max:12',
            'bulan_akhir' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'jumlah' => 'required|numeric|min:1000',
            'metode' => 'required|string',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'keterangan' => 'required|string'
        ]);

        // Validasi bulan
        if ($request->bulan_mulai > $request->bulan_akhir) {
            return back()->withErrors(['bulan_akhir' => 'Bulan akhir harus lebih besar atau sama dengan bulan mulai.'])->withInput();
        }

        $user = auth()->user();
        
        // Validasi: Cek apakah bulan sudah ada tagihan SPP yang belum lunas
        $bulanSudahAda = [];
        for ($bulan = $request->bulan_mulai; $bulan <= $request->bulan_akhir; $bulan++) {
            if ($user->isBulanSudahDibayar($request->tahun, $bulan)) {
                $bulanSudahAda[] = $bulan;
            }
        }
        
        if (!empty($bulanSudahAda)) {
            $bulanNames = array_map(function($bulan) {
                return User::getNamaBulanStatic($bulan);
            }, $bulanSudahAda);
            
            return back()->withErrors([
                'bulan_mulai' => 'Bulan ' . implode(', ', $bulanNames) . ' sudah memiliki pembayaran SPP.'
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            // Upload bukti
            $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');

            // Hitung jumlah bulan untuk menentukan jenis bayar
            $jumlahBulan = ($request->bulan_akhir - $request->bulan_mulai) + 1;
            $sppSetting = SppSetting::latest()->first();
            $totalHarusBayar = $sppSetting ? ($sppSetting->nominal * $jumlahBulan) : 0;
            
            $jenisBayar = $request->jumlah >= $totalHarusBayar ? 'lunas' : 'cicilan';

            // Buat pembayaran SPP murni (tanpa tagihan)
            $pembayaran = Pembayaran::create([
                'user_id' => $user->id,
                'metode' => $request->metode,
                'bukti' => $buktiPath,
                'jumlah' => $request->jumlah,
                'status' => 'pending',
                'keterangan' => $request->keterangan,
                'jenis_bayar' => $jenisBayar,
                'tanggal_upload' => now(),
                'tanggal_bayar' => now(),
                'tahun' => $request->tahun,
                'bulan_mulai' => $request->bulan_mulai,
                'bulan_akhir' => $request->bulan_akhir
            ]);

            DB::commit();

            return redirect()->route('murid.pembayaran.history')
                ->with('success', 'Pembayaran SPP berhasil diupload. Menunggu verifikasi admin.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat pembayaran SPP: ' . $e->getMessage()])->withInput();
        }
    }

    public function uploadBukti(Request $request, $id)
    {
        \Log::info('=== UPLOAD BUKTI DIPANGGIL ===');

        // Handle SPP virtual ID
        if (strpos($id, 'spp_') === 0) {
            return $this->handleUploadBuktiSpp($request, $id);
        }

        $tagihan = Tagihan::where('user_id', auth()->id())->findOrFail($id);
        
        $request->validate([
            'metode' => 'required|string',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'jumlah' => 'required|numeric|min:1000|max:' . $tagihan->sisa_tagihan,
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');

            // Cek apakah sudah ada pembayaran untuk tagihan ini
            $existingPayment = $tagihan->pembayaran()->first();

            if ($existingPayment) {
                // Update pembayaran yang sudah ada
                $existingPayment->update([
                    'metode' => $request->metode,
                    'bukti' => $buktiPath,
                    'jumlah' => $request->jumlah,
                    'status' => 'pending',
                    'keterangan' => $request->keterangan,
                    'jenis_bayar' => 'cicilan', // sementara cicilan, nanti admin tentukan
                    'tanggal_upload' => now(),
                    'tanggal_bayar' => now(),
                    'alasan_reject' => null, // reset alasan reject
                    'admin_id' => null // reset admin
                ]);
                \Log::info('Pembayaran diupdate: ' . $existingPayment->id);
            } else {
                // Buat baru kalau belum ada
                $pembayaran = Pembayaran::create([
                    'tagihan_id' => $tagihan->id,
                    'user_id' => auth()->id(),
                    'metode' => $request->metode,
                    'bukti' => $buktiPath,
                    'jumlah' => $request->jumlah,
                    'status' => 'pending',
                    'keterangan' => $request->keterangan,
                    'jenis_bayar' => 'cicilan',
                    'tanggal_upload' => now(),
                    'tanggal_bayar' => now()
                ]);
                \Log::info('Pembayaran dibuat baru: ' . $pembayaran->id);
            }

            // Update status tagihan
            $tagihan->update(['status' => 'pending']);

            DB::commit();

            return redirect()->route('murid.tagihan.index')
                ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal upload bukti: ' . $e->getMessage());
        }
    }

    private function handleUploadBuktiSpp(Request $request, $virtualId)
    {
        $request->validate([
            'metode' => 'required|string',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'jumlah' => 'required|numeric|min:1000',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            // Extract ID asli
            $sppId = str_replace('spp_', '', $virtualId);
            $pembayaranSpp = Pembayaran::findOrFail($sppId);

            // Validasi kepemilikan
            if ($pembayaranSpp->user_id != auth()->id()) {
                return back()->with('error', 'Akses ditolak.');
            }

            // Hitung sisa tagihan SPP
            $sppSetting = SppSetting::latest()->first();
            $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;
            $jumlahBulan = ($pembayaranSpp->bulan_akhir - $pembayaranSpp->bulan_mulai) + 1;
            $totalTagihan = $nominalSpp * $jumlahBulan;
            
            // Hitung total sudah dibayar (exclude pembayaran saat ini)
            $totalDibayar = Pembayaran::where('user_id', auth()->id())
                ->whereNull('tagihan_id')
                ->where('tahun', $pembayaranSpp->tahun)
                ->where('bulan_mulai', $pembayaranSpp->bulan_mulai)
                ->where('bulan_akhir', $pembayaranSpp->bulan_akhir)
                ->where('status', 'accepted')
                ->where('id', '!=', $pembayaranSpp->id) // exclude current
                ->sum('jumlah');

            $sisaTagihan = $totalTagihan - $totalDibayar;

            if ($request->jumlah > $sisaTagihan) {
                return back()->with('error', 'Jumlah bayar melebihi sisa tagihan. Sisa: Rp ' . number_format($sisaTagihan, 0, ',', '.'));
            }

            $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');

            // Tentukan jenis bayar
            $akanLunas = ($totalDibayar + $request->jumlah) >= $totalTagihan;
            $jenisBayar = $akanLunas ? 'lunas' : 'cicilan';

            // Update data yang sudah ada, jangan buat baru
            $pembayaranSpp->update([
                'metode' => $request->metode,
                'bukti' => $buktiPath,
                'jumlah' => $request->jumlah,
                'status' => 'pending',
                'keterangan' => $request->keterangan,
                'jenis_bayar' => $jenisBayar,
                'tanggal_upload' => now(),
                'tanggal_bayar' => now(),
                'alasan_reject' => null,
                'admin_id' => null
            ]);

            \Log::info('SPP diupdate: ' . $pembayaranSpp->id);

            return redirect()->route('murid.tagihan.index')
                ->with('success', 'Bukti pembayaran SPP berhasil diupload. Menunggu verifikasi admin.');

        } catch (Exception $e) {
            \Log::error('Error upload bukti SPP: ' . $e->getMessage());
            return back()->with('error', 'Gagal upload bukti SPP: ' . $e->getMessage());
        }
    }

    public function uploadBuktiSpp(Request $request)
    {
        $request->validate([
            'spp_id' => 'required',
            'metode' => 'required|string',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'jumlah' => 'required|numeric|min:1000',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            // Extract ID asli dari virtual ID (spp_38 -> 38)
            $virtualId = $request->spp_id;
            $sppId = str_replace('spp_', '', $virtualId);
            $pembayaranSppAsli = Pembayaran::findOrFail($sppId);

            // Validasi kepemilikan
            if ($pembayaranSppAsli->user_id != auth()->id()) {
                return back()->with('error', 'Akses ditolak.');
            }

            // Cek apakah ini pembayaran yang sama periode
            $pendingSppPayment = Pembayaran::where('user_id', auth()->id())
                ->whereNull('tagihan_id')
                ->where('tahun', $pembayaranSppAsli->tahun)
                ->where('bulan_mulai', $pembayaranSppAsli->bulan_mulai)
                ->where('bulan_akhir', $pembayaranSppAsli->bulan_akhir)
                ->where('status', 'pending')
                ->first();
                
            if ($pendingSppPayment) {
                return back()->with('error', 'Masih ada pembayaran SPP yang menunggu verifikasi. Tunggu sampai pembayaran sebelumnya diproses admin.');
            }

            // Jika ada yang ditolak, update yang ditolak jadi pending
            $rejectedSppPayment = Pembayaran::where('user_id', auth()->id())
                ->whereNull('tagihan_id')
                ->where('tahun', $pembayaranSppAsli->tahun)
                ->where('bulan_mulai', $pembayaranSppAsli->bulan_mulai)
                ->where('bulan_akhir', $pembayaranSppAsli->bulan_akhir)
                ->where('status', 'rejected')
                ->first();

            if ($rejectedSppPayment) {
                // Update yang ditolak jadi pending dengan data baru
                $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');
                
                $rejectedSppPayment->update([
                    'metode' => $request->metode,
                    'bukti' => $buktiPath,
                    'jumlah' => $request->jumlah,
                    'status' => 'pending',
                    'keterangan' => $request->keterangan,
                    'jenis_bayar' => $rejectedSppPayment->jenis_bayar, // Pertahankan jenis bayar
                    'tanggal_upload' => now(),
                    'tanggal_bayar' => now(),
                    'alasan_reject' => null, // Hapus alasan reject
                    'admin_id' => null // Reset admin
                ]);

                \Log::info('SPP ditolak diupdate menjadi pending: ' . $rejectedSppPayment->id);

                return redirect()->route('murid.tagihan.index')
                    ->with('success', 'Bukti pembayaran SPP berhasil diupload ulang. Menunggu verifikasi admin.');
            }

            // Hitung sisa tagihan SPP
            $sppSetting = SppSetting::latest()->first();
            $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;
            $jumlahBulan = ($pembayaranSppAsli->bulan_akhir - $pembayaranSppAsli->bulan_mulai) + 1;
            $totalTagihan = $nominalSpp * $jumlahBulan;
            
            // Hitung total sudah dibayar (pembayaran SPP yang accepted)
            $totalDibayar = Pembayaran::where('user_id', auth()->id())
                ->whereNull('tagihan_id')
                ->where('tahun', $pembayaranSppAsli->tahun)
                ->where('bulan_mulai', $pembayaranSppAsli->bulan_mulai)
                ->where('bulan_akhir', $pembayaranSppAsli->bulan_akhir)
                ->where('status', 'accepted')
                ->sum('jumlah');

            $sisaTagihan = $totalTagihan - $totalDibayar;

            // Validasi jumlah bayar
            if ($request->jumlah > $sisaTagihan) {
                return back()->with('error', 'Jumlah bayar melebihi sisa tagihan. Sisa: Rp ' . number_format($sisaTagihan, 0, ',', '.'));
            }

            $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');

            // Tentukan jenis bayar
            $akanLunas = ($totalDibayar + $request->jumlah) >= $totalTagihan;
            $jenisBayar = $akanLunas ? 'lunas' : 'cicilan';

            // Buat pembayaran SPP baru hanya jika tidak ada yang ditolak
            $pembayaranBaru = Pembayaran::create([
                'user_id' => auth()->id(),
                'metode' => $request->metode,
                'bukti' => $buktiPath,
                'jumlah' => $request->jumlah,
                'status' => 'pending',
                'keterangan' => $request->keterangan,
                'jenis_bayar' => $jenisBayar,
                'tanggal_upload' => now(),
                'tanggal_bayar' => now(),
                'tahun' => $pembayaranSppAsli->tahun,
                'bulan_mulai' => $pembayaranSppAsli->bulan_mulai,
                'bulan_akhir' => $pembayaranSppAsli->bulan_akhir
            ]);

            return redirect()->route('murid.tagihan.index')
                ->with('success', 'Bukti pembayaran SPP berhasil diupload. Menunggu verifikasi admin.');

        } catch (Exception $e) {
            \Log::error('Error upload bukti SPP: ' . $e->getMessage());
            return back()->with('error', 'Gagal upload bukti SPP: ' . $e->getMessage());
        }
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