@extends('layout')
@section('title', 'Detail Proyek')
@section('content')
    <div class="breadcrumb">
        <a href="{{ route('projects.index') }}">Jelajahi Proyek</a> / Detail proyek</div>
    <div class="detail-layout">
        <section class="panel">
            <div class="eyebrow">Detail proyek / {{ $project->kategori }}</div>
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
                <p class="prose">{{ $project->deskripsi }}</p>
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
                {{ $project->batas_lelang->format('d M Y') }} · 00.00
            </div>
            <div class="info">
                <strong>Status:</strong>
                <span class="badge">{{ ucfirst($project->status) }}</span>
            </div>
            <div class="actions">
                @if(auth()->user()->can('update', $project))

                    <a href="{{ route('projects.edit', $project) }}"
                        class="btn">
                        Edit Proyek
                    </a>
                    <form action="{{ route('projects.destroy', $project) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus proyek ini?');">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="btn danger">
                            Hapus Proyek
                        </button>
                    </form>

                @endif
            </div>
        </section>
        <aside class="panel">
            <h2>Ruang penawaran</h2>
            <p>Harga, durasi, dan proposal dalam satu tempat.</p>
            {{-- Form khusus Developer --}}
            @if($myBid)
                <hr>
                <section aria-labelledby="my-bid-title" class="info">
                    <h2 id="my-bid-title">Penawaran Kamu</h2>
                    <p>Kamu sudah mengirim penawaran untuk proyek ini.</p>
                    <p>
                        <strong>Harga Penawaran:</strong> Rp{{ number_format($myBid->harga, 0, ',', '.') }}</p>
                    <p>
                        <strong>Durasi:</strong> {{ $myBid->durasi }} hari</p>
                    <p>
                        <strong>Proposal:</strong>
                        <span class="prose">{{ $myBid->proposal }}</span>
                    </p>
                    <p>
                        <strong>Status Penawaran:</strong>
                        @if($myBid->is_winner)
                            Terpilih sebagai pemenang
                        @elseif($hasWinner)
                            Tidak terpilih
                        @elseif($project->status !== 'terbuka' || !$project->batas_lelang->isFuture())
                            Lelang berakhir, belum ada pemenang
                        @else
                            Menunggu keputusan klien
                        @endif
                    </p>
                </section>
            @elseif(auth()->user()->can('bid', $project)
                && $project->status === 'terbuka'
                && !$hasWinner
                && \Illuminate\Support\Carbon::parse($project->batas_lelang)->isFuture())

                <hr>
                <h2>Ajukan Penawaran</h2>
                <p>Isi penawaran untuk mengerjakan proyek ini.</p>
                <form action="{{ route('bids.store', $project) }}"
                    method="POST">

                    @csrf

                    <div class="info">
                        <label for="harga">Harga Penawaran (Rp)</label>
                        <input id="harga" type="number"
                        name="harga"
                        value="{{ old('harga') }}"
                        min="1"
                        required>
                        @error('harga')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="info">
                        <label for="durasi">Durasi Pengerjaan (Hari)</label>
                        <input id="durasi" type="number"
                        name="durasi"
                        value="{{ old('durasi') }}"
                        min="1"
                        required>
                        @error('durasi')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="info">
                        <label for="proposal">Proposal Penawaran</label>
                        <textarea id="proposal" name="proposal"
                        minlength="10"
                        maxlength="3000"
                        required>{{ old('proposal') }}</textarea>
                        @error('proposal')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit">
                        Kirim Penawaran
                    </button>
                </form>
            @elseif(auth()->user()->role === 'developer')
                <div class="notice">Lelang telah berakhir atau proyek sudah ditutup. Penawaran baru tidak tersedia.</div>
            @else
                <p>Penawaran hanya dapat diajukan oleh developer. Pemilik proyek dapat meninjau penawaran di bawah.</p>
            @endif

        </aside>
    </div>
    {{-- Daftar penawaran khusus pemilik proyek --}}
    @if(auth()->user()->can('selectWinner', $project))
        <section class="panel section-space">
            <hr>
            <h2>Daftar Penawaran Developer</h2>
            <p>Berikut penawaran yang masuk untuk proyek ini.</p>

            @forelse($project->bids as $bid)
                <div class="bid-card">
                    <p>
                        <strong>Developer:</strong>
                        {{ $bid->developer?->nama ?? 'Tidak diketahui' }}
                    </p>
                    <p>
                        <strong>Harga Penawaran:</strong>
                        Rp{{ number_format($bid->harga, 0, ',', '.') }}
                    </p>
                    <p>
                        <strong>Durasi:</strong>
                        {{ $bid->durasi }} hari
                    </p>
                    <p>
                        <strong>Proposal:</strong>
                        <span class="prose">{{ $bid->proposal }}</span>
                    </p>
                    <p>
                        <strong>Status:</strong>
                        {{ $bid->is_winner ? 'Terpilih' : ($hasWinner ? 'Tidak terpilih' : 'Belum dipilih') }}
                    </p>

                    {{-- Tombol memilih pemenang --}}
                    @if($project->status === 'terbuka'
                        && !$project->bids->contains('is_winner', true))

                        <form action="{{ route('bids.winner', [$project, $bid]) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin memilih developer ini sebagai pemenang?');">

                            @csrf

                            <button type="submit">

                                Pilih Pemenang

                            </button>
                        </form>
                    @endif

                </div>
            @empty
                <p>Belum ada penawaran untuk proyek ini.</p>
            @endforelse

        </section>
    @endif
@endsection
