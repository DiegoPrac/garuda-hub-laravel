@extends('layout')
@section('title', 'Registrasi')
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
            <h1>Mulai langkah pertama.</h1>
            <p>Buat akun baru</p>
            <form action="{{ route('register.store') }}" method="POST">
                @csrf

                <label for="nama">Nama Lengkap</label>
                <input id="nama" type="text" autocomplete="name" name="nama"
                value="{{ old('nama') }}"
                placeholder="Masukkan nama lengkap" required>
                @error('nama')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="email">Email</label>
                <input id="email" type="email" autocomplete="email" name="email"
                value="{{ old('email') }}"
                placeholder="Masukkan email" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="role">Daftar Sebagai</label>
                <small>Klien mencari talenta. Developer menawarkan keahlian.</small>
                <select id="role" name="role" required>
                    <option value="">Pilih peran</option>
                    <option value="klien" {{ old('role') == 'klien' ? 'selected' : '' }}>
                    Klien
                    </option>
                    <option value="developer" {{ old('role') == 'developer' ? 'selected' : '' }}>
                    Developer
                    </option>
                </select>
                @error('role')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="password">Password</label>
                <input id="password" type="password" autocomplete="new-password" name="password"
                placeholder="Minimal 8 karakter" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" autocomplete="new-password" name="password_confirmation"
                placeholder="Ulangi password" required>
                <button type="submit">Daftar Sekarang</button>
            </form>
            <p>
                Sudah punya akun?
                <a href="{{ route('login') }}">Login di sini</a>
            </p>
        </section>
    </div>
@endsection
