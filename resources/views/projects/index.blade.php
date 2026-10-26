<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Daftar Proyek - GarudaHub</title>

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
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        h1 {
            color: #1e40af;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px #00000010;
        }

        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .success {
            background: #d1fae5;
            padding: 15px;
            margin-bottom: 20px;
        }

        .empty {
            text-align: center;
            color: #6b7280;
        }
    </style>
</head>

<body>

<div class="navbar">
    <strong>GarudaHub - Lelang Proyek IT</strong>
</div>

<div class="container">

    <h1>Daftar Proyek</h1>

    <p>
        Temukan proyek IT yang tersedia
        di GarudaHub.
    </p>

    <a href="{{ route('home') }}" class="btn">
        Kembali ke Beranda
    </a>

    @if(auth()->user()->role === 'klien')
        <a href="{{ route('projects.create') }}" class="btn">
            + Tambah Proyek
        </a>
    @endif

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @forelse($projects as $project)

        <div class="card">

            <h2>{{ $project->judul }}</h2>

            <p>
                <strong>Klien:</strong>
                {{ $project->client?->nama ?? 'Tidak diketahui' }}
            </p>

            <p>
                <strong>Kategori:</strong>
                {{ $project->kategori }}
            </p>

            <p>
                <strong>Deskripsi:</strong>
                {{ $project->deskripsi }}
            </p>

            <p>
                <strong>Budget:</strong>
                Rp{{ number_format($project->budget_min, 0, ',', '.') }}
                -
                Rp{{ number_format($project->budget_max, 0, ',', '.') }}
            </p>

            <p>
                <strong>Status:</strong>
                {{ ucfirst($project->status) }}
            </p>

<a href="{{ route('projects.show', $project) }}" class="btn">
    Lihat Detail
</a>

        </div>

    @empty

        <div class="card empty">
            <h3>Belum Ada Proyek</h3>

            <p>
                Saat ini belum ada proyek yang
                ditambahkan oleh klien.
            </p>
        </div>

    @endforelse

    {{ $projects->links() }}

</div>

</body>
</html>
