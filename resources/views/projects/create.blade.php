<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Proyek - GarudaHub</title>

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
            max-width: 650px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px #00000015;
        }

        h1 {
            color: #1e40af;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 110px;
        }

        button, .btn {
            display: inline-block;
            padding: 12px 18px;
            margin-top: 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn {
            background: #6b7280;
        }

        .error {
            color: #dc2626;
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="navbar">
    <strong>GarudaHub - Lelang Proyek IT</strong>
</div>

<div class="container">

    <h1>Tambah Proyek Baru</h1>
    <p>Isi informasi proyek yang ingin dilelang.</p>

    <form action="{{ route('projects.store') }}" method="POST">
        @csrf

        <label>Judul Proyek</label>
        <input type="text" name="judul"
               value="{{ old('judul') }}"
               placeholder="Contoh: Pembuatan Website Toko Online"
               required>
        @error('judul')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Deskripsi Proyek</label>
        <textarea name="deskripsi"
                  placeholder="Jelaskan kebutuhan proyek"
                  required>{{ old('deskripsi') }}</textarea>
        @error('deskripsi')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Kategori</label>
        <input type="text" name="kategori"
               value="{{ old('kategori') }}"
               placeholder="Contoh: Web Development"
               required>
        @error('kategori')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Budget Minimal (Rp)</label>
        <input type="number" name="budget_min"
               value="{{ old('budget_min') }}"
               min="0"
               placeholder="Contoh: 500000"
               required>
        @error('budget_min')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Budget Maksimal (Rp)</label>
        <input type="number" name="budget_max"
               value="{{ old('budget_max') }}"
               min="0"
               placeholder="Contoh: 1500000"
               required>
        @error('budget_max')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Batas Lelang</label>
        <input type="date" name="batas_lelang"
               value="{{ old('batas_lelang') }}"
               min="{{ now()->toDateString() }}"
               required>
        @error('batas_lelang')
            <div class="error">{{ $message }}</div>
        @enderror

        <br>

        <button type="submit">Simpan Proyek</button>

        <a href="{{ route('projects.index') }}" class="btn">
            Batal
        </a>
    </form>

</div>

</body>
</html>
