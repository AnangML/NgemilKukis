@extends('layouts.admin')

@section('title', 'Kupon Promo & Diskon - NgemilKukis Control Center')
@section('page_title', 'Kupon Promo & Diskon')

@section('content')
<div x-data="{
    createModalOpen: false,
    editModalOpen: false,
    selectedCoupon: {
        id: null,
        code: '',
        name: '',
        description: '',
        type: 'fixed',
        value: 10000,
        min_order_amount: null,
        max_discount_amount: null,
        usage_limit_total: null,
        usage_limit_per_user: 1,
        start_date: '',
        end_date: '',
        is_active: true,
        is_stackable_with_member: true
    },
    openEditModal(coupon) {
        this.selectedCoupon = {
            id: coupon.id,
            code: coupon.code,
            name: coupon.name,
            description: coupon.description || '',
            type: coupon.type,
            value: coupon.value,
            min_order_amount: coupon.min_order_amount,
            max_discount_amount: coupon.max_discount_amount,
            usage_limit_total: coupon.usage_limit_total,
            usage_limit_per_user: coupon.usage_limit_per_user || 1,
            start_date: coupon.start_date ? coupon.start_date.substring(0, 10) : '',
            end_date: coupon.end_date ? coupon.end_date.substring(0, 10) : '',
            is_active: Boolean(coupon.is_active),
            is_stackable_with_member: Boolean(coupon.is_stackable_with_member)
        };
        this.editModalOpen = true;
    }
}" class="space-y-6">

    <!-- Top Stats Row (Clean 60-30-10 Aesthetic) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#f6efe8] text-[#b87d4b] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-[#6d4630] uppercase tracking-wider">Total Kupon</p>
                <h3 class="text-2xl font-black text-[#2d180c] mt-0.5">{{ number_format($stats['total']) }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-[#6d4630] uppercase tracking-wider">Kupon Aktif</p>
                <h3 class="text-2xl font-black text-[#2d180c] mt-0.5">{{ number_format($stats['active']) }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#faf7f4] text-[#2d180c] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-[#6d4630] uppercase tracking-wider">Total Pemakaian</p>
                <h3 class="text-2xl font-black text-[#2d180c] mt-0.5">{{ number_format($stats['total_used']) }} <span class="text-xs font-semibold text-[#6d4630]">kali</span></h3>
            </div>
        </div>
    </div>

    <!-- Header Action Bar & Filter -->
    <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-[#2d180c]">Daftar Kupon & Kode Promo</h2>
            <p class="text-xs sm:text-sm text-[#6d4630] mt-0.5">
                Kelola voucher diskon toko, batasan pemakaian, dan aturan stacking dengan diskon membership.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="createModalOpen = true" 
                    class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs sm:text-sm font-bold px-5 py-3 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Buat Kupon Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white rounded-2xl p-4 border border-[#ede7e2] shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="w-full sm:w-80 relative">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari kode promo atau nama..." 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c] placeholder:text-stone-400">
            <svg class="w-4 h-4 text-stone-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </form>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('admin.coupons.index', array_merge(request()->query(), ['status' => ''])) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ !request('status') ? 'bg-[#2d180c] text-white' : 'bg-[#f6efe8] text-[#6d4630] hover:bg-[#ede7e2]' }}">
                Semua
            </a>
            <a href="{{ route('admin.coupons.index', array_merge(request()->query(), ['status' => 'active'])) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('status') === 'active' ? 'bg-[#2d180c] text-white' : 'bg-[#f6efe8] text-[#6d4630] hover:bg-[#ede7e2]' }}">
                Aktif
            </a>
            <a href="{{ route('admin.coupons.index', array_merge(request()->query(), ['status' => 'inactive'])) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('status') === 'inactive' ? 'bg-[#2d180c] text-white' : 'bg-[#f6efe8] text-[#6d4630] hover:bg-[#ede7e2]' }}">
                Nonaktif
            </a>
        </div>
    </div>

    <!-- Coupons Table -->
    <div class="bg-white rounded-3xl border border-[#ede7e2] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#ede7e2] bg-[#faf7f4] text-[#6d4630] text-[11px] font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Kupon & Promo</th>
                        <th class="py-4 px-4">Tipe & Nilai Diskon</th>
                        <th class="py-4 px-4">Syarat & Limit</th>
                        <th class="py-4 px-4">Periode</th>
                        <th class="py-4 px-4 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ede7e2] text-xs">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-[#faf7f4]/60 transition">
                            <!-- Kode & Nama -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-[#f6efe8] text-[#b87d4b] flex items-center justify-center font-black text-sm shrink-0 uppercase tracking-wider border border-[#ede7e2]">
                                        %
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-[#2d180c] font-mono tracking-wider bg-stone-100 text-stone-800 px-2 py-0.5 rounded-md border border-stone-200 text-xs">
                                                {{ $coupon->code }}
                                            </span>
                                            @if($coupon->is_stackable_with_member)
                                                <span class="inline-flex items-center text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200" title="Bisa digabung dengan diskon membership VIP">
                                                    + Member VIP
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200" title="Tidak dapat digabung dengan diskon membership VIP">
                                                    Standalone
                                                </span>
                                            @endif
                                        </div>
                                        <p class="font-bold text-[#2d180c] mt-1">{{ $coupon->name }}</p>
                                        @if($coupon->description)
                                            <p class="text-[11px] text-[#6d4630] line-clamp-1 mt-0.5">{{ $coupon->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Tipe & Nilai Diskon -->
                            <td class="py-4 px-4">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-extrabold {{ $coupon->type === 'percentage' ? 'bg-purple-50 text-purple-800 border border-purple-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                    {{ $coupon->formatted_value }}
                                </span>
                                @if($coupon->type === 'percentage' && $coupon->max_discount_amount)
                                    <p class="text-[11px] text-[#6d4630] mt-1">
                                        Maks. Rp {{ number_format($coupon->max_discount_amount, 0, ',', '.') }}
                                    </p>
                                @endif
                            </td>

                            <!-- Syarat & Limit -->
                            <td class="py-4 px-4 space-y-1">
                                <p class="text-[#2d180c] font-semibold">
                                    Min: <span class="text-[#6d4630]">{{ $coupon->min_order_amount > 0 ? 'Rp ' . number_format($coupon->min_order_amount, 0, ',', '.') : 'Tanpa Min.' }}</span>
                                </p>
                                <div class="text-[11px] text-[#6d4630] flex items-center gap-1.5">
                                    <span>Pakai: <strong class="text-[#2d180c]">{{ $coupon->used_count }}</strong>{{ $coupon->usage_limit_total ? ' / ' . $coupon->usage_limit_total : ' (unlimited)' }}</span>
                                </div>
                                <p class="text-[10px] text-stone-500">Maks {{ $coupon->usage_limit_per_user }}x / pelanggan</p>
                            </td>

                            <!-- Periode -->
                            <td class="py-4 px-4 text-[#6d4630] text-[11px]">
                                @if($coupon->start_date || $coupon->end_date)
                                    <div>{{ $coupon->start_date ? $coupon->start_date->format('d M Y') : 'Mulai sekarang' }}</div>
                                    <div class="text-stone-400">&darr;</div>
                                    <div>{{ $coupon->end_date ? $coupon->end_date->format('d M Y') : 'Selamanya' }}</div>
                                @else
                                    <span class="text-stone-500 font-medium">Berlaku Selamanya</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4 text-center">
                                <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition cursor-pointer {{ $coupon->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100' : 'bg-stone-100 text-stone-500 border border-stone-200 hover:bg-stone-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $coupon->is_active ? 'bg-emerald-500' : 'bg-stone-400' }}"></span>
                                        <span>{{ $coupon->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal({{ json_encode($coupon) }})" 
                                            class="p-2 text-[#6d4630] hover:text-[#b87d4b] hover:bg-[#f6efe8] rounded-xl transition cursor-pointer"
                                            title="Edit Kupon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" 
                                          onsubmit="return confirm('Hapus kupon {{ $coupon->code }} secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 text-[#6d4630] hover:text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer"
                                                title="Hapus Kupon">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-[#6d4630]">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-[#f6efe8] text-[#b87d4b] flex items-center justify-center mx-auto text-xl">
                                        🎟️
                                    </div>
                                    <p class="font-bold text-sm text-[#2d180c]">Belum ada kupon promo</p>
                                    <p class="text-xs text-stone-500">Buat kode kupon pertama Anda untuk menarik lebih banyak pesanan dari pelanggan setia NgemilKukis.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
            <div class="p-4 border-t border-[#ede7e2]">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Buat Kupon Baru (Alpine.js) -->
    <div x-show="createModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
         @keydown.escape.window="createModalOpen = false">
        <div @click.away="createModalOpen = false" 
             class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full border border-[#ede7e2] shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between pb-4 border-b border-[#ede7e2]">
                <div>
                    <h3 class="text-lg font-black text-[#2d180c]">Buat Kupon Promo Baru</h3>
                    <p class="text-xs text-[#6d4630] mt-0.5">Tentukan kode, potongan harga, dan batasan penggunaan.</p>
                </div>
                <button @click="createModalOpen = false" class="text-stone-400 hover:text-[#2d180c] p-1.5 rounded-xl hover:bg-[#f6efe8] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kode Promo -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Kode Promo *</label>
                        <input type="text" 
                               name="code" 
                               required 
                               placeholder="Cth: KUKISMANIS" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-mono font-bold uppercase focus:border-[#b87d4b] outline-none">
                    </div>

                    <!-- Nama Kupon -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Nama Kupon *</label>
                        <input type="text" 
                               name="name" 
                               required 
                               placeholder="Cth: Diskon Promo Launching" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Deskripsi Singkat (Opsional)</label>
                    <textarea name="description" 
                              rows="2" 
                              placeholder="Deskripsi promo yang tampil ke pembeli..." 
                              class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none"></textarea>
                </div>

                <div x-data="{ couponType: 'fixed' }" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tipe Diskon -->
                        <div>
                            <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Tipe Diskon *</label>
                            <select name="type" 
                                    x-model="couponType" 
                                    required 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-bold focus:border-[#b87d4b] outline-none bg-white">
                                <option value="fixed">Nominal Rupiah (Rp)</option>
                                <option value="percentage">Persentase (%)</option>
                            </select>
                        </div>

                        <!-- Nilai Diskon -->
                        <div>
                            <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5" x-text="couponType === 'fixed' ? 'Nominal Potongan (Rp) *' : 'Persentase Potongan (%) *'"></label>
                            <input type="number" 
                                   name="value" 
                                   required 
                                   min="1" 
                                   placeholder="Cth: 15000" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-bold focus:border-[#b87d4b] outline-none">
                        </div>
                    </div>

                    <!-- Max Discount Cap (for percentage) -->
                    <div x-show="couponType === 'percentage'" x-cloak>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Maksimal Potongan Diskon (Rp, Opsional)</label>
                        <input type="number" 
                               name="max_discount_amount" 
                               min="0" 
                               placeholder="Cth: 50000 (biarkan kosong jika tanpa batas)" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Min Order Amount -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Min. Belanja (Rp)</label>
                        <input type="number" 
                               name="min_order_amount" 
                               min="0" 
                               placeholder="0 = Tanpa min" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>

                    <!-- Total Usage Limit -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Total Kuota Kupon</label>
                        <input type="number" 
                               name="usage_limit_total" 
                               min="1" 
                               placeholder="Kosong = Unlimited" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>

                    <!-- Per User Limit -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Batas per User *</label>
                        <input type="number" 
                               name="usage_limit_per_user" 
                               min="1" 
                               value="1" 
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-bold focus:border-[#b87d4b] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tanggal Mulai -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Tanggal Mulai (Opsional)</label>
                        <input type="date" 
                               name="start_date" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Tanggal Berakhir (Opsional)</label>
                        <input type="date" 
                               name="end_date" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>
                </div>

                <!-- Toggles: Stackable & Active -->
                <div class="pt-2 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_stackable_with_member" value="1" checked class="w-4 h-4 text-[#b87d4b] rounded border-[#ede7e2] focus:ring-[#b87d4b]">
                        <div>
                            <span class="text-xs font-bold text-[#2d180c]">Boleh digabung dengan Diskon Member VIP</span>
                            <p class="text-[10px] text-[#6d4630]">Jika dimatikan, saat kupon ini dipakai diskon member otomatis tidak berlaku.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-[#b87d4b] rounded border-[#ede7e2] focus:ring-[#b87d4b]">
                        <div>
                            <span class="text-xs font-bold text-[#2d180c]">Aktifkan Kupon Langsung</span>
                            <p class="text-[10px] text-[#6d4630]">Pelanggan dapat langsung memasukkan kode ini di halaman checkout.</p>
                        </div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#ede7e2]">
                    <button type="button" @click="createModalOpen = false" class="px-5 py-2.5 rounded-xl bg-[#f6efe8] text-[#6d4630] font-bold text-xs hover:bg-[#ede7e2] transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-xs shadow-sm transition cursor-pointer">
                        Simpan Kupon
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kupon (Alpine.js) -->
    <div x-show="editModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
         @keydown.escape.window="editModalOpen = false">
        <div @click.away="editModalOpen = false" 
             class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full border border-[#ede7e2] shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between pb-4 border-b border-[#ede7e2]">
                <div>
                    <h3 class="text-lg font-black text-[#2d180c]">Edit Kupon Promo</h3>
                    <p class="text-xs text-[#6d4630] mt-0.5">Perbarui aturan diskon atau masa berlaku promo.</p>
                </div>
                <button @click="editModalOpen = false" class="text-stone-400 hover:text-[#2d180c] p-1.5 rounded-xl hover:bg-[#f6efe8] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" :action="`/admin/coupons/${selectedCoupon.id}`" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kode Promo -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Kode Promo *</label>
                        <input type="text" 
                               name="code" 
                               x-model="selectedCoupon.code" 
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-mono font-bold uppercase focus:border-[#b87d4b] outline-none">
                    </div>

                    <!-- Nama Kupon -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Nama Kupon *</label>
                        <input type="text" 
                               name="name" 
                               x-model="selectedCoupon.name" 
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                    <textarea name="description" 
                              x-model="selectedCoupon.description" 
                              rows="2" 
                              class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tipe Diskon -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Tipe Diskon *</label>
                        <select name="type" 
                                x-model="selectedCoupon.type" 
                                required 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-bold focus:border-[#b87d4b] outline-none bg-white">
                            <option value="fixed">Nominal Rupiah (Rp)</option>
                            <option value="percentage">Persentase (%)</option>
                        </select>
                    </div>

                    <!-- Nilai Diskon -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5" x-text="selectedCoupon.type === 'fixed' ? 'Nominal Potongan (Rp) *' : 'Persentase Potongan (%) *'"></label>
                        <input type="number" 
                               name="value" 
                               x-model="selectedCoupon.value" 
                               required 
                               min="1" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-bold focus:border-[#b87d4b] outline-none">
                    </div>
                </div>

                <!-- Max Discount Cap (for percentage) -->
                <div x-show="selectedCoupon.type === 'percentage'" x-cloak>
                    <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Maksimal Potongan Diskon (Rp, Opsional)</label>
                    <input type="number" 
                           name="max_discount_amount" 
                           x-model="selectedCoupon.max_discount_amount" 
                           min="0" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Min Order Amount -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Min. Belanja (Rp)</label>
                        <input type="number" 
                               name="min_order_amount" 
                               x-model="selectedCoupon.min_order_amount" 
                               min="0" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>

                    <!-- Total Usage Limit -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Total Kuota Kupon</label>
                        <input type="number" 
                               name="usage_limit_total" 
                               x-model="selectedCoupon.usage_limit_total" 
                               min="1" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>

                    <!-- Per User Limit -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Batas per User *</label>
                        <input type="number" 
                               name="usage_limit_per_user" 
                               x-model="selectedCoupon.usage_limit_per_user" 
                               min="1" 
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-bold focus:border-[#b87d4b] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tanggal Mulai -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Tanggal Mulai</label>
                        <input type="date" 
                               name="start_date" 
                               x-model="selectedCoupon.start_date" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label class="block text-xs font-bold text-[#2d180c] uppercase tracking-wider mb-1.5">Tanggal Berakhir</label>
                        <input type="date" 
                               name="end_date" 
                               x-model="selectedCoupon.end_date" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] text-xs focus:border-[#b87d4b] outline-none">
                    </div>
                </div>

                <!-- Toggles: Stackable & Active -->
                <div class="pt-2 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_stackable_with_member" value="1" x-model="selectedCoupon.is_stackable_with_member" class="w-4 h-4 text-[#b87d4b] rounded border-[#ede7e2] focus:ring-[#b87d4b]">
                        <div>
                            <span class="text-xs font-bold text-[#2d180c]">Boleh digabung dengan Diskon Member VIP</span>
                            <p class="text-[10px] text-[#6d4630]">Jika dimatikan, saat kupon ini dipakai diskon member otomatis tidak berlaku.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="selectedCoupon.is_active" class="w-4 h-4 text-[#b87d4b] rounded border-[#ede7e2] focus:ring-[#b87d4b]">
                        <div>
                            <span class="text-xs font-bold text-[#2d180c]">Kupon Aktif</span>
                            <p class="text-[10px] text-[#6d4630]">Pelanggan dapat menggunakan kode ini saat checkout.</p>
                        </div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#ede7e2]">
                    <button type="button" @click="editModalOpen = false" class="px-5 py-2.5 rounded-xl bg-[#f6efe8] text-[#6d4630] font-bold text-xs hover:bg-[#ede7e2] transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-xs shadow-sm transition cursor-pointer">
                        Perbarui Kupon
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
