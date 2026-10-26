<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GarudaHub</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 380px;
            max-width: 85%;
            box-shadow: 0 4px 15px #00000015;
        }

        h1 {
            color: #1e40af;
            text-align: center;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>GarudaHub</h1>
    <p>Buat akun baru</p>

    <form action="{{ route('register.store') }}" method="POST">
        @csrf

        <label>Nama Lengkap</label>
        <input type="text" name="nama"
               value="{{ old('nama') }}"
               placeholder="Masukkan nama lengkap" required>
        @error('nama')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Email</label>
        <input type="email" name="email"
               value="{{ old('email') }}"
               placeholder="Masukkan email" required>
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Daftar Sebagai</label>
        <select name="role" required>
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

        <label>Password</label>
        <input type="password" name="password"
               placeholder="Minimal 8 karakter" required>
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation"
               placeholder="Ulangi password" required>

        <button type="submit">Daftar Sekarang</button>
    </form>

    <p>
        Sudah punya akun?
        <a href="{{ route('login') }}">Login di sini</a>
    </p>
</div>

</body>
</html>
