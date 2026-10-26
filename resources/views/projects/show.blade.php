<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Proyek - GarudaHub</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
        }

        .navbar {
            background: #1e40af;
            color: white;
            padding: 20px 40px;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px #00000015;
        }

        h1 {
            color: #1e40af;
        }

        .info {
            margin: 18px 0;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 18px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

        .btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>

<div class="navbar">
    <strong>GarudaHub - Lelang Proyek IT</strong>
</div>

<div class="container">

    <h1>{{ $project->judul }}</h1>

    <p>Informasi lengkap proyek yang tersedia di GarudaHub.</p>

    <hr>

    <div class="info">
        <strong>Nama Klien:</strong>
        {{ $project->client?->nama ?? 'Tidak diketahui' }}
    </div>

    <div class="info">
        <strong>Kategori:</strong>
        {{ $project->kategori }}
    </div>

    <div class="info">
        <strong>Deskripsi Proyek:</strong>
        <p>{{ $project->deskripsi }}</p>
    </div>

    <div class="info">
        <strong>Budget Minimal:</strong>
        Rp{{ number_format($project->budget_min, 0, ',', '.') }}
    </div>

    <div class="info">
        <strong>Budget Maksimal:</strong>
        Rp{{ number_format($project->budget_max, 0, ',', '.') }}
    </div>

    <div class="info">
        <strong>Batas Lelang:</strong>
        {{ $project->batas_lelang }}
    </div>

    <div class="info">
        <strong>Status:</strong>
        {{ ucfirst($project->status) }}
    </div>



@if(auth()->user()->role === 'klien'
    && (int) $project->client_id === auth()->id())

    <a href="{{ route('projects.edit', $project) }}"
       class="btn">
        Edit Proyek
    </a>

    <form action="{{ route('projects.destroy', $project) }}"
          method="POST"
          style="display: inline-block; margin-left: 8px;"
          onsubmit="return confirm('Yakin ingin menghapus proyek ini?');">

        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn"
                style="background: #dc2626; border: none; cursor: pointer;">
            Hapus Proyek
        </button>

    </form>

@endif


{{-- Notifikasi penawaran --}}
@if(session('success'))
    <p style="background:#d1fae5; padding:12px;">
        {{ session('success') }}
    </p>
@endif

@if(session('error'))
    <p style="background:#fee2e2; padding:12px;">
        {{ session('error') }}
    </p>
@endif

{{-- Form khusus Developer --}}
@if(auth()->user()->role === 'developer'
    && $project->status === 'terbuka'
    && \Illuminate\Support\Carbon::parse($project->batas_lelang)->isFuture())

    <hr>

    <h2>Ajukan Penawaran</h2>
    <p>Isi penawaran untuk mengerjakan proyek ini.</p>

    <form action="{{ route('bids.store', $project) }}"
          method="POST">

        @csrf

        <div class="info">
            <label>Harga Penawaran (Rp)</label>
            <input type="number"
                   name="harga"
                   value="{{ old('harga') }}"
                   min="1"
                   required
                   style="width:100%; padding:10px; box-sizing:border-box;">
            @error('harga')
                <p style="color:red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="info">
            <label>Durasi Pengerjaan (Hari)</label>
            <input type="number"
                   name="durasi"
                   value="{{ old('durasi') }}"
                   min="1"
                   required
                   style="width:100%; padding:10px; box-sizing:border-box;">
            @error('durasi')
                <p style="color:red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="info">
            <label>Proposal Penawaran</label>
            <textarea name="proposal"
                      minlength="10"
                      maxlength="3000"
                      required
                      style="width:100%; padding:10px; height:110px; box-sizing:border-box;">{{ old('proposal') }}</textarea>
            @error('proposal')
                <p style="color:red;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                style="background:#2563eb; color:white;
                       padding:12px 18px; border:none;
                       border-radius:5px; cursor:pointer;">
            Kirim Penawaran
        </button>

    </form>

@endif


    <a href="{{ route('projects.index') }}" class="btn">
        Kembali ke Daftar Proyek
    </a>

</div>

</body>
</html>
