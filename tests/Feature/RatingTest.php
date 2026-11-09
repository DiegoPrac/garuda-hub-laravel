<?php

namespace Tests\Feature;

use App\Models\Bid;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    // Membuat proyek contoh untuk pengujian
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

    // Membuat proyek yang sudah memiliki pemenang
    private function buatProyekDenganPemenang(
        User $klien,
        User $developer
    ): Project {
        $project = $this->buatProyek($klien);

        $bid = Bid::create([
            'project_id' => $project->id,
            'developer_id' => $developer->id,
            'harga' => 1200000,
            'durasi' => 14,
            'proposal' => 'Saya siap mengerjakan proyek ini.',
        ]);

        // Tandai penawaran sebagai pemenang
        $bid->is_winner = true;
        $bid->save();

        // Tutup proyek setelah pemenang ditentukan
        $project->status = 'ditutup';
        $project->save();

        return $project;
    }

    public function test_klien_bisa_memberikan_rating(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $developer = User::factory()->create([
            'role' => 'developer',
        ]);

        $project = $this->buatProyekDenganPemenang(
            $klien,
            $developer
        );

        $this->actingAs($klien)
            ->post(route('ratings.store', $project), [
                'nilai' => 5,
                'komentar' => 'Hasil pekerjaan sangat bagus.',
            ])
            ->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('ratings', [
            'project_id' => $project->id,
            'client_id' => $klien->id,
            'developer_id' => $developer->id,
            'nilai' => 5,
            'komentar' => 'Hasil pekerjaan sangat bagus.',
        ]);
    }

    public function test_developer_tidak_bisa_memberikan_rating(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $developer = User::factory()->create([
            'role' => 'developer',
        ]);

        $project = $this->buatProyekDenganPemenang(
            $klien,
            $developer
        );

        $this->actingAs($developer)
            ->post(route('ratings.store', $project), [
                'nilai' => 5,
                'komentar' => 'Mencoba memberikan rating.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('ratings', 0);
    }

    public function test_klien_lain_tidak_bisa_memberikan_rating(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $klienLain = User::factory()->create([
            'role' => 'klien',
        ]);

        $developer = User::factory()->create([
            'role' => 'developer',
        ]);

        $project = $this->buatProyekDenganPemenang(
            $klien,
            $developer
        );

        $this->actingAs($klienLain)
            ->post(route('ratings.store', $project), [
                'nilai' => 4,
                'komentar' => 'Proyek ini bukan milik saya.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('ratings', 0);
    }

    public function test_tamu_harus_login_sebelum_rating(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $developer = User::factory()->create([
            'role' => 'developer',
        ]);

        $project = $this->buatProyekDenganPemenang(
            $klien,
            $developer
        );

        $this->post(route('ratings.store', $project), [
            'nilai' => 5,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('ratings', 0);
    }

    public function test_rating_ditolak_jika_belum_ada_pemenang(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $project = $this->buatProyek($klien);

        $this->actingAs($klien)
            ->post(route('ratings.store', $project), [
                'nilai' => 5,
                'komentar' => 'Belum ada pemenang.',
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('ratings', 0);
    }

    public function test_satu_proyek_tidak_bisa_rating_dua_kali(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $developer = User::factory()->create([
            'role' => 'developer',
        ]);

        $project = $this->buatProyekDenganPemenang(
            $klien,
            $developer
        );

        // Rating pertama berhasil
        $this->actingAs($klien)
            ->post(route('ratings.store', $project), [
                'nilai' => 5,
                'komentar' => 'Rating pertama.',
            ])
            ->assertRedirect(route('projects.show', $project));

        // Rating kedua harus ditolak
        $this->actingAs($klien)
            ->post(route('ratings.store', $project), [
                'nilai' => 1,
                'komentar' => 'Rating kedua.',
            ])
            ->assertStatus(409);

        $this->assertDatabaseCount('ratings', 1);
    }

    public function test_nilai_rating_harus_antara_1_sampai_5(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $developer = User::factory()->create([
            'role' => 'developer',
        ]);

        $project = $this->buatProyekDenganPemenang(
            $klien,
            $developer
        );

        // Nilai 0 tidak diperbolehkan
        $this->actingAs($klien)
            ->post(route('ratings.store', $project), [
                'nilai' => 0,
            ])
            ->assertSessionHasErrors('nilai');

        // Nilai 6 juga tidak diperbolehkan
        $this->actingAs($klien)
            ->post(route('ratings.store', $project), [
                'nilai' => 6,
            ])
            ->assertSessionHasErrors('nilai');

        $this->assertDatabaseCount('ratings', 0);
    }

    public function test_komentar_rating_dilindungi_dari_xss(): void
    {
        $klien = User::factory()->create([
            'role' => 'klien',
        ]);

        $developer = User::factory()->create([
            'role' => 'developer',
        ]);

        $project = $this->buatProyekDenganPemenang(
            $klien,
            $developer
        );

        $komentar = '<script>alert(1)</script>';

        $this->actingAs($klien)
            ->post(route('ratings.store', $project), [
                'nilai' => 5,
                'komentar' => $komentar,
            ])
            ->assertRedirect(route('projects.show', $project));

        // Komentar harus ditampilkan sebagai teks biasa
        $this->actingAs($klien)
            ->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee(
                '&lt;script&gt;alert(1)&lt;/script&gt;',
                false
            )
            ->assertDontSee($komentar, false);
    }
}
