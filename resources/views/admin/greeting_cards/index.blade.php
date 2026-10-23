@extends('layouts.admin')

@section('title', 'Kelola Kartu Ucapan')
@section('page_title', 'Template Kartu Ucapan')

@section('content')
<div class="space-y-6">

    <!-- Header Actions Bar (Putih Bersih 60% dengan Tombol Coklat Muda 10%) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-[#2d180c]">Koleksi Kartu Kado</h2>
            <p class="text-xs sm:text-sm text-[#6d4630] mt-0.5">
                Template kartu ucapan kado yang dapat dipilih pelanggan saat memesan hampers kukis.
            </p>
        </div>

        <a href="{{ route('admin.greeting-cards.create') }}" 
           class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs sm:text-sm font-bold px-5 py-3 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Kartu Baru</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 border border-[#ede7e2] shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.greeting-cards.index') }}" class="w-full sm:w-80 relative">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari nama atau isi ucapan..." 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c] placeholder:text-stone-400">
            <svg class="w-4 h-4 text-stone-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </form>

        <span class="text-xs font-semibold text-[#6d4630]">
            Total {{ $cards->total() }} template kartu
        </span>
    </div>

    <!-- Cards Grid (Clean Design, 60-30-10) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($cards as $card)
            @php
                $colorClasses = match($card->theme_color) {
                    'amber' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'badge' => 'bg-amber-100 text-amber-900', 'dot' => 'bg-amber-500'],
                    'indigo' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'badge' => 'bg-indigo-100 text-indigo-900', 'dot' => 'bg-indigo-500'],
                    'rose' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'badge' => 'bg-rose-100 text-rose-900', 'dot' => 'bg-rose-500'],
                    'emerald' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'badge' => 'bg-emerald-100 text-emerald-900', 'dot' => 'bg-emerald-500'],
                    'pink' => ['bg' => 'bg-pink-50', 'border' => 'border-pink-200', 'badge' => 'bg-pink-100 text-pink-900', 'dot' => 'bg-pink-500'],
                    'teal' => ['bg' => 'bg-teal-50', 'border' => 'border-teal-200', 'badge' => 'bg-teal-100 text-teal-900', 'dot' => 'bg-teal-500'],
                    'violet' => ['bg' => 'bg-violet-50', 'border' => 'border-violet-200', 'badge' => 'bg-violet-100 text-violet-900', 'dot' => 'bg-violet-500'],
                    'orange' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'badge' => 'bg-orange-100 text-orange-900', 'dot' => 'bg-orange-500'],
                    default => ['bg' => 'bg-[#faf7f4]', 'border' => 'border-[#ede7e2]', 'badge' => 'bg-[#f6efe8] text-[#2d180c]', 'dot' => 'bg-[#b87d4b]'],
                };
            @endphp

            <div class="bg-white rounded-3xl border border-[#ede7e2] shadow-xs overflow-hidden flex flex-col justify-between hover:border-[#b87d4b]/40 transition">
                <div class="p-6 space-y-4">
                    <!-- Top Info: Theme & Status -->
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $colorClasses['badge'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $colorClasses['dot'] }}"></span>
                            {{ $card->theme_color }}
                        </span>

                        <form method="POST" action="{{ route('admin.greeting-cards.toggle', $card) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition cursor-pointer {{ $card->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-stone-100 text-stone-500 border border-stone-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $card->is_active ? 'bg-emerald-500' : 'bg-stone-400' }}"></span>
                                <span>{{ $card->is_active ? 'Aktif di Toko' : 'Nonaktif' }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Card Miniature Preview -->
                    <div class="{{ $colorClasses['bg'] }} p-5 rounded-2xl border {{ $colorClasses['border'] }} space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-xs text-[#2d180c] truncate">{{ $card->name }}</span>
                            <span class="text-xs font-bold text-[#b87d4b] flex-shrink-0">
                                {{ $card->price > 0 ? 'Rp ' . number_format($card->price, 0, ',', '.') : 'Gratis (Free)' }}
                            </span>
                        </div>
                        <p class="text-xs text-[#6d4630] italic line-clamp-3 leading-relaxed">
                            "{{ $card->default_message }}"
                        </p>
                    </div>

                    <div class="text-[11px] text-[#6d4630] flex items-center justify-between pt-1">
                        <span>Gaya: <strong class="text-[#2d180c]">{{ $card->design_style }}</strong></span>
                        <span>Slug: <code class="text-stone-500 bg-stone-50 px-1.5 py-0.5 rounded">{{ $card->slug }}</code></span>
                    </div>
                </div>

                <!-- Card Footer Actions -->
                <div class="p-4 bg-[#faf7f4] border-t border-[#ede7e2] flex items-center justify-between">
                    <a href="{{ route('admin.greeting-cards.edit', $card) }}" 
                       class="text-xs font-bold text-[#2d180c] hover:text-[#b87d4b] transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit Template</span>
                    </a>

                    <form method="POST" action="{{ route('admin.greeting-cards.destroy', $card) }}" 
                          onsubmit="return confirm('Hapus template kartu ini? Pelanggan tidak akan dapat memilihnya lagi.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="text-xs font-bold text-rose-600 hover:text-rose-800 transition flex items-center gap-1 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-[#ede7e2] space-y-3">
                <p class="text-base font-bold text-[#2d180c]">Belum ada template kartu ucapan</p>
                <p class="text-xs text-[#6d4630]">Tambahkan template pertama agar pelanggan dapat menyertakan kartu personal pada pesanan kado.</p>
                <a href="{{ route('admin.greeting-cards.create') }}" 
                   class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-5 py-2.5 rounded-full transition">
                    <span>Tambah Kartu Baru</span>
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($cards->hasPages())
        <div class="bg-white rounded-2xl p-4 border border-[#ede7e2]">
            {{ $cards->links() }}
        </div>
    @endif

</div>
@endsection
