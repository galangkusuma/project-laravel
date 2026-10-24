<?php

namespace Tests\Feature;

use App\Models\KelompokTani;
use App\Models\Petani;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PetaniSearchTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_petani_index_filters_results_by_search_term(): void
    {
        $kelompok = KelompokTani::create([
            'nama_kelompok' => 'Kelompok Maju',
        ]);

        Petani::create([
            'nama' => 'Sari Maju',
            'nik' => '1234567890123456',
            'alamat' => 'Desa Maju',
            'telp' => '081234567890',
            'foto' => '',
            'id_kelompok_tani' => $kelompok->id_kelompok_tani,
            'status' => '1',
        ]);

        Petani::create([
            'nama' => 'Budi Sejahtera',
            'nik' => '6543210987654321',
            'alamat' => 'Desa Sejahtera',
            'telp' => '089876543210',
            'foto' => '',
            'id_kelompok_tani' => $kelompok->id_kelompok_tani,
            'status' => '1',
        ]);

        $response = $this->get(route('petani.index', ['search' => 'Sari']));

        $response->assertOk()
            ->assertSee('Sari Maju')
            ->assertDontSee('Budi Sejahtera');
    }

    public function test_petani_index_filters_results_by_group(): void
    {
        $selectedGroup = KelompokTani::create([
            'nama_kelompok' => 'Kelompok Pilihan',
        ]);
        $otherGroup = KelompokTani::create([
            'nama_kelompok' => 'Kelompok Lain',
        ]);

        Petani::create([
            'nama' => 'Petani Pilihan',
            'nik' => '1234567890123456',
            'alamat' => 'Desa Pilihan',
            'telp' => '081234567890',
            'foto' => '',
            'id_kelompok_tani' => $selectedGroup->id_kelompok_tani,
            'status' => '1',
        ]);

        Petani::create([
            'nama' => 'Petani Lain',
            'nik' => '6543210987654321',
            'alamat' => 'Desa Lain',
            'telp' => '089876543210',
            'foto' => '',
            'id_kelompok_tani' => $otherGroup->id_kelompok_tani,
            'status' => '1',
        ]);

        $response = $this->get(route('petani.index', [
            'id_kelompok_tani' => $selectedGroup->id_kelompok_tani,
        ]));

        $response->assertOk()
            ->assertSee('Petani Pilihan')
            ->assertDontSee('Petani Lain');
    }

    public function test_petani_index_sorts_results_by_group_name(): void
    {
        $zebraGroup = KelompokTani::create([
            'nama_kelompok' => 'Zebra',
        ]);
        $alphaGroup = KelompokTani::create([
            'nama_kelompok' => 'Alpha',
        ]);

        Petani::create([
            'nama' => 'Petani Zebra',
            'nik' => '1234567890123456',
            'alamat' => 'Desa Zebra',
            'telp' => '081234567890',
            'foto' => '',
            'id_kelompok_tani' => $zebraGroup->id_kelompok_tani,
            'status' => '1',
        ]);

        Petani::create([
            'nama' => 'Petani Alpha',
            'nik' => '6543210987654321',
            'alamat' => 'Desa Alpha',
            'telp' => '089876543210',
            'foto' => '',
            'id_kelompok_tani' => $alphaGroup->id_kelompok_tani,
            'status' => '1',
        ]);

        $response = $this->get(route('petani.index'));

        $response->assertOk();
        $this->assertSame(
            ['Petani Alpha', 'Petani Zebra'],
            $response->viewData('petanis')->pluck('nama')->all(),
        );
    }
}
