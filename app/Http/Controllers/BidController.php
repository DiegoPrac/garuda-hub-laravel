<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    // Developer mengirim penawaran
    public function store(Request $request, Project $project)
    {
        // Hanya developer yang boleh memberikan penawaran
        abort_unless(
            auth()->user()?->role === 'developer',
            403,
            'Hanya developer yang boleh menawar.'
        );

        // Pastikan proyek masih terbuka
        if ($project->status !== 'terbuka') {
            return back()->with(
                'error',
                'Proyek ini sudah tidak menerima penawaran.'
            );
        }

        // Periksa batas waktu lelang
        if (now()->greaterThanOrEqualTo($project->batas_lelang)) {
            return back()->with(
                'error',
                'Batas waktu lelang sudah berakhir.'
            );
        }

        // Tidak boleh menawar jika pemenang sudah dipilih
        if ($project->bids()->where('is_winner', true)->exists()) {
            return back()->with(
                'error',
                'Pemenang proyek ini sudah ditentukan.'
            );
        }

        // Satu developer hanya boleh menawar sekali
        $sudahMenawar = Bid::where('project_id', $project->id)
            ->where('developer_id', auth()->id())
            ->exists();

        if ($sudahMenawar) {
            return back()->with(
                'error',
                'Kamu sudah memberikan penawaran pada proyek ini.'
            );
        }

        // Validasi data penawaran
        $data = $request->validate([
            'harga' => 'required|integer|min:1',
            'durasi' => 'required|integer|min:1',
            'proposal' => 'required|string|min:10|max:3000',
        ]);

        // Simpan penawaran menggunakan Eloquent
        Bid::create([
            'project_id' => $project->id,
            'developer_id' => auth()->id(),
            'harga' => $data['harga'],
            'durasi' => $data['durasi'],
            'proposal' => $data['proposal'],
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Penawaran berhasil dikirim!');
    }

    // Klien memilih pemenang lelang
    public function selectWinner(Project $project, Bid $bid)
    {
        // Hanya pemilik proyek yang boleh memilih pemenang
        abort_unless(
            auth()->user()?->role === 'klien' &&
            (int) $project->client_id === (int) auth()->id(),
            403,
            'Anda tidak memiliki akses.'
        );

        // Pastikan penawaran berasal dari proyek yang benar
        abort_unless(
            (int) $bid->project_id === (int) $project->id,
            404
        );

        // Gunakan transaksi untuk menjaga konsistensi data
        DB::transaction(function () use ($project, $bid) {

            $lockedProject = Project::query()
                ->lockForUpdate()
                ->findOrFail($project->id);

            // Proyek harus masih terbuka
            abort_unless(
                $lockedProject->status === 'terbuka',
                422,
                'Proyek sudah tidak terbuka.'
            );

            // Pastikan belum ada pemenang sebelumnya
            $sudahAdaPemenang = $lockedProject->bids()
                ->where('is_winner', true)
                ->exists();

            abort_if(
                $sudahAdaPemenang,
                409,
                'Pemenang proyek sudah dipilih.'
            );

// Tandai developer yang dipilih sebagai pemenang
$lockedProject->bids()
    ->whereKey($bid->id)
    ->update([
        'is_winner' => true,
    ]);

// Otomatis tutup lelang setelah pemenang dipilih
$lockedProject->status = 'ditutup';
$lockedProject->save();
        });

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Pemenang lelang berhasil dipilih!');
    }
}
