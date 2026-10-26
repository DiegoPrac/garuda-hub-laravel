@extends('layout')
@section('title', auth()->check() ? 'Dashboard' : 'Beranda')
@section('content')
    @auth
        <div class="page-heading">
            <div>
                <div class="eyebrow">Dashboard / {{ ucfirst(auth()->user()->role) }}</div>
                <h1>Selamat Datang, {{ auth()->user()->nama }}!</h1>
                <p>Siap membuat langkah berikutnya bersama GarudaHub?</p>
            </div>
            <span class="badge">{{ auth()->user()->email }}</span>
        </div>
        <section class="hero">
            <div>
                <div class="eyebrow">Ruang untuk karya berikutnya</div>
                @if(auth()->user()->role === 'klien')
                    <h2>Ide Anda layak diwujudkan.</h2>
                    <p>Publikasikan kebutuhan proyek, bandingkan penawaran, dan pilih developer yang tepat.</p>
                    <div class="actions">
                        <a class="btn" href="{{ route('projects.create') }}">+ Tambah Proyek</a>
                        <a class="btn secondary" href="{{ route('projects.index') }}">Lihat Daftar Proyek</a>
                    </div>
                @else
                    <h2>Keahlian Anda, peluang baru.</h2>
                    <p>Temukan proyek yang sesuai, susun proposal terbaik, dan mulai kolaborasi dengan klien.</p>
                    <div class="actions">
                        <a class="btn" href="{{ route('projects.index') }}">Lihat Daftar Proyek <span aria-hidden="true">→</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="hero-art">
                <div class="mini-card">
                    <span>WORKSPACE ANDA</span>
                    <strong>{{ ucfirst(auth()->user()->role) }}</strong>
                    <span>{{ auth()->user()->role === 'klien' ? 'Kelola ide dan temukan mitra pengembang.' : 'Jelajahi peluang dan tunjukkan keahlian Anda.' }}</span>
                </div>
            </div>
        </section>
    @else
        <section class="hero">
            <div>
                <div class="eyebrow">Marketplace proyek IT</div>
                <h1>Ide besar.<br>Talenta tepat.<br>Karya berdampak.</h1>
                <p>GarudaHub mempertemukan klien dan developer untuk mewujudkan proyek digital, satu kolaborasi pada satu waktu.</p>
                <div class="actions">
                    <a class="btn" href="{{ route('register') }}">Mulai Berkolaborasi <span aria-hidden="true">→</span>
                    </a>
                    <a class="btn secondary" href="{{ route('login') }}">Login</a>
                </div>
            </div>
            <div class="hero-art">
                <div class="mini-card">
                    <span>UNTUK KLIEN</span>
                    <strong>Bangun ide menjadi solusi.</strong>
                    <span>Publikasikan proyek dan pilih penawaran terbaik.</span>
                </div>
                <div class="mini-card">
                    <span>UNTUK DEVELOPER</span>
                    <strong>Ubah keahlian menjadi peluang.</strong>
                    <span>Temukan proyek dan kirim proposal Anda.</span>
                </div>
            </div>
        </section>
    @endauth
    <div class="page-heading">
        <div>
            <div class="eyebrow">Dari ide ke kolaborasi</div>
            <h2>Tiga langkah untuk memulai.</h2>
        </div>
    </div>
    <div class="steps">
        <article class="card">
            <span class="step-number">01</span>
            <h3>Temukan kecocokan</h3>
            <p>Klien menjelaskan kebutuhan. Developer menjelajahi proyek sesuai keahlian.</p>
            <a href="{{ route('projects.index') }}">Jelajahi proyek →</a>
        </article>
        <article class="card">
            <span class="step-number">02</span>
            <h3>Diskusikan melalui proposal</h3>
            <p>Developer mengirim harga, durasi, dan pendekatan pengerjaan melalui penawaran.</p>
        </article>
        <article class="card">
            <span class="step-number">03</span>
            <h3>Pilih mitra yang tepat</h3>
            <p>Klien membandingkan penawaran dan menentukan developer pemenang proyek.</p>
        </article>
    </div>
@endsection
