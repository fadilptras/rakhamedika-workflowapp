@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">{{ $pageTitle }}</h1>
        <button id="open-add-modal-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition-transform duration-200 hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Akun
        </button>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md relative mb-4 shadow-sm" role="alert">
            <p class="font-bold">Sukses!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md relative mb-4 shadow-sm" role="alert">
            <strong class="font-bold">Oops! Terjadi kesalahan.</strong>
            <ul class="list-disc ml-5 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="border-b-2 border-slate-200 bg-slate-50 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        <th class="px-5 py-3">Pengguna</th>
                        <th class="px-5 py-3">Jabatan</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3">Tgl Bergabung</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-slate-200 hover:bg-slate-50">
                        <td class="px-5 py-4 text-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <img class="w-full h-full rounded-full object-cover" 
                                         src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random&color=fff' }}" 
                                         alt="Foto profil {{ $user->name }}">
                                </div>
                                <div class="ml-4">
                                    <p class="text-slate-900 whitespace-no-wrap font-semibold">{{ $user->name }}</p>
                                    <p class="text-slate-600 whitespace-no-wrap">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            <p class="text-slate-900 whitespace-no-wrap">{{ $user->jabatan ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm">
                             @if($user->role == 'admin')
                                <span class="relative inline-block px-3 py-1 font-semibold text-blue-900 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-blue-200 opacity-50 rounded-full"></span>
                                    <span class="relative">Admin</span>
                                </span>
                            @else
                                <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                    <span class="relative">User</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm">
                            <p class="text-slate-900 whitespace-no-wrap">
                                {{ $user->tanggal_bergabung ? \Carbon\Carbon::parse($user->tanggal_bergabung)->isoFormat('DD MMMM YYYY') : '-' }}
                            </p>
                        </td>
                        <td class="px-5 py-4 text-sm text-center">
                            <div class="flex item-center justify-center gap-4">
                                <button class="open-edit-modal-btn text-yellow-600 hover:text-yellow-900 transition-colors duration-200" data-user='{{ $user->toJson() }}' title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                                </button>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-slate-500">Tidak ada data akun ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         <div class="px-5 py-4 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
             {{ $users->links() }}
         </div>
    </div>
@endsection