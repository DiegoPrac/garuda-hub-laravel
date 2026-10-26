<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BidController extends Controller
{
    // Developer mengirim penawaran
    public function store(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('bid', $project);

        return DB::transaction(function () use ($request, $project): RedirectResponse {
            // Semua penawaran dan pemilihan pemenang mengunci proyek yang sama.
            $project = Project::query()->lockForUpdate()->findOrFail($project->id);
            Gate::authorize('bid', $project);

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
                'durasi' => 'required|integer|min:1|max:4294967295',
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
        }, 3);
    }

    // Klien memilih pemenang lelang
    public function selectWinner(Project $project, Bid $bid): RedirectResponse
    {
        Gate::authorize('selectWinner', $project);

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

            Gate::authorize('selectWinner', $lockedProject);
            $lockedBid = $lockedProject->bids()->lockForUpdate()->findOrFail($bid->id);

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
            $lockedBid->is_winner = true;
            $lockedBid->save();

            // Otomatis tutup lelang setelah pemenang dipilih
            $lockedProject->status = 'ditutup';
            $lockedProject->save();
        }, 3);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Pemenang lelang berhasil dipilih!');
    }
}
