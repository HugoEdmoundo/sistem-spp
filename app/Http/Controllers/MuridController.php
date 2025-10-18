<?php
namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\SppSetting;
use App\Events\PembayaranDibuat;
use App\Mail\PembayaranNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class MuridController extends Controller
{
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

        // PERBAIKAN: Hitung PEMBAYARAN yang rejected, bukan TAGIHAN
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

        return view('murid.dashboard', [
            'totalTagihan' => $totalTagihan,
            'totalDibayar' => $totalDibayar,
            'tagihanPending' => $tagihanPending,
            'tagihanRejected' => $pembayaranRejectedCount, // GUNAKAN COUNT PEMBAYARAN REJECTED
            'pembayaranPending' => $pembayaranPending,
            'pembayaranRejected' => $pembayaranRejected,
            'nominalSpp' => $nominalSpp,
            'tagihan' => $tagihanTerbaru
        ]);
    }
    
    public function tagihanIndex()
    {
        $tagihan = Tagihan::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);
        
        return view('murid.tagihan.index', compact('tagihan'));
    }

    public function showBayarSpp()
    {
        // Ambil nominal SPP saat ini
        $sppSetting = SppSetting::latest()->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;

        return view('murid.bayar-spp', compact('nominalSpp'));
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
        $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');

        // Buat pembayaran tanpa tagihan
        $pembayaran = Pembayaran::create([
            'tagihan_id' => null,
            'user_id' => $user->id,
            'metode' => $request->metode,
            'bukti' => $buktiPath,
            'jumlah' => $request->jumlah,
            'status' => 'pending',
            'keterangan' => $request->keterangan,
            'jenis_bayar' => 'lunas',
            'tanggal_upload' => now()
        ]);

        return redirect()->route('murid.bayar.spp.page')->with('success', 'Pembayaran SPP berhasil diupload. Menunggu verifikasi admin.');
    }

    // public function bayarSpp(Request $request)
    // {
    //     $request->validate([
    //         'bulan_mulai' => 'required|integer|min:1|max:12',
    //         'bulan_akhir' => 'required|integer|min:1|max:12',
    //         'tahun' => 'required|integer',
    //         'jumlah' => 'required|numeric|min:0',
    //         'metode' => 'required|string',
    //         'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    //         'keterangan' => 'required|string'
    //     ]);

    //     // Validasi bulan
    //     if ($request->bulan_mulai > $request->bulan_akhir) {
    //         return back()->withErrors(['bulan_akhir' => 'Bulan akhir harus lebih besar atau sama dengan bulan mulai.']);
    //     }

    //     $user = auth()->user();
    //     $buktiPath = $request->file('bukti')->store('bukti-pembayaran', 'public');

    //     // Buat pembayaran tanpa tagihan
    //     $pembayaran = Pembayaran::create([
    //         'tagihan_id' => null,
    //         'user_id' => $user->id,
    //         'metode' => $request->metode,
    //         'bukti' => $buktiPath,
    //         'jumlah' => $request->jumlah,
    //         'status' => 'pending',
    //         'keterangan' => $request->keterangan,
    //         'jenis_bayar' => 'lunas',
    //         'tanggal_upload' => now()
    //     ]);

    //     // Trigger event dan email - COMMENT DULU UNTUK TEST
    //         // event(new PembayaranDibuat($pembayaran));
            
    //         // Kirim email ke admin - COMMENT DULU UNTUK TEST
    //         // $adminUsers = \App\Models\User::where('role', 'admin')->get();
    //         // foreach ($adminUsers as $admin) {
    //         //     Mail::to($admin->email)->send(new PembayaranNotification($pembayaran));
    //         // }


    //     return back()->with('success', 'Pembayaran SPP berhasil diupload. Menunggu verifikasi admin.');
    // }
     
    
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

        // Trigger event dan email - COMMENT DULU UNTUK TEST
        // event(new PembayaranDibuat($pembayaran));
        
        // Kirim email ke admin - COMMENT DULU UNTUK TEST
        // $adminUsers = \App\Models\User::where('role', 'admin')->get();
        // foreach ($adminUsers as $admin) {
        //     Mail::to($admin->email)->send(new PembayaranNotification($pembayaran));
        // }

    public function profile()
    {
        return view('murid.profile');
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

    public function pembayaranHistory()
    {
        $pembayaran = Pembayaran::where('user_id', auth()->id())
            ->with(['tagihan', 'admin'])
            ->latest()
            ->paginate(15);
        
        return view('murid.pembayaran.history', compact('pembayaran'));
    }
}