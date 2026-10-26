<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\Request;

class BidController extends Controller
{
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
}
