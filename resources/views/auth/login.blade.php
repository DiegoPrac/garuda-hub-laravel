@extends('layout')
@section('title', 'Login')
@section('content')
    <div class="auth-grid">
        <aside class="auth-story">
            <div class="eyebrow">Ide. Keahlian. Kolaborasi.</div>
            <h2>Proyek hebat dimulai dari koneksi yang tepat.</h2>
            <p>Temukan developer untuk ide Anda, atau bawa keahlian Anda ke proyek berikutnya.</p>
            <p>01 &nbsp; Temukan peluang<br>02 &nbsp; Ajukan penawaran<br>03 &nbsp; Mulai kolaborasi</p>
        </aside>
        <section class="auth-form">
            <div class="eyebrow">Workspace Anda</div>
            <h1>Selamat datang kembali.</h1>
            <p>Masuk ke akun Anda</p>
            <form action="{{ route('login.attempt') }}" method="POST">
                @csrf

                <label for="email">Email</label>
                <input id="email" type="email" autocomplete="email" name="email"
                value="{{ old('email') }}"
                placeholder="Masukkan email" required>

                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="password">Password</label>
                <input id="password" type="password" autocomplete="current-password" name="password"
                placeholder="Masukkan password" required>

                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror

                <button type="submit">Login</button>
            </form>
            <p>
                Belum punya akun?
                <a href="{{ route('register') }}">Daftar sekarang</a>
            </p>
        </section>
    </div>
@endsection
