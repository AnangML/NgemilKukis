@extends('layouts.admin')

@section('title', 'Pengaturan Tampilan Dinamis')
@section('page_title', 'Kelola Tampilan & Konten Dinamis')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ 
    currentTab: 'general',
    heroImageMode: 'file',
    heroPreviewUrl: '{{ $settings['hero_image_url']->value ?? 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1000&q=80' }}',
    previewHeroImage(event) {
        const file = event.target.files[0];
        if (file) {
            this.heroPreviewUrl = URL.createObjectURL(file);
        }
    }
}">

    <!-- Top Info Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div>
            <h2 class="text-xl font-extrabold text-[#2d180c]">Konten Toko Dinamis</h2>
            <p class="text-xs sm:text-sm text-[#6d4630] mt-1 leading-relaxed">
                Seluruh elemen visual, teks promo, pengumuman bar atas, hero banner, tarif pengiriman, panduan hangat kukis, jadwal baking PO, hingga kontak WhatsApp dapat diperbarui langsung dari halaman ini tanpa perlu deploy kode baru.
            </p>
        </div>
        <a href="{{ route('home') }}" target="_blank" 
           class="inline-flex items-center gap-2 bg-[#f6efe8] hover:bg-[#ede7e2] text-[#2d180c] text-xs font-bold px-4 py-2.5 rounded-full border border-[#ede7e2] transition flex-shrink-0">
            <span>Pratinjau Toko</span>
            <svg class="w-4 h-4 text-[#b87d4b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </a>
    </div>

    <!-- Clean Tabbed Navigation (Standard 60-30-10, no emoji clutter) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <button type="button" 
                @click="currentTab = 'general'" 
                :class="currentTab === 'general' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            Brand & Identitas
        </button>
        <button type="button" 
                @click="currentTab = 'announcement'" 
                :class="currentTab === 'announcement' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            Pengumuman Atas
        </button>
        <button type="button" 
                @click="currentTab = 'hero'" 
                :class="currentTab === 'hero' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            Hero Banner Utama
        </button>
        <button type="button" 
                @click="currentTab = 'shipping'" 
                :class="currentTab === 'shipping' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            Tarif Pengiriman
        </button>
        <button type="button" 
                @click="currentTab = 'guide'" 
                :class="currentTab === 'guide' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            Panduan & Tips
        </button>
        <button type="button" 
                @click="currentTab = 'preorder'" 
                :class="currentTab === 'preorder' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            Jadwal Pre-Order
        </button>
        <button type="button" 
                @click="currentTab = 'contact'" 
                :class="currentTab === 'contact' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            Kontak & CS
        </button>
    </div>

    <!-- Main Settings Form -->
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- TAB 1: General & Brand -->
        <div x-show="currentTab === 'general'" class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs space-y-6">
            <div class="border-b border-[#ede7e2] pb-4">
                <h3 class="text-base font-extrabold text-[#2d180c]">Identitas Toko & Brand</h3>
                <p class="text-xs text-[#6d4630] mt-0.5">Nama brand utama, tagline artisan, dan deskripsi singkat tentang toko.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Nama Toko (Brand Name)
                    </label>
                    <input type="text" 
                           name="settings[site_name]" 
                           value="{{ old('settings.site_name', $settings['site_name']->value ?? 'NgemilKukis') }}" 
                           required
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Tagline Toko
                    </label>
                    <input type="text" 
                           name="settings[site_tagline]" 
                           value="{{ old('settings.site_tagline', $settings['site_tagline']->value ?? 'Artisan Fresh Bake') }}" 
                           required
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                    Deskripsi Singkat Toko (Tentang / Footer)
                </label>
                <textarea name="settings[about_text]" 
                          rows="3" 
                          class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c] leading-relaxed">{{ old('settings.about_text', $settings['about_text']->value ?? 'Toko kukis artisan online dengan mentega Prancis murni & cokelat Belgia terbaik. Dipanggang fresh setiap hari dengan penuh kehangatan.') }}</textarea>
            </div>
        </div>

        <!-- TAB 2: Announcement Bar -->
        <div x-show="currentTab === 'announcement'" class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs space-y-6">
            <div class="border-b border-[#ede7e2] pb-4">
                <h3 class="text-base font-extrabold text-[#2d180c]">Pengumuman Bar Atas (Announcement Bar)</h3>
                <p class="text-xs text-[#6d4630] mt-0.5">Pita promosi bergerak di bagian paling atas halaman situs.</p>
            </div>

            <div class="bg-[#faf7f4] p-4 rounded-2xl border border-[#ede7e2]">
                <label class="flex items-center gap-3 cursor-pointer text-sm font-bold text-[#2d180c]">
                    <input type="hidden" name="settings[announcement_active]" value="0">
                    <input type="checkbox" 
                           name="settings[announcement_active]" 
                           value="1" 
                           {{ ($settings['announcement_active']->value ?? '1') == '1' ? 'checked' : '' }}
                           class="rounded border-[#ede7e2] text-[#b87d4b] focus:ring-[#b87d4b]">
                    <span>Aktifkan Announcement Bar di Bagian Paling Atas</span>
                </label>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                    Teks Pengumuman Promo
                </label>
                <input type="text" 
                       name="settings[announcement_text]" 
                       value="{{ old('settings.announcement_text', $settings['announcement_text']->value ?? 'Freshly Baked Today! Gratis Custom Greeting Card untuk setiap pemesanan Box of 4 & 6.') }}" 
                       class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Teks Tombol Aksi
                    </label>
                    <input type="text" 
                           name="settings[announcement_link_text]" 
                           value="{{ old('settings.announcement_link_text', $settings['announcement_link_text']->value ?? 'Pesan Sekarang →') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Tautan Aksi (URL / Anchor)
                    </label>
                    <input type="text" 
                           name="settings[announcement_link]" 
                           value="{{ old('settings.announcement_link', $settings['announcement_link']->value ?? '#katalog') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>
            </div>
        </div>

        <!-- TAB 3: Hero Section Banner -->
        <div x-show="currentTab === 'hero'" class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs space-y-6">
            <div class="border-b border-[#ede7e2] pb-4">
                <h3 class="text-base font-extrabold text-[#2d180c]">Hero Banner Utama</h3>
                <p class="text-xs text-[#6d4630] mt-0.5">Judul besar, gambar sorotan utama, dan 3 badge kepercayaan pelanggan.</p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                    Teks Badge Kecil di Atas Judul
                </label>
                <input type="text" 
                       name="settings[hero_badge]" 
                       value="{{ old('settings.hero_badge', $settings['hero_badge']->value ?? 'Dipanggang Segar Setiap Hari • 100% Belgian Chocolate') }}" 
                       class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Headline Utama (Baris 1)
                    </label>
                    <input type="text" 
                           name="settings[hero_headline]" 
                           value="{{ old('settings.hero_headline', $settings['hero_headline']->value ?? 'Kukis Renyah di Luar,') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Sub Headline Sorotan (Baris 2 Warna Aksen)
                    </label>
                    <input type="text" 
                           name="settings[hero_subheadline]" 
                           value="{{ old('settings.hero_subheadline', $settings['hero_subheadline']->value ?? 'Lumer Manis di Dalam!') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                    Paragraf Penjelasan Hero
                </label>
                <textarea name="settings[hero_description]" 
                          rows="3" 
                          class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c] leading-relaxed">{{ old('settings.hero_description', $settings['hero_description']->value ?? 'Dibuat tangan dengan mentega Prancis autentik dan lelehan cokelat Belgia murni. Nikmati sensasi kukis hangat untuk teman ngopi, atau kirim sebagai Hampers Kado dengan Custom Kartu Ucapan personal.') }}</textarea>
            </div>

            <!-- Dual Image Option (Upload File or URL) -->
            <div class="bg-[#faf7f4] p-5 rounded-2xl border border-[#ede7e2] space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#2d180c]">Foto Hero Banner</h4>
                        <p class="text-[11px] text-[#6d4630] mt-0.5">Unggah foto kukis berkualitas tinggi atau gunakan tautan URL eksternal.</p>
                    </div>
                    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-[#ede7e2]">
                        <button type="button" 
                                @click="heroImageMode = 'file'" 
                                :class="heroImageMode === 'file' ? 'bg-[#b87d4b] text-white font-bold' : 'text-[#6d4630] hover:text-[#2d180c]'"
                                class="px-3 py-1 rounded-lg text-xs transition cursor-pointer">
                            Upload File
                        </button>
                        <button type="button" 
                                @click="heroImageMode = 'url'" 
                                :class="heroImageMode === 'url' ? 'bg-[#b87d4b] text-white font-bold' : 'text-[#6d4630] hover:text-[#2d180c]'"
                                class="px-3 py-1 rounded-lg text-xs transition cursor-pointer">
                            Link URL
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <div class="md:col-span-2 space-y-3">
                        <!-- Mode 1: File Upload -->
                        <div x-show="heroImageMode === 'file'">
                            <input type="file" 
                                   name="hero_image_file" 
                                   accept="image/png,image/jpeg,image/webp,image/jpg"
                                   @change="previewHeroImage($event)"
                                   class="block w-full text-xs text-[#6d4630] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#b87d4b] file:text-white hover:file:bg-[#a36b3b] file:cursor-pointer">
                            <p class="text-[11px] text-[#6d4630] mt-1.5">Format JPG, PNG, WEBP. Maksimal ukuran 5 MB.</p>
                        </div>

                        <!-- Mode 2: Direct URL -->
                        <div x-show="heroImageMode === 'url'">
                            <input type="url" 
                                   name="settings[hero_image_url]" 
                                   x-model="heroPreviewUrl"
                                   placeholder="https://images.unsplash.com/..." 
                                   class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c] bg-white">
                        </div>
                    </div>

                    <!-- Live Image Preview Card -->
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-full h-32 rounded-2xl overflow-hidden border border-[#ede7e2] bg-white shadow-xs relative">
                            <img :src="heroPreviewUrl" 
                                 alt="Hero Preview" 
                                 class="w-full h-full object-cover object-center">
                        </div>
                        <span class="text-[10px] text-[#6d4630] font-semibold mt-1.5">Pratinjau Banner Hero</span>
                    </div>
                </div>
            </div>

            <!-- Trust Badges 1-3 -->
            <div class="border-t border-[#ede7e2] pt-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-3">3 Badge Kepercayaan Pelanggan</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Badge 1</label>
                        <input type="text" 
                               name="settings[hero_trust_badge_1]" 
                               value="{{ old('settings.hero_trust_badge_1', $settings['hero_trust_badge_1']->value ?? '4.95 Rating Kepuasan') }}" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Badge 2</label>
                        <input type="text" 
                               name="settings[hero_trust_badge_2]" 
                               value="{{ old('settings.hero_trust_badge_2', $settings['hero_trust_badge_2']->value ?? 'Sameday & Paxel Pendingin') }}" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Badge 3</label>
                        <input type="text" 
                               name="settings[hero_trust_badge_3]" 
                               value="{{ old('settings.hero_trust_badge_3', $settings['hero_trust_badge_3']->value ?? 'Gratis Greeting Card') }}" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: Shipping Rates -->
        <div x-show="currentTab === 'shipping'" class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs space-y-6">
            <div class="border-b border-[#ede7e2] pb-4">
                <h3 class="text-base font-extrabold text-[#2d180c]">Tarif Ongkos Kirim Dinamis</h3>
                <p class="text-xs text-[#6d4630] mt-0.5">Tarif ini berlaku di halaman Checkout dan dihitung otomatis saat pembuatan pesanan.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Instant -->
                <div class="bg-[#faf7f4] p-5 rounded-2xl border border-[#ede7e2] space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c]">
                            Grab / Gojek Instant
                        </label>
                        <span class="text-[10px] font-semibold text-[#b87d4b] bg-white px-2 py-0.5 rounded-md border border-[#ede7e2]">1-2 Jam</span>
                    </div>
                    <p class="text-[11px] text-[#6d4630]">Pengiriman kurir kilat khusus pesanan lokal.</p>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-xs font-bold text-[#6d4630]">Rp</span>
                        <input type="number" 
                               name="settings[shipping_instant_grab]" 
                               value="{{ old('settings.shipping_instant_grab', $settings['shipping_instant_grab']->value ?? '25000') }}" 
                               min="0" step="1000" required
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm font-bold text-[#2d180c] bg-white">
                    </div>
                </div>

                <!-- Sameday -->
                <div class="bg-[#faf7f4] p-5 rounded-2xl border border-[#ede7e2] space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c]">
                            Sameday Delivery (Gojek)
                        </label>
                        <span class="text-[10px] font-semibold text-[#b87d4b] bg-white px-2 py-0.5 rounded-md border border-[#ede7e2]">Hari Ini</span>
                    </div>
                    <p class="text-[11px] text-[#6d4630]">Tiba sore hari untuk order sebelum pukul 14.00.</p>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-xs font-bold text-[#6d4630]">Rp</span>
                        <input type="number" 
                               name="settings[shipping_sameday_gojek]" 
                               value="{{ old('settings.shipping_sameday_gojek', $settings['shipping_sameday_gojek']->value ?? '18000') }}" 
                               min="0" step="1000" required
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm font-bold text-[#2d180c] bg-white">
                    </div>
                </div>

                <!-- Paxel Food -->
                <div class="bg-[#faf7f4] p-5 rounded-2xl border border-[#ede7e2] space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c]">
                            Paxel Food (Pendingin Makanan)
                        </label>
                        <span class="text-[10px] font-semibold text-[#b87d4b] bg-white px-2 py-0.5 rounded-md border border-[#ede7e2]">Cold Storage</span>
                    </div>
                    <p class="text-[11px] text-[#6d4630]">Pengiriman antarkota dengan cooler & jaminan aroma terjaga.</p>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-xs font-bold text-[#6d4630]">Rp</span>
                        <input type="number" 
                               name="settings[shipping_paxel_food]" 
                               value="{{ old('settings.shipping_paxel_food', $settings['shipping_paxel_food']->value ?? '22000') }}" 
                               min="0" step="1000" required
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm font-bold text-[#2d180c] bg-white">
                    </div>
                </div>

                <!-- Regular JNE -->
                <div class="bg-[#faf7f4] p-5 rounded-2xl border border-[#ede7e2] space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c]">
                            JNE / SiCepat Reguler
                        </label>
                        <span class="text-[10px] font-semibold text-[#b87d4b] bg-white px-2 py-0.5 rounded-md border border-[#ede7e2]">1-2 Hari</span>
                    </div>
                    <p class="text-[11px] text-[#6d4630]">Pengiriman standar nasional dengan bubble wrap tebal.</p>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-xs font-bold text-[#6d4630]">Rp</span>
                        <input type="number" 
                               name="settings[shipping_regular_jne]" 
                               value="{{ old('settings.shipping_regular_jne', $settings['shipping_regular_jne']->value ?? '12000') }}" 
                               min="0" step="1000" required
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm font-bold text-[#2d180c] bg-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: Heating Guide & Baker Tips -->
        <div x-show="currentTab === 'guide'" class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs space-y-6">
            <div class="border-b border-[#ede7e2] pb-4">
                <h3 class="text-base font-extrabold text-[#2d180c]">Panduan Hangat & Tips Baker (#panduan)</h3>
                <p class="text-xs text-[#6d4630] mt-0.5">Petunjuk cara menikmati kukis lumer dan daya simpan yang tampil di beranda toko.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Badge Sub-Judul Panduan
                    </label>
                    <input type="text" 
                           name="settings[guide_badge]" 
                           value="{{ old('settings.guide_badge', $settings['guide_badge']->value ?? 'Tips Baker NgemilKukis') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Judul Utama Bagian Panduan
                    </label>
                    <input type="text" 
                           name="settings[guide_headline]" 
                           value="{{ old('settings.guide_headline', $settings['guide_headline']->value ?? 'Cara Terbaik Menikmati Kukis Lumer') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>
            </div>

            <div class="space-y-4 pt-2">
                <!-- Method 1: Microwave -->
                <div class="bg-[#faf7f4] p-5 rounded-2xl border border-[#ede7e2] space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#2d180c]">Metode 1: Microwave</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Judul Langkah</label>
                            <input type="text" 
                                   name="settings[guide_microwave_title]" 
                                   value="{{ old('settings.guide_microwave_title', $settings['guide_microwave_title']->value ?? 'Microwave (15 - 20 Detik)') }}" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c] bg-white">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Deskripsi & Efek Rasa</label>
                            <input type="text" 
                                   name="settings[guide_microwave_desc]" 
                                   value="{{ old('settings.guide_microwave_desc', $settings['guide_microwave_desc']->value ?? 'Cokelat dan isian lava di tengahnya akan kembali meleleh hangat dan lembut seketika.') }}" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c] bg-white">
                        </div>
                    </div>
                </div>

                <!-- Method 2: Oven / Airfryer -->
                <div class="bg-[#faf7f4] p-5 rounded-2xl border border-[#ede7e2] space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#2d180c]">Metode 2: Oven / Airfryer</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Judul Langkah</label>
                            <input type="text" 
                                   name="settings[guide_oven_title]" 
                                   value="{{ old('settings.guide_oven_title', $settings['guide_oven_title']->value ?? 'Oven / Airfryer (3 Menit)') }}" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c] bg-white">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Deskripsi & Efek Rasa</label>
                            <input type="text" 
                                   name="settings[guide_oven_desc]" 
                                   value="{{ old('settings.guide_oven_desc', $settings['guide_oven_desc']->value ?? 'Suhu 160°C untuk mendapatkan kembali kerenyahan pinggiran kukis yang renyah bermentega.') }}" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c] bg-white">
                        </div>
                    </div>
                </div>

                <!-- Method 3: Storage -->
                <div class="bg-[#faf7f4] p-5 rounded-2xl border border-[#ede7e2] space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#2d180c]">Petunjuk Daya Simpan</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Judul</label>
                            <input type="text" 
                                   name="settings[guide_storage_title]" 
                                   value="{{ old('settings.guide_storage_title', $settings['guide_storage_title']->value ?? 'Daya Simpan') }}" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c] bg-white">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Instruksi Penyimpanan</label>
                            <input type="text" 
                                   name="settings[guide_storage_desc]" 
                                   value="{{ old('settings.guide_storage_desc', $settings['guide_storage_desc']->value ?? 'Tahan hingga 7 hari di suhu ruang dalam toples/wadah kedap udara, atau 14 hari di kulkas.') }}" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c] bg-white">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 6: Pre-Order Schedule -->
        <div x-show="currentTab === 'preorder'" class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs space-y-6">
            <div class="border-b border-[#ede7e2] pb-4">
                <h3 class="text-base font-extrabold text-[#2d180c]">Jadwal Pre-Order (PO) & Baking</h3>
                <p class="text-xs text-[#6d4630] mt-0.5">Informasi batch pembuatan kukis spesial yang tampil di bagian tengah beranda.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Badge Jadwal
                    </label>
                    <input type="text" 
                           name="settings[preorder_badge]" 
                           value="{{ old('settings.preorder_badge', $settings['preorder_badge']->value ?? 'Batch Pre-Order Minggu Ini') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Judul Bagian Pre-Order
                    </label>
                    <input type="text" 
                           name="settings[preorder_headline]" 
                           value="{{ old('settings.preorder_headline', $settings['preorder_headline']->value ?? 'Jadwal Baking & Pengiriman Spesial') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Jadwal Baking Batch Berikutnya
                    </label>
                    <input type="text" 
                           name="settings[preorder_next_batch]" 
                           value="{{ old('settings.preorder_next_batch', $settings['preorder_next_batch']->value ?? 'Jumat, 25 Oktober 2026') }}" 
                           placeholder="Contoh: Jumat, 25 Oktober 2026"
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Batas Waktu Pemesanan (Cut-off Time)
                    </label>
                    <input type="text" 
                           name="settings[preorder_cutoff]" 
                           value="{{ old('settings.preorder_cutoff', $settings['preorder_cutoff']->value ?? 'Kamis pukul 18:00 WIB') }}" 
                           placeholder="Contoh: Kamis pukul 18:00 WIB"
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                    Deskripsi Alasan Batch Baking
                </label>
                <textarea name="settings[preorder_description]" 
                          rows="3" 
                          class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c] leading-relaxed">{{ old('settings.preorder_description', $settings['preorder_description']->value ?? 'Kukis dipanggang sesuai kuota batch agar kualitas aroma dan kegaringan tetap optimal saat tiba di tanganmu.') }}</textarea>
            </div>
        </div>

        <!-- TAB 7: Contact & Social Media -->
        <div x-show="currentTab === 'contact'" class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs space-y-6">
            <div class="border-b border-[#ede7e2] pb-4">
                <h3 class="text-base font-extrabold text-[#2d180c]">Kontak WhatsApp & Media Sosial</h3>
                <p class="text-xs text-[#6d4630] mt-0.5">Tautan tombol WhatsApp CS dan profil Instagram resmi.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Nomor WhatsApp (Format Internasional Tanpa +)
                    </label>
                    <input type="text" 
                           name="settings[whatsapp_number]" 
                           value="{{ old('settings.whatsapp_number', $settings['whatsapp_number']->value ?? '6281234567890') }}" 
                           placeholder="6281234567890" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Tampilan Nomor WhatsApp (Teks Display)
                    </label>
                    <input type="text" 
                           name="settings[whatsapp_display]" 
                           value="{{ old('settings.whatsapp_display', $settings['whatsapp_display']->value ?? '+62 812-3456-7890') }}" 
                           placeholder="+62 812-3456-7890" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Username Instagram
                    </label>
                    <input type="text" 
                           name="settings[instagram_username]" 
                           value="{{ old('settings.instagram_username', $settings['instagram_username']->value ?? 'ngemilkukis') }}" 
                           placeholder="ngemilkukis" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        URL Profil Instagram
                    </label>
                    <input type="url" 
                           name="settings[instagram_url]" 
                           value="{{ old('settings.instagram_url', $settings['instagram_url']->value ?? 'https://instagram.com') }}" 
                           placeholder="https://instagram.com/..." 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Save Bar (Tombol Coklat Muda 10% Sesuai Standard) -->
        <div class="sticky bottom-6 mt-8 bg-white p-4 sm:p-5 rounded-2xl border border-[#ede7e2] shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-[#6d4630]">
                <svg class="w-4 h-4 text-[#b87d4b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Pastikan perubahan sudah sesuai sebelum menekan tombol simpan</span>
            </div>

            <button type="submit" 
                    class="bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs sm:text-sm font-bold px-8 py-3.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer flex items-center gap-2">
                <span>Simpan Semua Pengaturan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </div>

    </form>

</div>
@endsection
