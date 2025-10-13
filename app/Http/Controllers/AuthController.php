<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('murid.dashboard');
            }
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Debug: Cek input
        \Log::info('Login attempt:', $request->all());

        // Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // Coba login dengan username
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Cek jika user aktif
            if (!$user->aktif) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
                ]);
            }
            
            \Log::info('Login successful for user: ' . $user->username);

            // Redirect berdasarkan role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'Login berhasil! Selamat datang, ' . $user->nama);
            } else {
                return redirect()->route('murid.dashboard')->with('success', 'Login berhasil! Selamat datang, ' . $user->nama);
            }
        }

        \Log::warning('Login failed for username: ' . $credentials['username']);

        // Jika login gagal
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Logout berhasil!');
    }
}