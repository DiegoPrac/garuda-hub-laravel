@extends('layout')
@section('title', 'Daftar Proyek')
@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow">Marketplace / Proyek IT</div>
            <h1>Temukan peluang berikutnya.</h1>
            <p>Daftar Proyek · Jelajahi kebutuhan digital dan mulai kolaborasi baru.</p>
        </div>
        @if(auth()->user()->role === 'klien')<a href="{{ route('projects.create') }}" class="btn">+ Tambah Proyek</a>@endif
    </div>
    <div class="card-top section-space">
        <h2>Semua proyek <span class="badge">{{ $projects->total() }}</span>
        </h2>
        <p class="meta">Proyek terbaru ditampilkan lebih dulu</p>
    </div>
    <div class="cards">
        @forelse($projects as $project)
            <article class="card project-card">
                <div class="card-top">
                    <span class="badge">{{ $project->kategori }}</span>
                    <span class="badge {{ $project->status === 'terbuka' && $project->batas_lelang->isFuture() ? 'open' : 'closed' }}">{{ $project->status === 'terbuka' && !$project->batas_lelang->isFuture() ? 'Lelang berakhir' : ucfirst($project->status) }}</span>
                </div>
                <h2>
                    <a href="{{ route('projects.show', $project) }}">{{ $project->judul }}</a>
                </h2>
                <div class="meta">Oleh {{ $project->client?->nama ?? 'Tidak diketahui' }} @if($project->client_id === auth()->id()) · <strong>Proyek Anda</strong>@endif
                </div>
                <p class="description">{{ $project->deskripsi }}</p>
                <p class="meta">Batas lelang · {{ $project->batas_lelang->format('d M Y') }} · 00.00</p>
                <div class="card-bottom">
                    <div>
                        <small>Kisaran budget</small>
                        <strong>Rp{{ number_format($project->budget_min, 0, ',', '.') }} – Rp{{ number_format($project->budget_max, 0, ',', '.') }}</strong>
                    </div>
                    <a class="btn secondary" href="{{ route('projects.show', $project) }}" aria-label="Lihat detail {{ $project->judul }}">Lihat Detail →</a>
                </div>
            </article>
        @empty
            <div class="card empty">
                <div class="empty-symbol" aria-hidden="true">◇</div>
                <h2>Belum Ada Proyek</h2>
                <p>Saat ini belum ada proyek yang ditambahkan oleh klien.</p>@if(auth()->user()->role === 'klien')<a class="btn" href="{{ route('projects.create') }}">Buat proyek pertama</a>@else
                    <p>Kunjungi kembali untuk menemukan peluang baru.</p>@endif
            </div>
        @endforelse
    </div>
    @if($projects->hasPages())
        <nav class="pagination" aria-label="Halaman daftar proyek">
            @if($projects->onFirstPage())<span class="disabled">← Sebelumnya</span>@else
                <a class="btn secondary" href="{{ $projects->previousPageUrl() }}">← Sebelumnya</a>@endif
            <span class="meta">Halaman {{ $projects->currentPage() }} dari {{ $projects->lastPage() }}</span>
            @if($projects->hasMorePages())<a class="btn secondary" href="{{ $projects->nextPageUrl() }}">Berikutnya →</a>@else
                <span class="disabled">Berikutnya →</span>@endif
        </nav>
    @endif
@endsection
