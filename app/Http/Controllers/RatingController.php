<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    // Klien memberikan rating kepada developer pemenang
    public function store(Request $request, Project $project): RedirectResponse
    {
        // Hanya klien pemilik proyek yang boleh memberikan rating
        abort_unless(
            auth()->user()?->role === 'klien'
            && (int) $project->client_id === (int) auth()->id(),
            403
        );

        // Validasi nilai dan komentar
        $data = $request->validate([
            'nilai' => 'required|integer|between:1,5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($project, $data): RedirectResponse {

            // Kunci proyek agar tidak terjadi rating ganda bersamaan
            $project = Project::query()
                ->lockForUpdate()
                ->findOrFail($project->id);

            // Periksa kembali pemilik proyek
            abort_unless(
                auth()->user()?->role === 'klien'
                && (int) $project->client_id === (int) auth()->id(),
                403
            );

            // Rating hanya setelah pemenang ditentukan
            abort_unless(
                $project->status === 'ditutup',
                422,
                'Proyek belum ditutup.'
            );

            // Cari developer yang memenangkan lelang
            $pemenang = $project->bids()
                ->where('is_winner', true)
                ->first();

            abort_unless(
                $pemenang !== null,
                422,
                'Pemenang proyek belum ditentukan.'
            );

            // Satu proyek hanya boleh mempunyai satu rating
            abort_if(
                $project->rating()->exists(),
                409,
                'Proyek ini sudah memiliki rating.'
            );

            // Simpan rating
            Rating::create([
                'project_id' => $project->id,
                'client_id' => auth()->id(),
                'developer_id' => $pemenang->developer_id,
                'nilai' => $data['nilai'],
                'komentar' => $data['komentar'] ?? null,
            ]);

            return redirect()
                ->route('projects.show', $project)
                ->with('success', 'Rating berhasil diberikan!');
        }, 3);
    }
}
