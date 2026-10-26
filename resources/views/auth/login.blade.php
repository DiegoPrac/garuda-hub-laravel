<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GarudaHub</title>

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
            width: 350px;
            max-width: 85%;
            box-shadow: 0 4px 15px #00000015;
        }

        h1 {
            color: #1e40af;
            text-align: center;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            color: white;
            background: #2563eb;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        p {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>GarudaHub</h1>
    <p>Masuk ke akun Anda</p>

    <form action="{{ route('login.attempt') }}" method="POST">
        @csrf

        <label>Email</label>
        <input type="email" name="email"
               value="{{ old('email') }}"
               placeholder="Masukkan email" required>

        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Password</label>
        <input type="password" name="password"
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
</div>

</body>
</html>
