<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GarudaHubUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_pages_render_shared_navigation_and_accessible_forms(): void
    {
        foreach (['home', 'login', 'register'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertSee('css/garudahub.css')
                ->assertSee('Lewati navigasi')
                ->assertDontSee('Logout');
        }

        $this->get(route('login'))->assertSee('for="email"', false)
            ->assertSee('autocomplete="current-password"', false);
        $this->get(route('register'))->assertSee('for="role"', false)
            ->assertSee('autocomplete="new-password"', false);
    }

    public function test_dashboard_navigation_respects_roles_and_project_forms_render(): void
    {
        $client = User::factory()->create(['role' => 'klien']);
        $developer = User::factory()->create(['role' => 'developer']);

        $this->actingAs($client)->get(route('home'))->assertOk()
            ->assertSee(route('projects.create'), false)->assertSee('Logout');
        $this->get(route('projects.create'))->assertOk()
            ->assertSee('for="judul"', false)->assertSee('Simpan Proyek');

        $project = $client->projects()->create([
            'judul' => 'Website komunitas', 'deskripsi' => 'Portal komunitas lokal.',
            'kategori' => 'Web', 'budget_min' => 1000000, 'budget_max' => 2000000,
            'batas_lelang' => now()->addWeek()->toDateString(),
        ]);

        $this->get(route('projects.edit', $project))->assertOk()
            ->assertSee('Website komunitas')->assertSee('Simpan Perubahan');
        $this->actingAs($developer)->get(route('home'))->assertOk()
            ->assertDontSee(route('projects.create'), false)->assertSee('Lihat Daftar Proyek');
        $this->get(route('projects.show', $project))->assertOk()
            ->assertSee('for="proposal"', false)->assertDontSee('Hapus Proyek');
    }

    public function test_project_list_handles_empty_state_and_pagination(): void
    {
        $client = User::factory()->create(['role' => 'klien']);
        $this->actingAs($client)->get(route('projects.index'))->assertOk()
            ->assertSee('Belum Ada Proyek')->assertSee('Buat proyek pertama');

        for ($i = 1; $i <= 11; $i++) {
            $client->projects()->create([
                'judul' => 'Proyek '.$i, 'deskripsi' => 'Deskripsi proyek untuk paginasi.',
                'kategori' => 'Web', 'budget_min' => 100, 'budget_max' => 200,
                'batas_lelang' => now()->addWeek()->toDateString(),
            ]);
        }

        $this->get(route('projects.index'))->assertOk()
            ->assertSee('Halaman 1 dari 2')->assertSee('?page=2', false);
        $this->get(route('projects.index', ['page' => 2]))->assertOk()
            ->assertSee('Halaman 2 dari 2')->assertSee('?page=1', false);
    }
}
