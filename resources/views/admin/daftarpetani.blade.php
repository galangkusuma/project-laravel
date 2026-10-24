@extends('layouts.admin-layout')

@section('content')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
        {{ $title }}
    </h2>

    <div>
        <div class="shadow px-6 py-4 bg-white rounded sm:px-1 sm:py-1">
            <div class="grid grid-cols-12">
                <div class="col-span-6 p-4">
                    <a href="{{ route('petani.create') }}">
                        <button class="px-4 py-1 text-sm rounded text-purple-600 font-semibold border border-purple-200 hover:text-white hover:bg-purple-600 hover:border-transparent focus:outline-none">Tambah</button>
                    </a>
                    <button class="px-4 py-1 text-sm rounded text-green-600 font-semibold border border-green-200 hover:text-white hover:bg-green-600 hover:border-transparent focus:outline-none">Publish</button>
                    <button class="px-4 py-1 text-sm rounded text-red-600 font-semibold border border-red-200 hover:text-white hover:bg-red-600 hover:border-transparent focus:outline-none">Delete</button>
                </div>
                
                <form action="{{ route('petani.index') }}" method="GET" class="col-span-6 p-4 flex justify-end">
                    <label for="id_kelompok_tani" class="sr-only">Filter kelompok</label>
                    <select name="id_kelompok_tani" id="id_kelompok_tani" class="px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 border">
                        <option value="">Semua kelompok</option>
                        @foreach ($kelompoks as $kelompok)
                            <option value="{{ $kelompok->id_kelompok_tani }}" @selected(request('id_kelompok_tani') == $kelompok->id_kelompok_tani)>
                                {{ $kelompok->nama_kelompok }}
                            </option>
                        @endforeach
                    </select>
                    <label for="search" class="sr-only">Cari petani</label>
                    <input type="search" name="search" id="search" value="{{ request('search') }}" placeholder="Cari petani..." class="px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-none rounded-l-md sm:text-sm border-gray-300 border">
                    <button type="submit" class="rounded-r-md border border-l-0 px-4 py-1 border-gray-300 bg-gray-50 text-blue-600 hover:text-white hover:bg-blue-600">Cari</button>
                </form>
            </div>

            <div class="py-2 align-middle inline-block min-w-full sm:px-4 lg:px-4">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kontak
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kelompok
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($petanis as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="" id="" class="mr-2 rounded border-gray-300">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if ($item->foto)
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $item->foto) }}" alt="Foto {{ $item->nama }}">
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $item->nama }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $item->nik }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $item->telp }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->alamat }}</div>
                                    </td>
                                    
                                    
                                    {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->nama_kelompok }}
                                    </td> --}}
                                    
                                    {{-- tambahkan tampilan kelompok tani --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->kelompoktani->nama_kelompok }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('petani.destroy', $item->id_penjual) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            
                                            <a href="{{ route('petani.edit', $item->id_penjual) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                            
                                            <button class="text-red-600 hover:text-red-900" onclick="return confirm('Anda Yakin ?')" type="submit">Del</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada data petani yang sesuai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $petanis->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection