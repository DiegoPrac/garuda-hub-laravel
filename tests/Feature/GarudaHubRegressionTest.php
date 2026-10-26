<?php

namespace Tests\Feature;

use App\Models\Bid;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GarudaHubRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function buatProyek(User $klien): Project
    {
        return $klien->projects()->create([
            'judul' => 'Proyek regresi', 'deskripsi' => 'Deskripsi proyek regresi.',
            'kategori' => 'Web', 'budget_min' => 100, 'budget_max' => 200,
            'batas_lelang' => now()->addWeek()->toDateString(),
        ]);
    }

    private function penawaran(): array
    {
        return ['harga' => 150, 'durasi' => 7, 'proposal' => 'Proposal rahasia developer.'];
    }

    private function buatBid(Project $project): Bid
    {
        return $project->bids()->create([
            'developer_id' => User::factory()->create(['role' => 'developer'])->id,
            ...$this->penawaran(),
        ]);
    }

    public function test_form_diganti_status_setelah_developer_menawar(): void
    {
        $project = $this->buatProyek(User::factory()->create(['role' => 'klien']));
        $developer = User::factory()->create(['role' => 'developer']);

        $this->actingAs($developer)->get(route('projects.show', $project))
            ->assertOk()->assertSee('Kirim Penawaran');
        $this->post(route('bids.store', $project), $this->penawaran())
            ->assertRedirect(route('projects.show', $project));
        $this->get(route('projects.show', $project))
            ->assertOk()->assertSee('Penawaran Kamu')->assertSee('Menunggu keputusan klien')
            ->assertSee('Rp150')->assertSee('7 hari')->assertSee($this->penawaran()['proposal'])
            ->assertDontSee('Kirim Penawaran')->assertDontSee('name="harga"', false);

        $developerLain = User::factory()->create(['role' => 'developer']);
        $this->actingAs($developerLain)->get(route('projects.show', $project))
            ->assertOk()->assertSee('Kirim Penawaran')->assertDontSee($this->penawaran()['proposal']);
    }

    public function test_status_penawaran_mengikuti_hasil_dan_batas_lelang(): void
    {
        $this->freezeTime();
        $project = $this->buatProyek(User::factory()->create(['role' => 'klien']));
        $bid = $this->buatBid($project);
        $this->actingAs($bid->developer);
        $this->travelTo($project->batas_lelang);
        $this->get(route('projects.show', $project))->assertOk()
            ->assertSee('Lelang berakhir, belum ada pemenang')->assertDontSee('Kirim Penawaran');

        $winner = $this->buatBid($project);
        $winner->is_winner = true;
        $winner->save();
        $project->status = 'ditutup';
        $project->save();
        $this->get(route('projects.show', $project))->assertOk()
            ->assertSee('Tidak terpilih')->assertDontSee('Kirim Penawaran');
        $this->actingAs($winner->developer)->get(route('projects.show', $project))->assertOk()
            ->assertSee('Terpilih sebagai pemenang')->assertDontSee('Kirim Penawaran');
    }

    public function test_policy_membatasi_peran_dan_kepemilikan(): void
    {
        $pemilik = User::factory()->create(['role' => 'klien']);
        $project = $this->buatProyek($pemilik);

        foreach ([$pemilik, User::factory()->create(['role' => 'klien']), User::factory()->create(['role' => 'developer']), User::factory()->create(['role' => 'admin'])] as $user) {
            $gate = Gate::forUser($user);
            $this->assertSame($user->role === 'klien', $gate->allows('create', Project::class));
            $this->assertSame($user->role === 'developer', $gate->allows('bid', $project));
            foreach (['update', 'delete', 'selectWinner'] as $ability) {
                $this->assertSame($user->is($pemilik), $gate->allows($ability, $project));
            }
        }
    }

    public function test_tamu_diarahkan_ke_login_saat_menawar(): void
    {
        $project = $this->buatProyek(User::factory()->create());

        $this->post(route('bids.store', $project), $this->penawaran())->assertRedirect(route('login'));

        $this->assertDatabaseCount('bids', 0);
    }

    public function test_klien_tidak_bisa_mengirim_bid(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $project = $this->buatProyek($klien);

        $this->actingAs($klien)->post(route('bids.store', $project), $this->penawaran())->assertForbidden();

        $this->assertDatabaseCount('bids', 0);
    }

    public function test_bid_project_lain_tidak_bisa_dipilih(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $project = $this->buatProyek($klien);
        $bid = $this->buatBid($this->buatProyek($klien));

        $this->actingAs($klien)->post(route('bids.winner', [$project, $bid]))->assertNotFound();

        $this->assertDatabaseHas('bids', ['id' => $bid->id, 'is_winner' => false]);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'terbuka']);
    }

    public function test_klien_lain_tidak_bisa_memilih_pemenang(): void
    {
        $project = $this->buatProyek(User::factory()->create(['role' => 'klien']));
        $bid = $this->buatBid($project);
        $klienLain = User::factory()->create(['role' => 'klien']);

        $this->actingAs($klienLain)->post(route('bids.winner', [$project, $bid]))->assertForbidden();

        $this->assertDatabaseHas('bids', ['id' => $bid->id, 'is_winner' => false]);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'terbuka']);
    }

    public function test_pemenang_tidak_bisa_diganti_setelah_ditutup(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $project = $this->buatProyek($klien);
        $pemenang = $this->buatBid($project);
        $bidLain = $this->buatBid($project);
        $pemenang->is_winner = true;
        $pemenang->save();
        $project->status = 'ditutup';
        $project->save();

        $this->actingAs($klien)->post(route('bids.winner', [$project, $bidLain]))->assertUnprocessable();

        $this->assertDatabaseHas('bids', ['id' => $pemenang->id, 'is_winner' => true]);
        $this->assertDatabaseHas('bids', ['id' => $bidLain->id, 'is_winner' => false]);
    }

    public function test_bid_ditolak_tepat_pada_batas_lelang(): void
    {
        $this->freezeTime();
        $project = $this->buatProyek(User::factory()->create());
        $this->travelTo($project->batas_lelang);
        $developer = User::factory()->create(['role' => 'developer']);

        $this->actingAs($developer)->post(route('bids.store', $project), $this->penawaran())
            ->assertSessionHas('error', 'Batas waktu lelang sudah berakhir.');

        $this->assertDatabaseCount('bids', 0);
    }

    public function test_bid_mengabaikan_identitas_dan_status_palsu(): void
    {
        $project = $this->buatProyek(User::factory()->create());
        $developer = User::factory()->create(['role' => 'developer']);

        $this->actingAs($developer)->post(route('bids.store', $project), [
            ...$this->penawaran(), 'developer_id' => $project->client_id,
            'project_id' => 9999, 'is_winner' => true,
        ])->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('bids', [
            'project_id' => $project->id, 'developer_id' => $developer->id, 'is_winner' => false,
        ]);
    }

    public function test_durasi_melebihi_kapasitas_mysql_ditolak(): void
    {
        $project = $this->buatProyek(User::factory()->create());
        $developer = User::factory()->create(['role' => 'developer']);

        $this->actingAs($developer)->post(route('bids.store', $project), [
            ...$this->penawaran(), 'durasi' => 4294967296,
        ])->assertSessionHasErrors('durasi');

        $this->assertDatabaseCount('bids', 0);
    }

    public function test_proposal_hanya_terlihat_oleh_pemilik_project(): void
    {
        $project = $this->buatProyek(User::factory()->create(['role' => 'klien']));
        $this->buatBid($project);
        $klienLain = User::factory()->create(['role' => 'klien']);

        $this->actingAs($klienLain)->get(route('projects.show', $project))
            ->assertOk()->assertDontSee('Proposal rahasia developer.');
    }

    public function test_registrasi_tidak_mengizinkan_admin(): void
    {
        $this->post(route('register.store'), [
            'nama' => 'Admin', 'email' => 'admin@example.test', 'role' => 'admin',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_registrasi_developer_menyimpan_peran_dan_password_hash(): void
    {
        $this->post(route('register.store'), [
            'nama' => 'Developer', 'email' => 'developer@example.test', 'role' => 'developer',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertRedirect('/');

        $user = User::where('email', 'developer@example.test')->firstOrFail();
        $this->assertSame('developer', $user->role);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_pemilihan_pemenang_rollback_jika_penutupan_project_gagal(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $project = $this->buatProyek($klien);
        $bid = $this->buatBid($project);
        $dispatcher = Project::getEventDispatcher();
        Project::setEventDispatcher(clone $dispatcher);
        Project::updating(function (): void {
            throw new \RuntimeException('Simulasi kegagalan penyimpanan proyek.');
        });

        try {
            $this->actingAs($klien)->post(route('bids.winner', [$project, $bid]))->assertServerError();
        } finally {
            Project::setEventDispatcher($dispatcher);
        }

        $this->assertDatabaseHas('bids', ['id' => $bid->id, 'is_winner' => false]);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'terbuka']);
    }

    public function test_pemilik_bisa_update_tanpa_mengganti_pemilik_atau_status(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $project = $this->buatProyek($klien);
        $data = $project->only(['judul', 'deskripsi', 'kategori', 'budget_min', 'budget_max']);

        $this->actingAs($klien)->put(route('projects.update', $project), [
            ...$data, 'judul' => 'Judul baru', 'batas_lelang' => now()->addWeek()->toDateString(),
            'client_id' => 9999, 'status' => 'ditutup',
        ])->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id, 'judul' => 'Judul baru', 'client_id' => $klien->id, 'status' => 'terbuka',
        ]);
    }

    public function test_penghapusan_project_menghapus_bid_terkait(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $project = $this->buatProyek($klien);
        $bid = $this->buatBid($project);

        $this->actingAs($klien)->delete(route('projects.destroy', $project))->assertRedirect(route('projects.index'));

        $this->assertModelMissing($project);
        $this->assertModelMissing($bid);
    }

    public function test_judul_project_di_escape_di_blade(): void
    {
        $klien = User::factory()->create(['role' => 'klien']);
        $project = $this->buatProyek($klien);
        $project->judul = '<script>alert(1)</script>';
        $project->save();

        $this->actingAs($klien)->get(route('projects.show', $project))
            ->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_percobaan_login_dibatasi(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.attempt'), ['email' => 'unknown@example.test', 'password' => 'salah'])
                ->assertSessionHasErrors('email');
        }

        $this->post(route('login.attempt'), ['email' => 'unknown@example.test', 'password' => 'salah'])
            ->assertTooManyRequests();
        $this->assertGuest();
    }
}
