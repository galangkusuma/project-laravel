<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePetaniRequest;
use App\Http\Requests\UpdatePetaniRequest;
use App\Models\KelompokTani;
use App\Models\Petani;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PetaniController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim($request->string('search')->toString());
        $kelompokId = $request->integer('id_kelompok_tani');
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        $petanis = Petani::with('kelompoktani')
            ->when($kelompokId > 0, fn (Builder $query) => $query->where('id_kelompok_tani', $kelompokId))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('telp', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhereHas('kelompoktani', fn (Builder $query) => $query->where('nama_kelompok', 'like', "%{$search}%"));
                });
            })
            ->orderBy(KelompokTani::select('nama_kelompok')
                ->whereColumn('kelompok_tanis.id_kelompok_tani', 'petanis.id_kelompok_tani'))
            ->orderBy('nama')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json($petanis);
    }

    public function show(Petani $petani): JsonResponse
    {
        return response()->json($petani->load('kelompoktani'));
    }

    public function store(StorePetaniRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['foto'] = $request->file('foto')->store('uploads/petanis', 'public');
        $petani = Petani::create($data)->load('kelompoktani');

        return response()->json($petani, 201);
    }

    public function update(UpdatePetaniRequest $request, Petani $petani): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('uploads/petanis', 'public');
        }

        $petani->update($data);

        return response()->json($petani->fresh()->load('kelompoktani'));
    }

    public function destroy(Request $request, Petani $petani): JsonResponse
    {
        Gate::authorize('delete', $petani);
        $petani->delete();

        return response()->json(['message' => 'Petani deleted successfully.']);
    }
}
