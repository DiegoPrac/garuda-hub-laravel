<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GarudaHubSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function buatProyek(User $klien): Project
    {
        return Project::create([
            'client_id' => $klien->id,
            'judul' => 'Website Pengujian',
            'deskripsi' => 'Proyek untuk pengujian keamanan.',
            'kategori' => 'Web Development',
            'budget_min' => 500000,
            'budget_max' => 2000000,
            'batas_lelang' => now()->addDays(7)->toDateString(),
            'status' => 'terbuka',
        ]);
    }

    public function test_klien_lain_tidak_bisa_edit_proyek(): void
    {
        $pemilik = User::factory()->create(['role' => 'klien']);
        $klienLain = User::factory()->create(['role' => 'klien']);

        $project = $this->buatProyek($pemilik);

        $this->actingAs($klienLain)
            ->get(route('projects.edit', $project))
            ->assertForbidden();
    }

    public function test_klien_lain_tidak_bisa_hapus_proyek(): void
    {
        $pemilik = User::factory()->create(['role' => 'klien']);
        $klienLain = User::factory()->create(['role' => 'klien']);

        $project = $this->buatProyek($pemilik);

        $this->actingAs($klienLain)
            ->delete(route('projects.destroy', $project))
            ->assertForbidden();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_developer_tidak_bisa_menawar_dua_kali(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $developer = User::factory()->create(['role' => 'developer']);

        $project = $this->buatProyek($klien);

        $penawaran = [
            'harga' => 1000000,
            'durasi' => 14,
            'proposal' => 'Saya siap mengerjakan proyek ini menggunakan Laravel.',
        ];

        $this->actingAs($developer)
            ->post(route('bids.store', $project), $penawaran)
            ->assertRedirect(route('projects.show', $project));

        $this->actingAs($developer)
            ->post(route('bids.store', $project), $penawaran)
            ->assertSessionHas('error');

        $this->assertDatabaseCount('bids', 1);
    }


public function test_proyek_ditutup_tidak_bisa_ditawar(): void
{
    $klien = User::factory()->create([
        'role' => 'klien'
    ]);

    $developer = User::factory()->create([
        'role' => 'developer'
    ]);

    $project = $this->buatProyek($klien);

    // Tutup proyek secara langsung
    $project->status = 'ditutup';
    $project->save();
    $project->refresh();

    // Pastikan status benar-benar berubah
    $this->assertEquals('ditutup', $project->status);

    // Developer mencoba memberikan penawaran
    $this->actingAs($developer)
        ->from(route('projects.show', $project))
        ->post(route('bids.store', $project), [
            'harga' => 1000000,
            'durasi' => 14,
            'proposal' => 'Saya siap mengerjakan proyek menggunakan Laravel.',
        ])
        ->assertSessionHas('error');

    // Pastikan tidak ada penawaran tersimpan
    $this->assertDatabaseCount('bids', 0);
}
}
