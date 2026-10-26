@extends('layout')
@section('title', 'Tambah Proyek')
@section('content')
    <div class="breadcrumb">
        <a href="{{ route('projects.index') }}">Jelajahi Proyek</a> / Tambah proyek</div>
    <div class="page-heading">
        <div>
            <div class="eyebrow">Workspace klien</div>
            <h1>Wujudkan ide Anda.</h1>
            <p>Tambah Proyek Baru · Berikan informasi yang membantu developer memahami kebutuhan Anda.</p>
        </div>
    </div>
    <div class="form-layout">
        <section class="panel">
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf

                <label for="judul">Judul Proyek</label>
                <input id="judul" type="text" name="judul"
                value="{{ old('judul') }}"
                placeholder="Contoh: Pembuatan Website Toko Online"
                required>
                @error('judul')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="deskripsi">Deskripsi Proyek</label>
                <textarea id="deskripsi" name="deskripsi"
                placeholder="Jelaskan kebutuhan proyek"
                required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="kategori">Kategori</label>
                <input id="kategori" type="text" name="kategori"
                value="{{ old('kategori') }}"
                placeholder="Contoh: Web Development"
                required>
                @error('kategori')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="budget_min">Budget Minimal (Rp)</label>
                <input id="budget_min" type="number" name="budget_min"
                value="{{ old('budget_min') }}"
                min="0"
                placeholder="Contoh: 500000"
                required>
                @error('budget_min')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="budget_max">Budget Maksimal (Rp)</label>
                <input id="budget_max" type="number" name="budget_max"
                value="{{ old('budget_max') }}"
                min="0"
                placeholder="Contoh: 1500000"
                required>
                @error('budget_max')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="batas_lelang">Batas Lelang</label>
                <input id="batas_lelang" type="date" name="batas_lelang"
                value="{{ old('batas_lelang') }}"
                min="{{ now()->toDateString() }}"
                required>
                @error('batas_lelang')
                    <div class="error">{{ $message }}</div>
                @enderror



                <div class="actions">
                    <button type="submit">Simpan Proyek</button>
                    <a href="{{ route('projects.index') }}" class="btn secondary">
                        Batal
                    </a>
                </div>
            </form>
        </section>
        <aside class="guide">
            <div class="eyebrow">Panduan proyek</div>
            <h2>Brief jelas, penawaran lebih tepat.</h2>
            <p>
                <strong>01 · Tentukan hasil</strong>
                <br>Jelaskan fitur, tujuan, dan hasil akhir yang Anda harapkan.</p>
            <p>
                <strong>02 · Siapkan anggaran</strong>
                <br>Isi kisaran budget sesuai lingkup pekerjaan.</p>
            <p>
                <strong>03 · Tetapkan batas lelang</strong>
                <br>Penawaran ditutup saat memasuki tanggal yang dipilih (00.00 waktu aplikasi).</p>
        </aside>
    </div>
@endsection
