<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Menampilkan daftar semua proyek
    public function index()
    {
        $projects = Project::with('client')
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    // Menampilkan form tambah proyek
    public function create()
    {
        abort_unless(auth()->user()?->role === 'klien', 403);

        return view('projects.create');
    }

    // Menyimpan proyek baru
    public function store(Request $request)
    {
        abort_unless(auth()->user()?->role === 'klien', 403);

        $data = $this->validateProject($request);

        // Pemilik proyek diambil dari akun yang login
        $data['client_id'] = auth()->id();

        Project::create($data);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyek berhasil ditambahkan!');
    }

    // Menampilkan detail proyek
    public function show(Project $project)
    {
        $project->load('client');

        return view('projects.show', compact('project'));
    }

    // Menampilkan form edit
    public function edit(Project $project)
    {
        $this->checkOwner($project);

        return view('projects.edit', compact('project'));
    }

    // Menyimpan perubahan proyek
    public function update(Request $request, Project $project)
    {
        $this->checkOwner($project);

        $data = $this->validateProject($request);

        $project->update($data);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyek berhasil diperbarui!');
    }

    // Menghapus proyek
    public function destroy(Project $project)
    {
        $this->checkOwner($project);

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

    // Memastikan proyek milik klien yang login
    private function checkOwner(Project $project): void
    {
        abort_unless(
            auth()->user()?->role === 'klien'
            && $project->client_id === auth()->id(),
            403,
            'Anda tidak memiliki akses ke proyek ini.'
        );
    }
}
