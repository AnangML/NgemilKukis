@extends('layouts.admin')

@section('title', 'Tambah Kartu Ucapan')
@section('page_title', 'Tambah Template Kartu')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    cardName: '{{ old('name', 'Happy Birthday Sweet Moments') }}',
    cardColor: '{{ old('theme_color', 'amber') }}',
    designStyle: '{{ old('design_style', 'birthday_confetti') }}',
    defaultMessage: `{{ old('default_message', 'Selamat ulang tahun! Semoga harimu semanis dan sehangat kukis ini, selalu dilimpahi kesehatan dan kebahagiaan.') }}`,
    cardPrice: '{{ old('price', 0) }}',
    isActive: {{ old('is_active', 1) ? 'true' : 'false' }},

    getColorBg(color) {
        const maps = {
            'amber': 'bg-amber-50 border-amber-200',
            'indigo': 'bg-indigo-50 border-indigo-200',
            'rose': 'bg-rose-50 border-rose-200',
            'emerald': 'bg-emerald-50 border-emerald-200',
            'pink': 'bg-pink-50 border-pink-200',
            'teal': 'bg-teal-50 border-teal-200',
            'violet': 'bg-violet-50 border-violet-200',
            'orange': 'bg-orange-50 border-orange-200',
            'stone': 'bg-stone-50 border-stone-200'
        };
        return maps[color] || 'bg-[#faf7f4] border-[#ede7e2]';
    }
}">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.greeting-cards.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-[#6d4630] hover:text-[#2d180c] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Kartu</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.greeting-cards.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Form Fields (7 cols) -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-[#2d180c]">Formulir Template Kartu</h3>
                    <p class="text-xs text-[#6d4630] mt-0.5">Lengkapi informasi kartu ucapan personal untuk kado kukis.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Nama Template Kartu <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           x-model="cardName"
                           required
                           placeholder="Contoh: Happy Birthday Special Edition" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Theme Color Selector -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Warna Tema Kartu <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-2.5">
                        @foreach(['amber', 'indigo', 'rose', 'emerald', 'pink', 'teal', 'violet', 'orange', 'stone'] as $color)
                            <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border cursor-pointer transition text-center"
                                   :class="cardColor === '{{ $color }}' ? 'border-[#b87d4b] bg-[#f6efe8] ring-2 ring-[#b87d4b]' : 'border-[#ede7e2] hover:bg-stone-50'">
                                <input type="radio" 
                                       name="theme_color" 
                                       value="{{ $color }}" 
                                       x-model="cardColor" 
                                       class="sr-only">
                                <span class="w-4 h-4 rounded-full mb-1" 
                                      style="background-color: {{ match($color) {
                                          'amber' => '#f59e0b',
                                          'indigo' => '#6366f1',
                                          'rose' => '#f43f5e',
                                          'emerald' => '#10b981',
                                          'pink' => '#ec4899',
                                          'teal' => '#14b8a6',
                                          'violet' => '#8b5cf6',
                                          'orange' => '#f97316',
                                          default => '#78716c'
                                      } }};"></span>
                                <span class="text-[11px] font-bold capitalize text-[#2d180c]">{{ $color }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('theme_color')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                            Gaya Desain (Style Key) <span class="text-rose-500">*</span>
                        </label>
                        <select name="design_style" 
                                x-model="designStyle"
                                class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c] bg-white">
                            <option value="birthday_confetti">birthday_confetti</option>
                            <option value="graduation_hat">graduation_hat</option>
                            <option value="floral_thankyou">floral_thankyou</option>
                            <option value="cozy_cookie">cozy_cookie</option>
                            <option value="romantic_love">romantic_love</option>
                            <option value="elegant_gold">elegant_gold</option>
                            <option value="festive_holiday">festive_holiday</option>
                        </select>
                        @error('design_style')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                            Biaya Tambahan (Rp)
                        </label>
                        <input type="number" 
                               name="price" 
                               x-model="cardPrice"
                               min="0" step="1000"
                               placeholder="0 (Gratis)" 
                               class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm font-bold text-[#2d180c]">
                        <p class="text-[11px] text-[#6d4630] mt-1">Masukkan 0 jika ingin digratiskan untuk kado.</p>
                        @error('price')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Teks Contoh Ucapan Default <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="default_message" 
                              x-model="defaultMessage"
                              rows="4" 
                              required
                              placeholder="Tuliskan ucapan bawaan yang manis dan hangat..." 
                              class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c] leading-relaxed"></textarea>
                    @error('default_message')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Checkbox -->
                <div class="bg-[#faf7f4] p-4 rounded-2xl border border-[#ede7e2]">
                    <label class="flex items-center gap-3 cursor-pointer text-sm font-bold text-[#2d180c]">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               x-model="isActive"
                               class="rounded border-[#ede7e2] text-[#b87d4b] focus:ring-[#b87d4b]">
                        <span>Aktifkan Template Kartu Ini di Halaman Beranda Pelanggan</span>
                    </label>
                </div>
            </div>

            <!-- Right Preview Panel (5 cols) -->
            <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-8">
                <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs space-y-4">
                    <div class="border-b border-[#ede7e2] pb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#b87d4b]">Live Preview</span>
                        <h4 class="text-sm font-extrabold text-[#2d180c]">Tampilan di Halaman Pelanggan</h4>
                    </div>

                    <!-- Simulated Greeting Card Preview -->
                    <div :class="getColorBg(cardColor)" class="p-6 rounded-2xl border transition duration-300 space-y-4">
                        <div class="flex items-center justify-between border-b border-black/10 pb-3">
                            <span class="text-xs font-extrabold text-[#2d180c]" x-text="cardName || 'Nama Kartu'"></span>
                            <span class="text-xs font-bold text-[#b87d4b]" 
                                  x-text="cardPrice > 0 ? 'Rp ' + Number(cardPrice).toLocaleString('id-ID') : 'Gratis'"></span>
                        </div>

                        <div class="space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630]">Untuk: Sahabat Tersayang</span>
                            <p class="text-xs text-[#2d180c] italic leading-relaxed" 
                               x-text="'\'' + (defaultMessage || 'Pesan ucapan...') + '\''"></p>
                            <span class="text-[10px] font-bold text-[#6d4630] block pt-1">Dari: Teman Manismu</span>
                        </div>
                    </div>

                    <p class="text-[11px] text-[#6d4630] text-center leading-relaxed">
                        Pelanggan dapat mempersonalisasi nama penerima, nama pengirim, dan isi pesan ini sebelum checkout.
                    </p>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs">
                    <button type="submit" 
                            class="w-full bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-sm py-4 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Template Kartu</span>
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
