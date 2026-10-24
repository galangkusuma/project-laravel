<?php

namespace Tests\Feature\Api;

use App\Models\KelompokTani;
use App\Models\Petani;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_login_logout_and_revoke_tokens(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Api User',
            'email' => 'api@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['user', 'token']);

        $login = $this->postJson('/api/login', [
            'email' => 'api@example.com',
            'password' => 'password',
        ]);
        $login->assertOk()->assertJsonStructure(['user', 'token']);

        $token = $login->json('token');
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/user')->assertUnauthorized();

        $revokeToken = User::first()->createToken('revoke-test', ['*']);
        $this->withToken($revokeToken->plainTextToken)
            ->deleteJson('/api/tokens/'.$revokeToken->accessToken->id)
            ->assertOk();
        $this->app['auth']->forgetGuards();
        $this->withToken($revokeToken->plainTextToken)->getJson('/api/user')->assertUnauthorized();
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'api@example.com', 'password' => 'password']);

        $this->postJson('/api/login', [
            'email' => 'api@example.com',
            'password' => 'wrong-password',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_petani_api_supports_json_crud_and_upload(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $kelompok = KelompokTani::create(['nama_kelompok' => 'Kelompok API']);
        $payload = [
            'nama' => 'Petani API',
            'nik' => '1234567890123456',
            'alamat' => 'Desa API',
            'telp' => '081234567890',
            'foto' => UploadedFile::fake()->image('petani.jpg'),
            'id_kelompok_tani' => $kelompok->id_kelompok_tani,
            'status' => '1',
        ];

        $created = $this->withToken($this->tokenFor($user))->postJson('/api/petani', $payload);

        $created->assertCreated()->assertJsonPath('nama', 'Petani API');
        Storage::disk('public')->assertExists($created->json('foto'));

        $petani = Petani::firstOrFail();
        $this->getJson('/api/petani/'.$petani->id_penjual)
            ->assertOk()
            ->assertJsonPath('id_penjual', $petani->id_penjual);

        $this->withToken($this->tokenFor($user))->patchJson('/api/petani/'.$petani->id_penjual, [
            'nik' => $payload['nik'],
            'alamat' => $payload['alamat'],
            'telp' => $payload['telp'],
            'id_kelompok_tani' => $payload['id_kelompok_tani'],
            'status' => $payload['status'],
            'nama' => 'Petani Updated',
        ])->assertOk()->assertJsonPath('nama', 'Petani Updated');

        $this->withToken($this->tokenFor($user))
            ->deleteJson('/api/petani/'.$petani->id_penjual)
            ->assertOk();
        $this->assertDatabaseMissing('petanis', ['id_penjual' => $petani->id_penjual]);
    }

    public function test_petani_mutations_require_authentication_and_write_ability(): void
    {
        $kelompok = KelompokTani::create(['nama_kelompok' => 'Kelompok API']);
        $payload = [
            'nama' => 'Petani API',
            'nik' => '1234567890123456',
            'alamat' => 'Desa API',
            'telp' => '081234567890',
            'foto' => UploadedFile::fake()->image('petani.jpg'),
            'id_kelompok_tani' => $kelompok->id_kelompok_tani,
            'status' => '1',
        ];

        $this->postJson('/api/petani', $payload)->assertUnauthorized();

        $user = User::factory()->create();
        $this->withToken($this->tokenFor($user, ['petani:read']))
            ->postJson('/api/petani', $payload)
            ->assertForbidden();
    }

    public function test_petani_api_validates_upload_and_fields(): void
    {
        $user = User::factory()->create();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/petani', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama', 'nik', 'foto', 'id_kelompok_tani']);
    }

    public function test_petani_api_search_filter_and_pagination(): void
    {
        $selectedGroup = KelompokTani::create(['nama_kelompok' => 'Kelompok Pilihan']);
        $otherGroup = KelompokTani::create(['nama_kelompok' => 'Kelompok Lain']);

        Petani::create([
            'nama' => 'Sari Pilihan', 'nik' => '1234567890123456', 'alamat' => 'Desa Pilihan',
            'telp' => '081234567890', 'foto' => 'sari.jpg', 'id_kelompok_tani' => $selectedGroup->id_kelompok_tani, 'status' => '1',
        ]);
        Petani::create([
            'nama' => 'Budi Lain', 'nik' => '6543210987654321', 'alamat' => 'Desa Lain',
            'telp' => '089876543210', 'foto' => 'budi.jpg', 'id_kelompok_tani' => $otherGroup->id_kelompok_tani, 'status' => '1',
        ]);

        $this->getJson('/api/petani?search=Sari&id_kelompok_tani='.$selectedGroup->id_kelompok_tani.'&per_page=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nama', 'Sari Pilihan')
            ->assertJsonPath('per_page', 1);
    }

    public function test_petani_api_sorts_results_by_group_name(): void
    {
        $zebraGroup = KelompokTani::create(['nama_kelompok' => 'Zebra']);
        $alphaGroup = KelompokTani::create(['nama_kelompok' => 'Alpha']);

        Petani::create([
            'nama' => 'Petani Zebra', 'nik' => '1234567890123456', 'alamat' => 'Desa Zebra',
            'telp' => '081234567890', 'foto' => '', 'id_kelompok_tani' => $zebraGroup->id_kelompok_tani, 'status' => '1',
        ]);
        Petani::create([
            'nama' => 'Petani Alpha', 'nik' => '6543210987654321', 'alamat' => 'Desa Alpha',
            'telp' => '089876543210', 'foto' => '', 'id_kelompok_tani' => $alphaGroup->id_kelompok_tani, 'status' => '1',
        ]);

        $response = $this->getJson('/api/petani?per_page=10')->assertOk();

        $this->assertSame('Petani Alpha', $response->json('data.0.nama'));
        $this->assertSame('Petani Zebra', $response->json('data.1.nama'));
    }

    private function tokenFor(User $user, array $abilities = ['*']): string
    {
        return $user->createToken('test-token', $abilities)->plainTextToken;
    }
}
