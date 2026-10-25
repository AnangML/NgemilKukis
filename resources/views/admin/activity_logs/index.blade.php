@extends('layouts.admin')

@section('title', 'Log Aktivitas Pengguna & Audit Trail - NgemilKukis Control Center')

@section('content')
<div x-data="{
    detailModalOpen: false,
    selectedLog: null,
    openDetail(log) {
        this.selectedLog = log;
        this.detailModalOpen = true;
    }
}" class="space-y-6">

    <!-- Header & Information -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#2d180c] tracking-tight">Log Aktivitas & Audit Trail</h1>
            <p class="text-sm text-[#6d4630] mt-1">Pantau seluruh aktivitas staf, pemrosesan pesanan, autentikasi pelanggan, dan perubahan sistem secara real-time.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Audit Logging Aktif
            </span>
        </div>
    </div>

    <!-- 4 Metric KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Hari Ini -->
        <div class="bg-white rounded-2xl border border-[#ede7e2] p-4.5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#6d4630] uppercase tracking-wider">Aktivitas Hari Ini</span>
                <span class="p-2 rounded-xl bg-amber-50 text-[#b87d4b]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black text-[#2d180c] mt-2">{{ number_format($stats['total_today']) }}</p>
            <p class="text-[11px] text-[#6d4630] mt-0.5">Total interaksi sistem terdata</p>
        </div>

        <!-- Autentikasi -->
        <div class="bg-white rounded-2xl border border-[#ede7e2] p-4.5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#6d4630] uppercase tracking-wider">Autentikasi</span>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black text-[#2d180c] mt-2">{{ number_format($stats['auth_today']) }}</p>
            <p class="text-[11px] text-[#6d4630] mt-0.5">Login/Logout/Daftar hari ini</p>
        </div>

        <!-- Pesanan -->
        <div class="bg-white rounded-2xl border border-[#ede7e2] p-4.5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#6d4630] uppercase tracking-wider">Aktivitas Pesanan</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black text-[#2d180c] mt-2">{{ number_format($stats['order_today']) }}</p>
            <p class="text-[11px] text-[#6d4630] mt-0.5">Transaksi & update status</p>
        </div>

        <!-- Tindakan Staf -->
        <div class="bg-white rounded-2xl border border-[#ede7e2] p-4.5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#6d4630] uppercase tracking-wider">Aksi Staf Internal</span>
                <span class="p-2 rounded-xl bg-purple-50 text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-black text-[#2d180c] mt-2">{{ number_format($stats['staff_actions_today']) }}</p>
            <p class="text-[11px] text-[#6d4630] mt-0.5">Oleh Admin, Penjual, dan CS</p>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white rounded-2xl border border-[#ede7e2] p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-2 relative">
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] }}" 
                       placeholder="Cari nama, email, order, atau teks aksi..." 
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-[#ede7e2] text-xs focus:ring-2 focus:ring-[#b87d4b] focus:border-transparent outline-none">
                <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Role Filter -->
            <div>
                <select name="role" class="w-full px-3 py-2 rounded-xl border border-[#ede7e2] text-xs focus:ring-2 focus:ring-[#b87d4b] outline-none bg-white">
                    <option value="all" {{ $filters['role'] === 'all' ? 'selected' : '' }}>Semua Peran (Role)</option>
                    <option value="admin" {{ $filters['role'] === 'admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="seller" {{ $filters['role'] === 'seller' ? 'selected' : '' }}>Penjual (Toko)</option>
                    <option value="customer_service" {{ $filters['role'] === 'customer_service' ? 'selected' : '' }}>Customer Service</option>
                    <option value="customer" {{ $filters['role'] === 'customer' ? 'selected' : '' }}>Pelanggan</option>
                    <option value="guest" {{ $filters['role'] === 'guest' ? 'selected' : '' }}>Tamu (Guest)</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <select name="category" class="w-full px-3 py-2 rounded-xl border border-[#ede7e2] text-xs focus:ring-2 focus:ring-[#b87d4b] outline-none bg-white">
                    <option value="all" {{ $filters['category'] === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="auth" {{ $filters['category'] === 'auth' ? 'selected' : '' }}>Autentikasi</option>
                    <option value="order" {{ $filters['category'] === 'order' ? 'selected' : '' }}>Pesanan & Transaksi</option>
                    <option value="catalog" {{ $filters['category'] === 'catalog' ? 'selected' : '' }}>Katalog & Produk</option>
                    <option value="settings" {{ $filters['category'] === 'settings' ? 'selected' : '' }}>Pengaturan Toko</option>
                    <option value="staff" {{ $filters['category'] === 'staff' ? 'selected' : '' }}>Manajemen Staf</option>
                </select>
            </div>

            <!-- Date Filter & Submit -->
            <div class="flex items-center gap-2">
                <select name="date" class="w-full px-3 py-2 rounded-xl border border-[#ede7e2] text-xs focus:ring-2 focus:ring-[#b87d4b] outline-none bg-white">
                    <option value="all" {{ $filters['date'] === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                    <option value="today" {{ $filters['date'] === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="yesterday" {{ $filters['date'] === 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                    <option value="week" {{ $filters['date'] === 'week' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="month" {{ $filters['date'] === 'month' ? 'selected' : '' }}>30 Hari Terakhir</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#2d180c] hover:bg-[#432311] text-white text-xs font-bold transition shrink-0">
                    Filter
                </button>
                @if($filters['search'] || $filters['role'] !== 'all' || $filters['category'] !== 'all' || $filters['date'] !== 'all')
                    <a href="{{ route('admin.activity-logs.index') }}" class="text-xs text-rose-600 hover:underline shrink-0 font-semibold">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Activity Log Table -->
    <div class="bg-white rounded-2xl border border-[#ede7e2] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#2d180c]">
                <thead class="bg-[#f6efe8] text-[#6d4630] text-xs uppercase font-extrabold tracking-wider border-b border-[#ede7e2]">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Pengguna & Peran</th>
                        <th class="px-6 py-4">Kategori & Aksi</th>
                        <th class="px-6 py-4">Deskripsi Aktivitas</th>
                        <th class="px-6 py-4">IP & Browser</th>
                        <th class="px-6 py-4 text-right">Data Diff</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ede7e2]">
                    @forelse($logs as $log)
                        <tr class="hover:bg-[#fcfaf7] transition">
                            <!-- Timestamp -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-bold text-[#2d180c]">{{ $log->created_at->format('H:i:s') }}</div>
                                <div class="text-[11px] text-[#6d4630]">{{ $log->created_at->format('d M Y') }}</div>
                            </td>

                            <!-- User & Role -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-[#f6efe8] border border-[#ede7e2] text-[#2d180c] font-bold text-[11px] flex items-center justify-center">
                                        {{ strtoupper(substr($log->user_name ?? 'G', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-xs text-[#2d180c] leading-tight">{{ $log->user_name }}</p>
                                        <span class="inline-block text-[9px] font-extrabold uppercase px-1.5 py-0.2 rounded border mt-0.5 {{ $log->role_badge_classes }}">
                                            @if($log->user_role === 'admin') 👑 Super Admin
                                            @elseif($log->user_role === 'seller') 🛍️ Penjual
                                            @elseif($log->user_role === 'customer_service') 🎧 CS
                                            @elseif($log->user_role === 'customer') 👤 Member
                                            @else 🌐 Tamu
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Category & Action -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-extrabold border uppercase {{ $log->category_badge_classes }}">
                                    {{ $log->category_label }}
                                </span>
                                <p class="text-[11px] font-mono text-[#6d4630] mt-0.5">{{ $log->action }}</p>
                            </td>

                            <!-- Description -->
                            <td class="px-6 py-4">
                                <p class="text-xs font-medium text-[#2d180c] max-w-md">{{ $log->description }}</p>
                            </td>

                            <!-- IP & Browser -->
                            <td class="px-6 py-4 text-[11px] text-[#6d4630] whitespace-nowrap">
                                <div class="font-mono text-stone-700">{{ $log->ip_address ?: '127.0.0.1' }}</div>
                                <div class="text-[10px] text-stone-400 truncate max-w-[140px]" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent ? Str::limit($log->user_agent, 20) : 'Browser' }}
                                </div>
                            </td>

                            <!-- Action Detail -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if(!empty($log->properties))
                                    <button @click="openDetail({{ json_encode($log) }})"
                                            class="px-2.5 py-1 rounded-lg bg-[#f6efe8] hover:bg-[#ede7e2] text-xs font-bold text-[#b87d4b] hover:text-[#2d180c] transition cursor-pointer">
                                        Lihat Diff
                                    </button>
                                @else
                                    <span class="text-[11px] text-stone-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-stone-500">
                                <svg class="w-12 h-12 mx-auto text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                <p class="font-bold text-sm">Tidak ada log aktivitas yang cocok dengan filter.</p>
                                <p class="text-xs text-stone-400 mt-1">Coba sesuaikan kata kunci pencarian atau ubah rentang tanggal.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-[#ede7e2] bg-[#fcfaf7]">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: Diff Data Detail -->
    <div x-show="detailModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs">
        <div @click.away="detailModalOpen = false" 
             class="bg-white rounded-3xl border border-[#ede7e2] shadow-2xl w-full max-w-xl p-6 space-y-4">
            
            <div class="flex items-center justify-between border-b border-[#ede7e2] pb-3">
                <div>
                    <h3 class="text-base font-black text-[#2d180c]">Detail Parameter & Perubahan Data</h3>
                    <p class="text-xs text-[#6d4630] mt-0.5" x-text="selectedLog?.action"></p>
                </div>
                <button @click="detailModalOpen = false" class="text-stone-400 hover:text-stone-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div class="bg-[#fcfaf7] border border-[#ede7e2] rounded-xl p-3 space-y-1">
                    <p class="font-bold text-[#2d180c]" x-text="selectedLog?.description"></p>
                    <p class="text-[#6d4630]">Oleh: <strong x-text="selectedLog?.user_name"></strong> (<span x-text="selectedLog?.user_role"></span>) pada <span x-text="selectedLog?.created_at"></span></p>
                </div>

                <div>
                    <span class="block text-[11px] font-bold text-[#6d4630] uppercase tracking-wider mb-1">Payload / Properties JSON</span>
                    <pre class="bg-stone-900 text-emerald-400 p-4 rounded-xl text-xs font-mono overflow-x-auto max-h-60"
                         x-text="JSON.stringify(selectedLog?.properties, null, 2)"></pre>
                </div>
            </div>

            <div class="pt-3 flex justify-end border-t border-[#ede7e2]">
                <button type="button" @click="detailModalOpen = false"
                        class="px-5 py-2 rounded-xl bg-[#2d180c] hover:bg-[#432311] text-white text-xs font-bold transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
