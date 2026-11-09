<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Workspace') · GarudaHub</title>
        <link rel="stylesheet" href="{{ asset('css/garudahub.css') }}">
        <script src="{{ asset('js/confirm-actions.js') }}" defer></script>
    </head>
    <body>
        <a class="skip-link" href="#main">Lewati navigasi</a>
        <header class="site-header">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-mark" aria-hidden="true">G</span>Garuda<span>Hub</span>
            </a>
            <nav aria-label="Navigasi utama">
                @auth
                    <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Dashboard</a>
                    <a href="{{ route('projects.index') }}" @if(request()->routeIs('projects.index', 'projects.show')) aria-current="page" @endif>Jelajahi Proyek</a>
                    @if(auth()->user()->role === 'klien')
                        <a href="{{ route('projects.create') }}" @if(request()->routeIs('projects.create')) aria-current="page" @endif>+ Tambah Proyek</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>Login</a>
                    <a href="{{ route('register') }}" @if(request()->routeIs('register')) aria-current="page" @endif>Daftar Sekarang</a>
                @endauth
            </nav>
            @auth
                <div class="account">
                    <span class="badge">{{ ucfirst(auth()->user()->role) }}</span>
                    <form action="{{ route('logout') }}" method="POST">@csrf<button class="btn subtle" type="submit">Logout</button>
                    </form>
                </div>
            @endauth
        </header>
        <main id="main" class="workspace" tabindex="-1">
            @if(session('success'))<div class="notice success" role="status">{{ session('success') }}</div>
            @endif
            @if(session('error'))<div class="notice error" role="alert">{{ session('error') }}</div>
            @endif
            @if($errors->any())<div class="notice error" role="alert">
                    <strong>Periksa kembali isian Anda.</strong>
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
        <footer class="site-footer">
            <span>GarudaHub / Ruang kolaborasi digital.</span>
            <span>Ide bertemu keahlian.</span>
        </footer>
    </body>
</html>
