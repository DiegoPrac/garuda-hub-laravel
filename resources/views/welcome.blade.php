<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Beranda - GarudaHub</title>

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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            max-width: 800px;
            margin: 60px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px #00000015;
        }

        h1 {
            color: #1e40af;
        }

        button, .btn {
            background: #2563eb;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        button:hover, .btn:hover {
            background: #1d4ed8;
        }

        .logout {
            background: #dc2626;
        }

        .logout:hover {
            background: #b91c1c;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <strong>GarudaHub</strong>

        @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout">
                    Logout
                </button>
            </form>
        @endauth
    </div>

    <div class="container">

        @auth
            <h1>Selamat Datang, {{ auth()->user()->nama }}!</h1>

            <p>
                Anda berhasil masuk ke GarudaHub.
            </p>

            <p>
                <strong>Email:</strong>
                {{ auth()->user()->email }}
            </p>

            <p>
                <strong>Role:</strong>
                {{ ucfirst(auth()->user()->role) }}
            </p>

            <hr>

            <h2>GarudaHub - Lelang Proyek IT</h2>

            <p>
                Platform untuk mempertemukan klien
                dan developer dalam pengerjaan proyek IT.
            </p>


<p>
    Silakan lihat daftar proyek IT yang tersedia
    dan ajukan penawaran sesuai keahlianmu.
</p>

<a href="{{ route('projects.index') }}"
   style="display:inline-block;
          background:#2563eb;
          color:white;
          padding:12px 20px;
          border-radius:5px;
          text-decoration:none;
          margin-top:10px;">
    Lihat Daftar Proyek
</a>

        @else
            <h1>Selamat Datang di GarudaHub</h1>

            <p>
                Platform lelang proyek IT untuk
                klien dan developer.
            </p>

            <a href="{{ route('login') }}" class="btn">
                Login
            </a>

            <a href="{{ route('register') }}" class="btn">
                Register
            </a>
        @endauth

    </div>

</body>
</html>
