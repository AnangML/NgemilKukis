@extends('layouts.admin')

@section('title', 'Katalog Produk')
@section('page_title', 'Kelola Katalog Produk')

@section('content')
<div class="space-y-6">

    <!-- Top Toolbar: Search, Filter, CTA (Putih 60%, Tombol Coklat Muda 10%) -->
    <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Search & Filters -->
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama atau tagline..." 
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-[#ede7e2] text-xs text-[#2d180c] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 outline-none">
                <svg class="w-4 h-4 text-[#6d4630] absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Type Filter Tabs -->
            <div class="flex items-center gap-1.5 bg-[#f6efe8] p-1 rounded-xl text-xs font-bold text-[#6d4630]">
                <a href="{{ route('admin.products.index', array_merge(request()->except('type', 'page'))) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ !request('type') ? 'bg-white text-[#2d180c] shadow-xs' : 'hover:text-[#2d180c]' }}">
                    Semua
                </a>
                <a href="{{ route('admin.products.index', array_merge(request()->except('page'), ['type' => 'ready_stock'])) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ request('type') === 'ready_stock' ? 'bg-white text-[#2d180c] shadow-xs' : 'hover:text-[#2d180c]' }}">
                    Ready Stock
                </a>
                <a href="{{ route('admin.products.index', array_merge(request()->except('page'), ['type' => 'pre_order'])) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ request('type') === 'pre_order' ? 'bg-white text-[#2d180c] shadow-xs' : 'hover:text-[#2d180c]' }}">
                    Pre-Order
                </a>
            </div>

            @if(request('search') || request('type'))
                <a href="{{ route('admin.products.index') }}" class="text-xs text-[#b87d4b] hover:underline font-semibold">
                    Reset
                </a>
            @endif
        </form>

        <!-- Tambah Produk Button (10% Coklat Muda) -->
        <a href="{{ route('admin.products.create') }}" 
           class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Varian Baru</span>
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-3xl border border-[#ede7e2] shadow-xs overflow-hidden">
        @if($products->isEmpty())
            <div class="p-12 text-center text-[#6d4630]">
                <span class="text-4xl block mb-2">🍪</span>
                <p class="text-sm font-bold text-[#2d180c]">Tidak ada produk yang cocok dengan pencarian</p>
                <a href="{{ route('admin.products.create') }}" class="inline-block mt-3 text-xs font-bold text-[#b87d4b] hover:underline">
                    + Buat Produk Baru Sekarang
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-[#fcfaf8] text-[#6d4630] font-bold uppercase text-[10px] tracking-wider border-b border-[#ede7e2]">
                        <tr>
                            <th class="py-3.5 px-6">Produk</th>
                            <th class="py-3.5 px-6">Tipe Model</th>
                            <th class="py-3.5 px-6">Harga</th>
                            <th class="py-3.5 px-6">Stok</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ede7e2] text-[#2d180c]">
                        @foreach($products as $product)
                            <tr class="hover:bg-[#faf7f4] transition">
                                <!-- Image & Name -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-stone-100 flex-shrink-0 border border-[#ede7e2]">
                                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-bold text-[#2d180c] leading-tight">{{ $product->name }}</p>
                                            <p class="text-xs text-[#6d4630] line-clamp-1">{{ $product->tagline }}</p>
                                            @if($product->badge)
                                                <span class="inline-block mt-1 bg-amber-100 text-amber-900 text-[10px] font-extrabold px-2 py-0.5 rounded-full">
                                                    {{ $product->badge }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="py-4 px-6">
                                    @if($product->type === 'ready_stock')
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold px-2.5 py-1 rounded-full">
                                            <span>⚡ Ready Stock</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold px-2.5 py-1 rounded-full">
                                            <span>📅 Pre-Order</span>
                                        </span>
                                        @if($product->baking_schedule)
                                            <p class="text-[11px] text-[#6d4630] mt-1">{{ $product->baking_schedule }}</p>
                                        @endif
                                    @endif
                                </td>

                                <!-- Price -->
                                <td class="py-4 px-6">
                                    @if($product->sale_price)
                                        <p class="font-bold text-[#b87d4b]">{{ $product->formattedSalePrice() }}</p>
                                        <p class="text-xs text-[#6d4630] line-through">{{ $product->formattedPrice() }}</p>
                                    @else
                                        <p class="font-bold text-[#2d180c]">{{ $product->formattedPrice() }}</p>
                                    @endif
                                </td>

                                <!-- Stock -->
                                <td class="py-4 px-6">
                                    <span class="font-bold {{ $product->stock <= 5 ? 'text-rose-600' : 'text-[#2d180c]' }}">
                                        {{ $product->stock }} pcs
                                    </span>
                                </td>

                                <!-- Status (Toggleable) -->
                                <td class="py-4 px-6">
                                    <form method="POST" action="{{ route('admin.products.toggle', $product) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                title="Klik untuk ubah status aktif"
                                                class="cursor-pointer transition transform active:scale-95 inline-block text-[11px] font-bold px-2.5 py-1 rounded-full {{ $product->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                    @if($product->is_featured)
                                        <span class="block mt-1 text-[10px] font-bold text-[#b87d4b]">Unggulan</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="inline-flex items-center gap-1 bg-[#f6efe8] hover:bg-[#ede7e2] text-[#2d180c] text-xs font-bold px-3 py-1.5 rounded-lg transition"
                                           title="Edit Produk">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            <span>Edit</span>
                                        </a>

                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk \'{{ $product->name }}\'?');" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer"
                                                    title="Hapus Produk">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="p-6 border-t border-[#ede7e2]">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
