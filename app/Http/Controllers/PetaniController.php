<?php

namespace App\Http\Controllers;

use App\Models\KelompokTani;
use App\Models\Petani;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PetaniController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Daftar Petani';
        $search = trim($request->input('search', ''));
        $kelompokId = $request->integer('id_kelompok_tani');

        $petanis = Petani::with('kelompoktani')
            ->when($kelompokId > 0, function (Builder $query) use ($kelompokId) {
                $query->where('id_kelompok_tani', $kelompokId);
            })
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('telp', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhereHas('kelompoktani', function (Builder $query) use ($search) {
                            $query->where('nama_kelompok', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy(KelompokTani::select('nama_kelompok')
                ->whereColumn('kelompok_tanis.id_kelompok_tani', 'petanis.id_kelompok_tani'))
            ->orderBy('nama')
            ->paginate(2)
            ->withQueryString();
        $kelompoks = KelompokTani::orderBy('nama_kelompok')->get();

        return view('admin.daftarpetani', compact('title', 'petanis', 'kelompoks'));
    }

    public function create()
    {
        $title = 'Input Petani';
        $kelompoks = KelompokTani::all();

        return view('admin.inputpetani', compact('title', 'kelompoks'));
    }

    public function store(Request $request)
    {
        $validasi = $request->validate([
            'nik' => 'required|numeric|digits:16',
            'nama' => 'required',
            'alamat' => 'nullable|string|max:100',
            'telp' => 'required',
            'foto' => 'required|image|mimes:png,jpg,jpeg|max:1024',
            'id_kelompok_tani' => 'required',
            'status' => '',
        ]);

        $fileName = time().$request->file('foto')->getClientOriginalName();
        $validasi['foto'] = $request->file('foto')->storeAs('uploads/petanis', $fileName, 'public');
        Petani::create($validasi);

        return redirect()->route('petani.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $title = 'Input Petani';
        $kelompoks = KelompokTani::all();
        $petani = Petani::find($id);

        if ($petani !== null) {
            $title = 'Edit Data '.$petani->nama;
        }

        return view('admin.inputpetani', compact('title', 'kelompoks', 'petani'));
    }

    public function update(Request $request, $id)
    {
        $validasi = $request->validate([
            'nik' => 'required|numeric',
            'nama' => 'required',
            'alamat' => 'nullable|string|max:100',
            'telp' => 'required',
            'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'id_kelompok_tani' => 'required',
            'status' => '',
        ]);

        if ($request->file('foto')) {
            $fileName = time().$request->file('foto')->getClientOriginalName();
            $validasi['foto'] = $request->file('foto')->storeAs('uploads/petanis', $fileName, 'public');
        }

        Petani::findOrFail($id)->update($validasi);

        return redirect()->route('petani.index');
    }

    public function destroy($id)
    {
        Petani::where('id_penjual', $id)->firstOrFail()->delete();

        return redirect()->route('petani.index')->with('success', 'Data petani berhasil dihapus!');
    }
}
