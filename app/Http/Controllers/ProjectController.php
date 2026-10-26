<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    // Menampilkan daftar semua proyek
    public function index(): View
    {
        $projects = Project::with('client')
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    // Menampilkan form tambah proyek
    public function create(): View
    {
        Gate::authorize('create', Project::class);

        return view('projects.create');
    }

    // Menyimpan proyek baru
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Project::class);

        $data = $this->validateProject($request);

        // Pemilik proyek diambil dari akun yang login
        $data['client_id'] = auth()->id();

        Project::create($data);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyek berhasil ditambahkan!');
    }

    // Menampilkan detail proyek
    public function show(Project $project): View
    {
        $project->load('client');

        if (Gate::allows('update', $project)) {
            $project->load('bids.developer');
        }

        $myBid = null;
        if (Gate::allows('bid', $project)) {
            $myBid = $project->bids()->where('developer_id', auth()->id())->first();
        }

        $hasWinner = $project->bids()->where('is_winner', true)->exists();

        return view('projects.show', compact('project', 'myBid', 'hasWinner'));
    }

    // Menampilkan form edit
    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    // Menyimpan perubahan proyek
    public function update(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $data = $this->validateProject($request);

        $project->update($data);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyek berhasil diperbarui!');
    }

    // Menghapus proyek
    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus!');
    }

    // Validasi data proyek
    private function validateProject(Request $request): array
    {
        return $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string|max:50',
            'budget_min' => 'required|integer|min:0',
            'budget_max' => 'required|integer|gte:budget_min',
            'batas_lelang' => 'required|date|after_or_equal:today',
        ]);
    }
}
