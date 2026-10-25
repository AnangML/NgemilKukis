@extends('layouts.admin')

@section('title', 'Manajemen Staf & Pengguna - NgemilKukis Control Center')

@section('content')
<div x-data="{
    createModalOpen: false,
    editModalOpen: false,
    selectedUser: {
        id: null,
        name: '',
        email: '',
        phone: '',
        role: 'seller'
    },
    openEditModal(user) {
        this.selectedUser = { ...user };
        this.editModalOpen = true;
    }
}" class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#2d180c] tracking-tight">Manajemen Staf & Pengguna</h1>
            <p class="text-sm text-[#6d4630] mt-1">Kelola akun staf internal (Penjual, Customer Service, Admin) dan hak akses operasional toko.</p>
        </div>

        <button @click="createModalOpen = true"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#b87d4b] hover:bg-[#a06b3f] text-white text-sm font-bold shadow-md hover:shadow-lg transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Staf Baru</span>
        </button>
    </div>

    <!-- Alert / Validation Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold space-y-1 shadow-2xs">
            @if(session('error'))
                <p>{{ session('error') }}</p>
            @endif
            @foreach($errors->all() as $err)
                <p class="text-xs">&bull; {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <!-- Role Filter Tabs & Search Bar -->
    <div class="bg-white rounded-2xl border border-[#ede7e2] p-4 shadow-sm space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold">
                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'staff'])) }}"
                   class="px-3 py-1.5 rounded-lg border transition {{ request('role', 'staff') === 'staff' ? 'bg-[#2d180c] text-white border-[#2d180c]' : 'bg-[#f6efe8] text-[#6d4630] border-transparent hover:border-[#ede7e2]' }}">
                    Semua Staf ({{ $roleCounts['staff'] }})
                </a>
                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'admin'])) }}"
                   class="px-3 py-1.5 rounded-lg border transition {{ request('role') === 'admin' ? 'bg-[#b87d4b] text-white border-[#b87d4b]' : 'bg-[#f6efe8] text-[#6d4630] border-transparent hover:border-[#ede7e2]' }}">
                    Super Admin ({{ $roleCounts['admin'] }})
                </a>
                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'seller'])) }}"
                   class="px-3 py-1.5 rounded-lg border transition {{ request('role') === 'seller' ? 'bg-blue-600 text-white border-blue-600' : 'bg-[#f6efe8] text-[#6d4630] border-transparent hover:border-[#ede7e2]' }}">
                    Penjual ({{ $roleCounts['seller'] }})
                </a>
                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'customer_service'])) }}"
                   class="px-3 py-1.5 rounded-lg border transition {{ request('role') === 'customer_service' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-[#f6efe8] text-[#6d4630] border-transparent hover:border-[#ede7e2]' }}">
                    Customer Service ({{ $roleCounts['customer_service'] }})
                </a>
                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'customer'])) }}"
                   class="px-3 py-1.5 rounded-lg border transition {{ request('role') === 'customer' ? 'bg-stone-700 text-white border-stone-700' : 'bg-[#f6efe8] text-[#6d4630] border-transparent hover:border-[#ede7e2]' }}">
                    Pelanggan ({{ $roleCounts['customer'] }})
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="role" value="{{ request('role', 'staff') }}">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, email, hp..." 
                           class="w-64 pl-9 pr-3 py-1.5 rounded-xl border border-[#ede7e2] text-xs focus:ring-2 focus:ring-[#b87d4b] focus:border-transparent outline-none">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-[#2d180c] text-white text-xs font-bold hover:bg-[#432311] transition">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.users.index', ['role' => request('role', 'staff')]) }}" class="text-xs text-rose-600 hover:underline">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-[#ede7e2] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#2d180c]">
                <thead class="bg-[#f6efe8] text-[#6d4630] text-xs uppercase font-extrabold tracking-wider border-b border-[#ede7e2]">
                    <tr>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Kontak (HP/Email)</th>
                        <th class="px-6 py-4">Peran (Role)</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ede7e2]">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#fcfaf7] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#2d180c] text-white font-black flex items-center justify-center text-sm shadow-2xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#2d180c] flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="text-[10px] font-extrabold px-1.5 py-0.2 bg-amber-100 text-amber-900 border border-amber-300 rounded">Anda</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-[#6d4630]">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-[#6d4630]">
                                <div>{{ $user->phone ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-extrabold border {{ $user->role_badge_classes }}">
                                    @if($user->role === 'admin') 👑
                                    @elseif($user->role === 'seller') 🛍️
                                    @elseif($user->role === 'customer_service') 🎧
                                    @else 👤
                                    @endif
                                    {{ $user->role_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-[#6d4630]">
                                <div>{{ $user->created_at?->format('d M Y, H:i') ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal({
                                                id: {{ $user->id }},
                                                name: '{{ addslashes($user->name) }}',
                                                email: '{{ addslashes($user->email) }}',
                                                phone: '{{ addslashes($user->phone ?? '') }}',
                                                role: '{{ $user->role }}'
                                            })"
                                            class="px-3 py-1.5 rounded-lg border border-[#ede7e2] hover:bg-[#f6efe8] text-xs font-bold text-[#6d4630] hover:text-[#2d180c] transition cursor-pointer">
                                        Edit
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ addslashes($user->name) }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-xs font-bold text-rose-600 transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-stone-500">
                                <svg class="w-12 h-12 mx-auto text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p class="font-bold text-sm">Tidak ada data pengguna ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-[#ede7e2] bg-[#fcfaf7]">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: Tambah Staf Baru -->
    <div x-show="createModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs">
        <div @click.away="createModalOpen = false" 
             class="bg-white rounded-3xl border border-[#ede7e2] shadow-2xl w-full max-w-lg p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-[#ede7e2] pb-4">
                <div>
                    <h3 class="text-lg font-black text-[#2d180c]">Tambah Staf Baru</h3>
                    <p class="text-xs text-[#6d4630] mt-0.5">Daftarkan akun staf baru untuk operasional toko.</p>
                </div>
                <button @click="createModalOpen = false" class="text-stone-400 hover:text-stone-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#2d180c] mb-1">Nama Lengkap Staf *</label>
                    <input type="text" name="name" required placeholder="Contoh: Rina Melati (Kasir/Penjual)"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-sm focus:ring-2 focus:ring-[#b87d4b] outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] mb-1">Email Login *</label>
                        <input type="email" name="email" required placeholder="staf@ngemilkukis.com"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-sm focus:ring-2 focus:ring-[#b87d4b] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] mb-1">Nomor WhatsApp</label>
                        <input type="text" name="phone" placeholder="0812xxxxxxxx"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-sm focus:ring-2 focus:ring-[#b87d4b] outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#2d180c] mb-1">Peran Staf (Role) *</label>
                    <select name="role" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-sm focus:ring-2 focus:ring-[#b87d4b] outline-none bg-white">
                        <option value="seller">Penjual (Kelola Stok & Pesanan Dapur)</option>
                        <option value="customer_service">Customer Service (Layanan Pelanggan & Pesanan)</option>
                        <option value="admin">Super Admin (Akses Penuh Sistem)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#2d180c] mb-1">Kata Sandi Awal * (Min 8 Karakter)</label>
                    <input type="password" name="password" required placeholder="Minimal 8 karakter..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-sm focus:ring-2 focus:ring-[#b87d4b] outline-none">
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#ede7e2]">
                    <button type="button" @click="createModalOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-bold text-[#6d4630] hover:bg-[#f6efe8] transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#b87d4b] hover:bg-[#a06b3f] text-white text-xs font-bold shadow-md transition">
                        Simpan Akun Staf
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Edit Staf -->
    <div x-show="editModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs">
        <div @click.away="editModalOpen = false" 
             class="bg-white rounded-3xl border border-[#ede7e2] shadow-2xl w-full max-w-lg p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-[#ede7e2] pb-4">
                <div>
                    <h3 class="text-lg font-black text-[#2d180c]">Edit Data Pengguna</h3>
                    <p class="text-xs text-[#6d4630] mt-0.5">Ubah nama, peran, kontak, atau atur ulang kata sandi.</p>
                </div>
                <button @click="editModalOpen = false" class="text-stone-400 hover:text-stone-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="'{{ url('/admin/users') }}/' + selectedUser.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-[#2d180c] mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" x-model="selectedUser.name" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-sm focus:ring-2 focus:ring-[#b87d4b] outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] mb-1">Email *</label>
                        <input type="email" name="email" x-model="selectedUser.email" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-sm focus:ring-2 focus:ring-[#b87d4b] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] mb-1">Nomor WhatsApp</label>
                        <input type="text" name="phone" x-model="selectedUser.phone"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-sm focus:ring-2 focus:ring-[#b87d4b] outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#2d180c] mb-1">Peran (Role) *</label>
                    <select name="role" x-model="selectedUser.role" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-sm focus:ring-2 focus:ring-[#b87d4b] outline-none bg-white">
                        <option value="seller">Penjual (Kelola Stok & Pesanan)</option>
                        <option value="customer_service">Customer Service (Layanan Pelanggan)</option>
                        <option value="admin">Super Admin (Akses Penuh)</option>
                        <option value="customer">Pelanggan Toko</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#2d180c] mb-1">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-sm focus:ring-2 focus:ring-[#b87d4b] outline-none">
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#ede7e2]">
                    <button type="button" @click="editModalOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-bold text-[#6d4630] hover:bg-[#f6efe8] transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#2d180c] hover:bg-[#432311] text-white text-xs font-bold shadow-md transition">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
