<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// ====================================================
// PWEB - EPISODE 2: THE PHANTOM VULNERABILITY
// ====================================================
// Nama      : Diego Valentino Augusta
// NIM       : 252410102108
// Kelompok  : 5
// Mata Kuliah: Pemrograman Berbasis Web (KSTI407)
// Fakultas  : Ilmu Komputer
// Universitas: Universitas Jember
//
// Fokus Audit Keamanan:
// 1. Authentication dan Password Hashing
// 2. Session Management
// 3. CSRF Protection
// 4. SQL Injection Prevention
// 5. Cross-Site Scripting (XSS) Prevention
//
// Catatan:
// Komentar ini merupakan identitas pengerjaan
// Episode 2 untuk dokumentasi dan bukti OWASP ZAP.
// ====================================================

class AuthController extends Controller
{
    // Menampilkan halaman registrasi
    public function showRegister(): View
    {
        return view('auth.register');
    }

    // Menyimpan pengguna baru
    public function register(Request $request): RedirectResponse
    {
        // Validasi data registrasi pengguna
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:klien,developer',
        ]);

        // Membuat pengguna baru
        // Password harus di-hash melalui cast pada model User
        $user = new User([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        // Menetapkan role yang sudah divalidasi
        $user->role = $data['role'];
        $user->save();

        // Login otomatis setelah registrasi
        Auth::login($user);

        // Regenerasi session untuk mencegah session fixation
        $request->session()->regenerate();

        return redirect('/');
    }

    // Menampilkan halaman login
    public function showLogin(): View
    {
        return view('auth.login');
    }

    // Memproses login pengguna
    public function login(Request $request): RedirectResponse
    {
        // Validasi input email dan password
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Autentikasi menggunakan Laravel Auth
        if (Auth::attempt($credentials)) {

            // Membuat session ID baru setelah login berhasil
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        // Pesan kesalahan tanpa mengungkap akun terdaftar
        return back()
            ->withErrors([
                'email' => 'Email atau password salah.',
            ])
            ->onlyInput('email');
    }

    // Mengeluarkan pengguna dari aplikasi
    public function logout(Request $request): RedirectResponse
    {
        // Mengakhiri autentikasi pengguna
        Auth::logout();

        // Menghapus data session lama
        $request->session()->invalidate();

        // Membuat CSRF token baru
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
