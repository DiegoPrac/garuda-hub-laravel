<?php

namespace Tests\Feature;

use App\Models\Bid;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GarudaHubFlowTest extends TestCase
{
    use RefreshDatabase;

    private function buatProyek(User $klien): Project
    {
        return Project::create([
            'client_id' => $klien->id,
            'judul' => 'Website Toko Online',
            'deskripsi' => 'Membuat website toko online sederhana.',
            'kategori' => 'Web Development',
            'budget_min' => 500000,
            'budget_max' => 2000000,
            'batas_lelang' => now()->addDays(7)->toDateString(),
        ]);
    }

    public function test_developer_tidak_bisa_membuat_proyek(): void
    {
        $developer = User::factory()->create([
            'role' => 'developer',
        ]);

        $this->actingAs($developer)
            ->get(route('projects.create'))
            ->assertForbidden();
    }

    public function test_klien_bisa_membuat_proyek(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $this->actingAs($klien)
            ->post(route('projects.store'), [
                'judul' => 'Website Toko Online',
                'deskripsi' => 'Membuat website sederhana.',
                'kategori' => 'Web Development',
                'budget_min' => 500000,
                'budget_max' => 2000000,
                'batas_lelang' => now()->addDays(7)->toDateString(),
            ])
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', [
            'client_id' => $klien->id,
            'judul' => 'Website Toko Online',
        ]);
    }

    public function test_developer_bisa_mengirim_penawaran(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $developer = User::factory()->create([
            'role' => 'developer',
        ]);

        $project = $this->buatProyek($klien);

        $this->actingAs($developer)
            ->post(route('bids.store', $project), [
                'harga' => 1200000,
                'durasi' => 14,
                'proposal' => 'Saya siap mengerjakan proyek ini.',
            ])
            ->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('bids', [
            'project_id' => $project->id,
            'developer_id' => $developer->id,
            'harga' => 1200000,
        ]);
    }

    public function test_klien_bisa_memilih_pemenang(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $developer = User::factory()->create([
            'role' => 'developer',
        ]);

        $project = $this->buatProyek($klien);

        $bid = Bid::create([
            'project_id' => $project->id,
            'developer_id' => $developer->id,
            'harga' => 1200000,
            'durasi' => 14,
            'proposal' => 'Saya siap mengerjakan proyek ini.',
        ]);

        $this->actingAs($klien)
            ->post(route('bids.winner', [$project, $bid]))
            ->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('bids', [
            'id' => $bid->id,
            'is_winner' => 1,
        ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => 'ditutup',
        ]);
    }
}
