<?php

namespace App\Http\Requests;

use App\Models\Petani;
use Illuminate\Foundation\Http\FormRequest;

class StorePetaniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Petani::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'digits:16'],
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:100'],
            'telp' => ['required', 'string', 'max:255'],
            'foto' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
            'id_kelompok_tani' => ['required', 'integer', 'exists:kelompok_tanis,id_kelompok_tani'],
            'status' => ['nullable', 'string', 'max:1'],
        ];
    }
}
