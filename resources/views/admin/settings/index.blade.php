@extends('layouts.admin')

@section('title', 'Pengaturan Tampilan Dinamis')
@section('page_title', 'Kelola Tampilan & Konten Dinamis')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ currentTab: 'general' }">

    <!-- Description Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <h2 class="text-xl font-extrabold text-[#2d180c]">Konten Toko Dinamis</h2>
            <p class="text-xs sm:text-sm text-[#6d4630] mt-1">
                Seluruh elemen visual, teks promo, pengumuman bar atas, jadwal baking PO, hingga kontak WhatsApp dapat diperbarui langsung dari halaman ini tanpa perlu mengubah kode.
            </p>
        </div>
        <a href="{{ route('home') }}" target="_blank" 
           class="inline-flex items-center gap-2 bg-[#f6efe8] hover:bg-[#ede7e2] text-[#2d180c] text-xs font-bold px-4 py-2.5 rounded-full border border-[#ede7e2] transition flex-shrink-0">
            <span>🌐 Pratinjau Toko</span>
            <span>&rarr;</span>
        </a>
    </div>

    <!-- Tabbed Navigation (Putih Bersih 60%) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <button type="button" 
                @click="currentTab = 'general'" 
                :class="currentTab === 'general' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            ✨ Brand & Umum
        </button>
        <button type="button" 
                @click="currentTab = 'announcement'" 
                :class="currentTab === 'announcement' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            📢 Top Announcement Bar
        </button>
        <button type="button" 
                @click="currentTab = 'hero'" 
                :class="currentTab === 'hero' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            🌟 Hero Banner Utama
        </button>
        <button type="button" 
                @click="currentTab = 'preorder'" 
                :class="currentTab === 'preorder' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            📅 Jadwal Pre-Order
        </button>
        <button type="button" 
                @click="currentTab = 'contact'" 
                :class="currentTab === 'contact' ? 'bg-[#b87d4b] text-white shadow-xs font-bold' : 'bg-white text-[#6d4630] hover:text-[#2d180c] border border-[#ede7e2] font-semibold'"
                class="px-4 py-2.5 rounded-2xl text-xs whitespace-nowrap transition cursor-pointer">
            💬 Kontak & Media Sosial
        </button>
    </div>

    <!-- Main Settings Form -->
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <!-- TAB 1: General & Brand -->
        <div x-show="currentTab === 'general'" class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs space-y-6">
            <div class="border-b border-[#ede7e2] pb-4">
                <h3 class="text-base font-extrabold text-[#2d180c]">Identitas Toko & Brand</h3>
                <p class="text-xs text-[#6d4630] mt-0.5">Nama brand utama dan tagline artisan.</p>
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
                          class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">{{ old('settings.about_text', $settings['about_text']->value ?? 'Toko kukis artisan online dengan mentega Prancis murni & cokelat Belgia terbaik. Dipanggang fresh setiap hari dengan penuh kehangatan.') }}</textarea>
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
                    <span>Aktifkan Announcement Bar di Bagian Atas</span>
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
                <p class="text-xs text-[#6d4630] mt-0.5">Judul besar dan visual pertama yang dilihat pengunjung.</p>
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
                          class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">{{ old('settings.hero_description', $settings['hero_description']->value ?? 'Dibuat tangan dengan mentega Prancis autentik dan lelehan cokelat Belgia murni. Nikmati sensasi kukis hangat untuk teman ngopi, atau kirim sebagai Hampers Kado dengan Custom Kartu Ucapan personal.') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                    URL Foto Hero Utama (Unsplash / CDN)
                </label>
                <input type="url" 
                       name="settings[hero_image_url]" 
                       value="{{ old('settings.hero_image_url', $settings['hero_image_url']->value ?? 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1000&q=80') }}" 
                       class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
            </div>
        </div>

        <!-- TAB 4: Pre-Order Schedule -->
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
                          class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">{{ old('settings.preorder_description', $settings['preorder_description']->value ?? 'Kukis dipanggang sesuai kuota batch agar kualitas aroma dan kegaringan tetap optimal saat tiba di tanganmu.') }}</textarea>
            </div>
        </div>

        <!-- TAB 5: Contact & Social Media -->
        <div x-show="currentTab === 'contact'" class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs space-y-6">
            <div class="border-b border-[#ede7e2] pb-4">
                <h3 class="text-base font-extrabold text-[#2d180c]">Kontak WhatsApp & Media Sosial</h3>
                <p class="text-xs text-[#6d4630] mt-0.5">Tautan tombol WhatsApp CS dan profil Instagram.</p>
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

        <!-- Sticky Bottom Save Bar (Tombol Coklat Muda 10%) -->
        <div class="sticky bottom-6 mt-8 bg-white p-4 sm:p-5 rounded-2xl border border-[#ede7e2] shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-[#6d4630]">
                <span>💾</span>
                <span>Pastikan perubahan sudah sesuai sebelum menyimpan</span>
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
