@extends('layouts.admin-layout')

@section('content')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
        {{ $title }}
    </h2>

    <div class="shadow px-6 py-4 bg-white rounded sm:px-1 sm:py-1">
        <div class="container mx-auto">
            <form action="{{ isset($petani) ? route('petani.update', $petani->id_penjual) : route('petani.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                
                @csrf
                @if(isset($petani))
                    @method('PUT')
                @endif

                <div class="px-2 py-8 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">

                        <!-- Field Nama -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="nama" class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" 
                                   id="nama" 
                                   name="nama" 
                                   placeholder="Nama Lengkap" 
                                   required 
                                   value="{{ old('nama', $petani->nama ?? '') }}"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            
                            @error('nama')
                                <div class="text-xs text-red-800 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Field NIK -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                            <input type="number" 
                                   name="nik" 
                                   id="nik" 
                                   placeholder="NIK Anggota Tani" 
                                   required 
                                   value="{{ old('nik', $petani->nik ?? '') }}"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            
                            @error('nik')
                                <div class="text-xs text-red-800 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Field Telp -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="telp" class="block text-sm font-medium text-gray-700">Telp</label>
                            <input type="text" 
                                   name="telp" 
                                   id="telp" 
                                   placeholder="08129921991" 
                                   value="{{ old('telp', $petani->telp ?? '') }}"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            
                            @error('telp')
                                <div class="text-xs text-red-800 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Field Kelompok Tani -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="kelompok" class="block text-sm font-medium text-gray-700">Kelompok Tani</label>
                            <select id="kelompok" 
                                    name="id_kelompok_tani" 
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Pilih Kelompok Tani</option>
                                
                                @foreach ($kelompoks as $item)
                                    <option value="{{ $item->id_kelompok_tani }}" 
                                        @selected(old('id_kelompok_tani', $petani->id_kelompok_tani ?? '') == $item->id_kelompok_tani)>
                                        {{ $item->nama_kelompok }}
                                    </option>
                                @endforeach
                            </select>

                            @error('id_kelompok_tani')
                                <div class="text-xs text-red-800 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Field Photo -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="foto" class="block text-sm font-medium text-gray-700">Photo</label>
                            <input type="file" 
                                   name="foto" 
                                   id="foto" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            
                            @error('foto')
                                <div class="text-xs text-red-800 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Field Alamat -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea name="alamat" 
                                      id="alamat" 
                                      cols="30" 
                                      rows="2" 
                                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('alamat', $petani->alamat ?? '') }}</textarea>
                            
                            @error('alamat')
                                <div class="text-xs text-red-800 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Field Status -->
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-2 space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" 
                                           name="status" 
                                           value="Y" 
                                           @checked(old('status', $petani->status ?? '') == 'Y')
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                                </label>
                                
                                <label class="inline-flex items-center">
                                    <input type="radio" 
                                           name="status" 
                                           value="N" 
                                           @checked(old('status', $petani->status ?? '') == 'N')
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Non-Aktif</span>
                                </label>
                            </div>

                            @error('status')
                                <div class="text-xs text-red-800 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <hr>

                <!-- Button Submit -->
                <div class="px-4 py-3 bg-white text-right sm:px-6">
                    <button type="submit" 
                            class="inline-flex justify-center w-24 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection