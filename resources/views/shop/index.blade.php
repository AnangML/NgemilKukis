@extends('layouts.app')

@section('title', 'NgemilKukis - Toko Kukis Online Artisan, Ready Stock & Hampers Kado')

@section('content')
<div x-data="{
    activeTab: 'all',
    selectedCard: {{ $greetingCards->first()->id ?? 1 }},
    recipientName: 'Sahabat Tersayang',
    senderName: 'Dari Teman Manismu',
    cardMessage: 'Selamat ulang tahun! Semoga harimu semanis dan sehangat kukis ini, selalu dilimpahi kesehatan dan kebahagiaan.',
    cardColor: 'amber',
    cardIcon: '🎂',
    cardName: 'Happy Birthday Sweet Moments',
    toastMessage: '',
    showToast: false,

    notify(msg) {
        this.toastMessage = msg;
        this.showToast = true;
        setTimeout(() => { this.showToast = false; }, 3000);
    },

    chooseCard(card) {
        this.selectedCard = card.id;
        this.cardName = card.name;
        this.cardColor = card.theme_color;
        this.cardIcon = card.icon;
        this.cardMessage = card.default_message;
    },

    applyCardToCart() {
        $store.cart.isGift = true;
        $store.cart.giftData = {
            templateId: this.selectedCard,
            templateName: this.cardName,
            recipientName: this.recipientName,
            senderName: this.senderName,
            message: this.cardMessage,
            color: this.cardColor
        };
        this.notify('Kartu ucapan personal berhasil dipasang ke keranjang kado!');
        $store.cart.isOpen = true;
    }
}">

    <!-- Toast Notification (30% Coklat Agak Tua + 10% Aksen Coklat Muda) -->
    <div x-cloak x-show="showToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 right-6 z-50 bg-[#2d180c] text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 border border-[#b87d4b]">
        <svg class="w-5 h-5 text-[#b87d4b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-sm font-semibold" x-text="toastMessage"></span>
    </div>

    <!-- HERO SECTION (Putih 60% Dominan) -->
    <section class="bg-white pt-12 pb-20 lg:pt-20 lg:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left: Headline & CTAs (30% Coklat Agak Tua + 10% Coklat Muda) -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 bg-[#f6efe8] border border-[#ede7e2] px-4 py-1.5 rounded-full text-xs font-bold text-[#b87d4b]">
                        <span class="w-2 h-2 rounded-full bg-[#b87d4b] animate-pulse"></span>
                        <span>{{ $siteSettings['hero_badge'] ?? 'Dipanggang Segar Setiap Hari • 100% Belgian Chocolate' }}</span>
                    </div>

                    <h1 class="font-extrabold text-4xl sm:text-5xl lg:text-6xl tracking-tight text-[#2d180c] leading-[1.15]">
                        {{ $siteSettings['hero_headline'] ?? 'Kukis Renyah di Luar,' }}<br>
                        <span class="text-[#b87d4b]">
                            {{ $siteSettings['hero_subheadline'] ?? 'Lumer Manis di Dalam!' }}
                        </span>
                    </h1>

                    <p class="text-base sm:text-lg text-[#6d4630] max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        {{ $siteSettings['hero_description'] ?? 'Dibuat tangan dengan mentega Prancis autentik dan lelehan cokelat Belgia murni. Nikmati sensasi kukis hangat untuk teman ngopi, atau kirim sebagai Hampers Kado dengan Custom Kartu Ucapan personal.' }}
                    </p>

                    <!-- Trust Points (Clean, Minimalist Badges) -->
                    <div class="flex flex-wrap justify-center lg:justify-start gap-3 pt-1 text-xs font-semibold text-[#2d180c]">
                        <div class="flex items-center gap-2 bg-[#f6efe8] px-3.5 py-1.5 rounded-full border border-[#ede7e2]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#b87d4b]"></span>
                            <span>4.95 Rating Kepuasan</span>
                        </div>
                        <div class="flex items-center gap-2 bg-[#f6efe8] px-3.5 py-1.5 rounded-full border border-[#ede7e2]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#b87d4b]"></span>
                            <span>Sameday & Paxel Pendingin</span>
                        </div>
                        <div class="flex items-center gap-2 bg-[#f6efe8] px-3.5 py-1.5 rounded-full border border-[#ede7e2]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#b87d4b]"></span>
                            <span>Gratis Greeting Card</span>
                        </div>
                    </div>

                    <!-- Call To Actions (Semua Tombol Menggunakan Coklat Muda 10%) -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-3">
                        <a href="#katalog" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-sm sm:text-base px-8 py-4 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
                            <span>Beli Kukis Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                        <a href="#kartu-ucapan" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#f6efe8] hover:bg-[#ede7e2] text-[#2d180c] font-bold text-sm sm:text-base px-7 py-4 rounded-full border border-[#ede7e2] transition cursor-pointer">
                            <span>Desain Kartu Kado</span>
                        </a>
                    </div>
                </div>

                <!-- Right: Visual Hero Card -->
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Card Frame (Putih Bersih 60% dengan border halus) -->
                        <div class="relative rounded-3xl overflow-hidden shadow-xl border border-[#ede7e2] bg-white group">
                            <img src="{{ $siteSettings['hero_image_url'] ?? 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1000&q=80' }}" 
                                 alt="{{ $siteSettings['site_name'] ?? 'NgemilKukis' }}" 
                                 class="w-full h-96 object-cover object-center group-hover:scale-105 transition duration-500">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-[#2d180c]/85 via-black/20 to-transparent"></div>

                            <div class="absolute bottom-6 left-6 right-6 text-white space-y-1.5">
                                <span class="inline-block bg-[#b87d4b] text-white font-bold text-[10px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                    Signature Dark Choco
                                </span>
                                <h3 class="font-bold text-xl sm:text-2xl">Lumer di Setiap Gigitan</h3>
                                <p class="text-xs text-stone-200">
                                    Dark chocolate chunk Belgia 70% dengan taburan garam laut Bali artisan.
                                </p>
                            </div>
                        </div>

                        <!-- Floating Badge 1: Fresh Batch (Minimalist Glass Pill) -->
                        <div class="absolute -top-4 -right-4 bg-white/95 backdrop-blur-md px-4 py-3 rounded-2xl shadow-lg border border-[#ede7e2]">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#b87d4b] animate-pulse"></span>
                                <p class="text-xs font-bold text-[#2d180c]">Dipanggang Hari Ini</p>
                            </div>
                            <p class="text-[11px] text-[#6d4630] mt-0.5">Siap kirim via Instant & Sameday</p>
                        </div>

                        <!-- Floating Badge 2: Gift Ready (Minimalist Glass Pill) -->
                        <div class="absolute -bottom-5 -left-4 bg-white/95 backdrop-blur-md px-4 py-3 rounded-2xl shadow-lg border border-[#ede7e2]">
                            <p class="text-xs font-bold text-[#2d180c]">Hampers & Box Kado</p>
                            <p class="text-[11px] text-[#b87d4b] font-semibold mt-0.5">Pita satin & kartu ucapan personal</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- BAKING & PO SCHEDULE BANNER (Coklat Agak Tua Sekunder 30%) -->
    <section id="preorder" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-[#2d180c] text-white rounded-3xl p-6 sm:p-8 shadow-md flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4 text-center md:text-left">
                <div class="w-11 h-11 rounded-2xl bg-white/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#b87d4b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 justify-center md:justify-start">
                        <span class="w-2 h-2 rounded-full bg-[#b87d4b]"></span>
                        <span class="text-xs uppercase font-bold tracking-wider text-[#b87d4b]">
                            {{ $siteSettings['preorder_badge'] ?? 'Jadwal Baking & Pengiriman' }}
                        </span>
                    </div>
                    <h3 class="font-bold text-xl sm:text-2xl mt-0.5">
                        {{ $siteSettings['preorder_headline'] ?? 'Ready Stock Setiap Hari & Pre-Order Batch Khusus' }}
                    </h3>
                    <p class="text-xs sm:text-sm text-stone-300 mt-1">
                        {{ $siteSettings['preorder_description'] ?? 'Pesan varian Ready Stock sebelum pukul 15.00 WIB untuk dikirim di hari yang sama via Gojek/Grab Instant atau Paxel!' }}
                    </p>
                    @if(!empty($siteSettings['preorder_next_batch']) || !empty($siteSettings['preorder_cutoff']))
                        <div class="flex flex-wrap gap-2.5 mt-2.5 text-xs">
                            @if(!empty($siteSettings['preorder_next_batch']))
                                <span class="bg-white/10 px-2.5 py-1 rounded-lg border border-white/15">
                                    Batch: <strong>{{ $siteSettings['preorder_next_batch'] }}</strong>
                                </span>
                            @endif
                            @if(!empty($siteSettings['preorder_cutoff']))
                                <span class="bg-[#b87d4b]/30 text-amber-200 px-2.5 py-1 rounded-lg border border-[#b87d4b]/40">
                                    Cut-off: <strong>{{ $siteSettings['preorder_cutoff'] }}</strong>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <!-- Tombol Coklat Muda -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="#katalog" @click="activeTab = 'ready_stock'" class="bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-xs sm:text-sm px-6 py-3 rounded-full transition shadow-sm cursor-pointer">
                    Lihat Ready Stock
                </a>
                <a href="#katalog" @click="activeTab = 'pre_order'" class="bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm px-6 py-3 rounded-full border border-white/20 transition cursor-pointer">
                    Lihat Pre-Order (PO)
                </a>
            </div>
        </div>
    </section>

    <!-- PRODUCT CATALOG SECTION (Putih Bersih 60%) -->
    <section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-16">
        <div class="text-center max-w-2xl mx-auto space-y-2.5">
            <span class="text-xs font-bold tracking-widest text-[#b87d4b] uppercase">Menu Pilihan Kami</span>
            <h2 class="font-extrabold text-3xl sm:text-4xl text-[#2d180c]">Katalog Kukis Artisan</h2>
            <p class="text-sm text-[#6d4630]">
                Pilih kukis favoritmu dalam kemasan satuan atau pilih boks hampers spesial untuk hadiah terbaik.
            </p>
        </div>

        <!-- Filter Tab Buttons (Tombol Coklat Muda 10% jika aktif) -->
        <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 mt-8 mb-12">
            <button @click="activeTab = 'all'" 
                    :class="activeTab === 'all' ? 'bg-[#b87d4b] text-white shadow-sm' : 'bg-[#f6efe8] text-[#2d180c] hover:bg-[#ede7e2]'"
                    class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition cursor-pointer">
                Semua Menu ({{ $allProducts->count() }})
            </button>
            <button @click="activeTab = 'ready_stock'" 
                    :class="activeTab === 'ready_stock' ? 'bg-[#b87d4b] text-white shadow-sm' : 'bg-[#f6efe8] text-[#2d180c] hover:bg-[#ede7e2]'"
                    class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition flex items-center gap-1.5 cursor-pointer">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Ready Stock ({{ $readyStockProducts->count() }})
            </button>
            <button @click="activeTab = 'pre_order'" 
                    :class="activeTab === 'pre_order' ? 'bg-[#b87d4b] text-white shadow-sm' : 'bg-[#f6efe8] text-[#2d180c] hover:bg-[#ede7e2]'"
                    class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition flex items-center gap-1.5 cursor-pointer">
                <span>🗓️</span>
                Pre-Order Batch ({{ $preOrderProducts->count() }})
            </button>
        </div>

        <!-- Products Grid (Putih 60% + Coklat Tua 30% + Tombol Coklat Muda 10%) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-7">
            @foreach ($allProducts as $product)
                <div x-show="activeTab === 'all' || activeTab === '{{ $product->type }}'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="group bg-white rounded-3xl overflow-hidden border border-[#ede7e2] hover:border-[#b87d4b] shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                    
                    <!-- Top Image Area -->
                    <div class="relative overflow-hidden aspect-square bg-stone-50">
                        <img src="{{ $product->image }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5 items-start">
                            @if ($product->badge)
                                <span class="bg-[#b87d4b] text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-xs uppercase tracking-wider">
                                    {{ $product->badge }}
                                </span>
                            @endif

                            @if ($product->type === 'ready_stock')
                                <span class="bg-[#2d180c]/85 backdrop-blur-xs text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                                    ✓ Ready Stock
                                </span>
                            @else
                                <span class="bg-[#2d180c]/85 backdrop-blur-xs text-[#b87d4b] text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                                    🗓️ Pre-Order
                                </span>
                            @endif
                        </div>

                        <!-- Weight Pill -->
                        <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-xs text-white text-[11px] font-medium px-2 py-0.5 rounded-md">
                            {{ $product->weight_grams }}g
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-1.5">
                            <!-- Rating -->
                            <div class="flex items-center gap-1.5 text-xs text-[#6d4630]">
                                <span class="text-[#b87d4b] font-bold">★ {{ number_format($product->rating, 1) }}</span>
                                <span class="text-stone-400">({{ $product->review_count }})</span>
                            </div>

                            <h3 class="font-bold text-base text-[#2d180c] group-hover:text-[#b87d4b] transition leading-snug">
                                {{ $product->name }}
                            </h3>

                            <p class="text-xs text-[#6d4630] line-clamp-2 leading-relaxed">
                                {{ $product->tagline }}
                            </p>

                            @if ($product->baking_schedule)
                                <p class="text-[11px] text-[#2d180c] font-medium bg-[#f6efe8] rounded-lg p-2 border border-[#ede7e2]">
                                    ⚡ {{ $product->baking_schedule }}
                                </p>
                            @endif
                        </div>

                        <!-- Pricing & Add to Cart (Tombol Coklat Muda 10%) -->
                        <div class="pt-3 border-t border-[#ede7e2] flex items-center justify-between">
                            <div>
                                @if ($product->sale_price)
                                    <span class="text-[11px] text-stone-400 line-through block">
                                        {{ $product->formattedPrice() }}
                                    </span>
                                    <span class="font-extrabold text-lg text-[#b87d4b]">
                                        {{ $product->formattedSalePrice() }}
                                    </span>
                                @else
                                    <span class="font-extrabold text-lg text-[#2d180c]">
                                        {{ $product->formattedPrice() }}
                                    </span>
                                @endif
                            </div>

                            <!-- Add to Cart Button (Tombol Coklat Muda) -->
                            <button @click="$store.cart.addItem({
                                        id: {{ $product->id }},
                                        name: '{{ addslashes($product->name) }}',
                                        price: {{ $product->effectivePrice() }},
                                        image: '{{ $product->image }}',
                                        type: '{{ $product->type }}'
                                    }); notify('{{ addslashes($product->name) }} berhasil ditambahkan');" 
                                    type="button"
                                    class="inline-flex items-center justify-center gap-1.5 bg-[#b87d4b] hover:bg-[#a36b3b] text-white px-4 py-2.5 rounded-full font-bold text-xs shadow-xs transition transform active:scale-95 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Pesan</span>
                            </button>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </section>

    <!-- INTERACTIVE CUSTOM GREETING CARD / GIFT STUDIO (Putih Bersih 60% + Coklat Agak Tua 30% + Coklat Muda 10%) -->
    <section id="kartu-ucapan" class="py-20 bg-[#fbfbfb] border-y border-[#ede7e2] relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-2xl mx-auto space-y-2.5 mb-12">
                <span class="text-xs font-bold tracking-widest text-[#b87d4b] uppercase">Fitur Spesial Kado & Hampers</span>
                <h2 class="font-extrabold text-3xl sm:text-4xl text-[#2d180c]">
                    Studio Desain Greeting Card
                </h2>
                <p class="text-sm text-[#6d4630]">
                    Ingin mengirim kukis sebagai kado ulang tahun, wisuda, atau kejutan romantis? Tuliskan pesan personalmu di sini, dan kami akan mencetaknya di kartu premium untuk diselipkan ke dalam boks kukis!
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <!-- Left: Form Controls & Template Selector -->
                <div class="lg:col-span-6 bg-white p-7 sm:p-9 rounded-3xl border border-[#ede7e2] shadow-sm space-y-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-2.5">
                            1. Pilih Tema Kartu Ucapan
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            @foreach ($greetingCards as $card)
                                <button type="button" 
                                        @click="chooseCard({{ json_encode($card) }})"
                                        :class="selectedCard === {{ $card->id }} ? 'border-[#b87d4b] bg-[#f6efe8] ring-2 ring-[#b87d4b]' : 'border-[#ede7e2] bg-white hover:bg-[#f6efe8]'"
                                        class="p-3 rounded-2xl border text-left transition flex flex-col justify-between cursor-pointer">
                                    <span class="text-2xl mb-1">{{ $card->icon }}</span>
                                    <span class="text-xs font-bold text-[#2d180c] line-clamp-1">{{ $card->name }}</span>
                                    <span class="text-[10px] text-[#b87d4b] font-semibold mt-0.5">Gratis</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Personal Information Form -->
                    <div class="space-y-4 pt-2 border-t border-[#ede7e2]">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c]">
                            2. Tulis Pesan Personal
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#2d180c] mb-1">Untuk (Nama Penerima):</label>
                                <input type="text" x-model="recipientName" 
                                       placeholder="Contoh: Sahabatku Maya"
                                       class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#2d180c] mb-1">Dari (Nama Pengirim):</label>
                                <input type="text" x-model="senderName" 
                                       placeholder="Contoh: Dari Rian"
                                       class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#2d180c] mb-1">Isi Pesan Kartu:</label>
                            <textarea x-model="cardMessage" rows="3" 
                                      placeholder="Tuliskan ucapan manismu di sini..."
                                      class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 text-sm leading-relaxed"></textarea>
                            <span class="text-[11px] text-stone-400 block text-right">Maksimal 600 karakter</span>
                        </div>
                    </div>

                    <!-- Apply Button (Tombol Coklat Muda 10%) -->
                    <button @click="applyCardToCart()" 
                            type="button"
                            class="w-full bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold py-3.5 px-6 rounded-full shadow-sm transition flex items-center justify-center gap-2 cursor-pointer">
                        <span>Pasang Kartu Ini ke Keranjang Belanja</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>

                <!-- Right: Realistic Clean Card Preview (Putih Bersih 60%) -->
                <div class="lg:col-span-6 flex justify-center">
                    <div class="w-full max-w-md">
                        <!-- Physical Card Simulator Frame -->
                        <div class="relative bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-[#ede7e2] transition-all transform duration-300">
                            
                            <!-- Card Header -->
                            <div class="flex items-center justify-between pb-6 border-b border-[#ede7e2]">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-3xl" x-text="cardIcon"></span>
                                    <div>
                                        <p class="font-bold text-sm text-[#2d180c]" x-text="cardName"></p>
                                        <p class="text-[10px] text-[#6d4630] tracking-wider uppercase font-semibold">NgemilKukis Gift Studio</p>
                                    </div>
                                </div>
                                <div class="w-9 h-9 rounded-full bg-[#b87d4b] text-white text-xs font-bold flex items-center justify-center shadow-xs">
                                    NK
                                </div>
                            </div>

                            <!-- Card Body Written Text -->
                            <div class="py-8 space-y-4">
                                <div class="text-xs uppercase tracking-wider font-bold text-[#6d4630]">
                                    Dear: <span class="text-base font-bold text-[#2d180c] normal-case" x-text="recipientName"></span>
                                </div>

                                <blockquote class="text-sm sm:text-base text-[#2d180c] leading-relaxed min-h-[80px] border-l-2 border-[#b87d4b] pl-4 py-1" x-text="cardMessage">
                                </blockquote>

                                <div class="pt-4 text-right">
                                    <span class="text-xs text-[#6d4630] block">Hangat dari,</span>
                                    <span class="font-bold text-base text-[#2d180c]" x-text="senderName"></span>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="pt-4 border-t border-[#ede7e2] flex items-center justify-between text-[11px] text-[#6d4630]">
                                <span>🍪 Baked with Love by NgemilKukis</span>
                                <span>Free Gift Card Edition</span>
                            </div>
                        </div>

                        <!-- Card Preview Note -->
                        <p class="text-center text-xs text-[#6d4630] mt-4">
                            ✨ Kartu ini dicetak dengan kertas tebal linen textured dan diselipkan di dalam boks kukis kamu.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SOCIAL PROOF & INSTAGRAM BISNIS SECTION (Putih Bersih 60%) -->
    <section id="sosmed" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center max-w-2xl mx-auto space-y-2.5 mb-12">
            <span class="text-xs font-bold tracking-widest text-[#b87d4b] uppercase">Komunitas & Cerita Pelanggan</span>
            <h2 class="font-extrabold text-3xl sm:text-4xl text-[#2d180c]">
                Momen Manis di Instagram
            </h2>
            <p class="text-sm text-[#6d4630]">
                Intip keseruan unboxing dan ulasan para pecinta kukis. Tag kami di <strong>@ngemilkukis</strong> untuk berkesempatan memenangkan free cookies setiap bulannya!
            </p>
        </div>

        <!-- 4-Grid Instagram Gallery -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            <!-- Feed 1 -->
            <div class="group relative rounded-3xl overflow-hidden aspect-square shadow-xs border border-[#ede7e2]">
                <img src="https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=600&q=80" 
                     alt="Lotus Cookie Review" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-[#2d180c]/70 opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4 text-white">
                    <p class="text-xs font-semibold">"Lotus Biscoff-nya beneran nagih! Lumer pas dipanasin di microwave 15 detik 🤤"</p>
                    <span class="text-[10px] text-[#b87d4b] font-bold mt-1">@dian_pratama</span>
                </div>
            </div>

            <!-- Feed 2 -->
            <div class="group relative rounded-3xl overflow-hidden aspect-square shadow-xs border border-[#ede7e2]">
                <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=600&q=80" 
                     alt="Hampers Box Unboxing" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-[#2d180c]/70 opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4 text-white">
                    <p class="text-xs font-semibold">"Gift card-nya estetik banget, temenku seneng banget pas dikasih kado wisuda ini!"</p>
                    <span class="text-[10px] text-[#b87d4b] font-bold mt-1">@clarissaa_w</span>
                </div>
            </div>

            <!-- Feed 3 -->
            <div class="group relative rounded-3xl overflow-hidden aspect-square shadow-xs border border-[#ede7e2]">
                <img src="https://images.unsplash.com/photo-1618923834413-2770b904ee10?auto=format&fit=crop&w=600&q=80" 
                     alt="Red Velvet Cookie" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-[#2d180c]/70 opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4 text-white">
                    <p class="text-xs font-semibold">"Cream cheese red velvetnya lumer parah, ga enek sama sekali, pas banget!"</p>
                    <span class="text-[10px] text-[#b87d4b] font-bold mt-1">@reza.kuliner</span>
                </div>
            </div>

            <!-- Feed 4 -->
            <div class="group relative rounded-3xl overflow-hidden aspect-square shadow-xs border border-[#ede7e2]">
                <img src="https://images.unsplash.com/photo-1548848221-0c2e497ed557?auto=format&fit=crop&w=600&q=80" 
                     alt="Matcha Cookie" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-[#2d180c]/70 opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4 text-white">
                    <p class="text-xs font-semibold">"Matchanya wangi autentik Kyoto, ada white choco melt yang bikin nagih!"</p>
                    <span class="text-[10px] text-[#b87d4b] font-bold mt-1">@fathir_snack</span>
                </div>
            </div>
        </div>

        <!-- Social Media Buttons (Tombol Coklat Muda 10%) -->
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ $siteSettings['instagram_url'] ?? 'https://instagram.com' }}" target="_blank" rel="noopener noreferrer" 
               class="inline-flex items-center gap-2.5 bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-sm px-7 py-3.5 rounded-full shadow-sm transition transform hover:-translate-y-0.5 cursor-pointer">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
                <span>Follow Kami di {{ '@' . ($siteSettings['instagram_username'] ?? 'ngemilkukis') }}</span>
            </a>

            <a href="https://wa.me/{{ $siteSettings['whatsapp_number'] ?? '6281234567890' }}?text=Halo%20Admin%20{{ urlencode($siteSettings['site_name'] ?? 'NgemilKukis') }},%20mau%20konsultasi%20pesanan%20kukis%20dan%20hampers%20dong" 
               target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 bg-[#2d180c] hover:bg-[#b87d4b] text-white font-bold text-sm px-7 py-3.5 rounded-full shadow-sm transition transform hover:-translate-y-0.5 cursor-pointer">
                <span>Chat WhatsApp Bisnis ({{ $siteSettings['whatsapp_display'] ?? '+62 812-3456-7890' }})</span>
            </a>
        </div>
    </section>

    <!-- FAQ & COOKIE HEATING GUIDE -->
    <section id="panduan" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="bg-[#fbfbfb] p-8 sm:p-12 rounded-3xl border border-[#ede7e2] space-y-6">
            <div class="text-center space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-[#b87d4b]">Tips Baker NgemilKukis</span>
                <h3 class="font-bold text-2xl text-[#2d180c]">Cara Terbaik Menikmati Kukis Lumer</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 text-center">
                <div class="bg-white p-6 rounded-2xl border border-[#ede7e2] shadow-xs space-y-3">
                    <div class="w-10 h-10 mx-auto rounded-xl bg-[#f6efe8] flex items-center justify-center text-[#b87d4b]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-sm text-[#2d180c]">Microwave (15 - 20 Detik)</h4>
                    <p class="text-xs text-[#6d4630] leading-relaxed">
                        Cokelat dan isian lava di tengahnya akan kembali meleleh hangat dan lembut seketika.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-[#ede7e2] shadow-xs space-y-3">
                    <div class="w-10 h-10 mx-auto rounded-xl bg-[#f6efe8] flex items-center justify-center text-[#b87d4b]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343a7.975 7.975 0 010 11.314z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-sm text-[#2d180c]">Oven / Airfryer (3 Menit)</h4>
                    <p class="text-xs text-[#6d4630] leading-relaxed">
                        Suhu 160°C untuk mendapatkan kembali kerenyahan pinggiran kukis yang renyah bermentega.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-[#ede7e2] shadow-xs space-y-3">
                    <div class="w-10 h-10 mx-auto rounded-xl bg-[#f6efe8] flex items-center justify-center text-[#b87d4b]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-sm text-[#2d180c]">Daya Simpan</h4>
                    <p class="text-xs text-[#6d4630] leading-relaxed">
                        Tahan hingga 7 hari di suhu ruang dalam toples/wadah kedap udara, atau 14 hari di kulkas.
                    </p>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
