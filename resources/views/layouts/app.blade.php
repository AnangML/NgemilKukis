<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'NgemilKukis - Toko Kukis Online Artisan & Hampers Kado')</title>
    <meta name="description" content="Kukis artisan premium renyah & chewy dengan 100% Belgian chocolate dan French butter. Tersedia Ready Stock, Pre-Order, hampers gift box & custom kartu ucapan.">
    <meta name="keywords" content="kukis online, hampers kukis, artisan cookies, soft baked cookies, choco chunk, ngemilkukis, kado wisuda, hampers ulang tahun">

    <!-- Modern Clean Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-white text-[#2d180c] antialiased selection:bg-[#b87d4b] selection:text-white" x-data>


    <!-- Navigation Header (Putih Bersih 60%) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-[#ede7e2] transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Clean Brand Typography Logo -->
                <a href="{{ route('home') }}" class="group">
                    <span class="font-extrabold text-2xl sm:text-3xl tracking-tight text-[#2d180c] block leading-none">
                        {{ $siteSettings['site_name'] ?? 'NgemilKukis' }}<span class="text-[#b87d4b]">.</span>
                    </span>
                    <span class="text-[10px] font-semibold tracking-wider text-[#6d4630] uppercase mt-1 block">
                        {{ $siteSettings['site_tagline'] ?? 'Artisan Fresh Bake' }}
                    </span>
                </a>

                <!-- Clean Desktop Navigation Links (No Emojis) -->
                <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-[#2d180c]">
                    <a href="{{ route('home') }}#katalog" class="hover:text-[#b87d4b] transition">
                        Katalog Kukis
                    </a>
                    <a href="{{ route('home') }}#preorder" class="hover:text-[#b87d4b] transition">
                        Jadwal Pre-Order
                    </a>
                    <a href="{{ route('home') }}#kartu-ucapan" class="hover:text-[#b87d4b] transition">
                        Custom Gift Card
                    </a>
                    <a href="{{ route('home') }}#panduan" class="hover:text-[#b87d4b] transition">
                        Tips Penyajian
                    </a>
                    <a href="{{ route('customer.orders') }}" 
                       class="hover:text-[#b87d4b] transition {{ request()->routeIs('customer.orders') ? 'text-[#b87d4b]' : '' }}">
                        Pesanan Saya
                    </a>
                </nav>

                <!-- Actions: Admin Access, Mobile Orders & Cart -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <a href="{{ route('customer.orders') }}" 
                       class="inline-flex md:hidden text-xs font-bold text-[#6d4630] hover:text-[#b87d4b] transition py-1"
                       title="Pesanan Saya">
                        Pesanan
                    </a>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" 
                               class="text-xs font-bold text-[#b87d4b] hover:text-[#2d180c] transition">
                                Panel Admin
                            </a>
                        @endif
                    @else
                        <a href="{{ route('admin.login') }}" 
                           class="text-xs font-bold text-[#6d4630] hover:text-[#b87d4b] transition"
                           title="Masuk ke Panel Admin">
                            Masuk
                        </a>
                    @endauth

                    <!-- Trigger Cart Button (Tombol Coklat Muda 10%) -->
                    <button @click="$store.cart.isOpen = true" 
                            type="button"
                            class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold shadow-sm transition transform active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Keranjang</span>
                        <span x-show="$store.cart.count > 0" 
                              x-text="$store.cart.count"
                              class="bg-white text-[#2d180c] text-[11px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center -mr-1">
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Slide-over Cart Drawer (Putih Bersih 60%) -->
    <div x-cloak x-show="$store.cart.isOpen" 
         class="fixed inset-0 z-50 overflow-hidden" 
         role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="$store.cart.isOpen"
             x-transition:enter="ease-in-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$store.cart.isOpen = false"
             class="fixed inset-0 bg-[#2d180c]/50 backdrop-blur-xs transition-opacity">
        </div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div x-show="$store.cart.isOpen"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
                
                <!-- Drawer Header (Modern & Minimalist) -->
                <div class="p-6 bg-white border-b border-[#ede7e2] flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#f6efe8] flex items-center justify-center text-[#b87d4b] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-base text-[#2d180c] tracking-tight">Keranjang Belanja</h2>
                            <p class="text-xs text-[#6d4630] font-medium" x-text="$store.cart.count + ' kukis dipilih'"></p>
                        </div>
                    </div>
                    <button @click="$store.cart.isOpen = false" 
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-stone-400 hover:text-[#2d180c] hover:bg-[#f6efe8] transition cursor-pointer"
                            aria-label="Tutup Keranjang">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Drawer Content -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    <!-- Empty State (Minimalist) -->
                    <template x-if="$store.cart.items.length === 0">
                        <div class="py-16 text-center">
                            <div class="w-14 h-14 mx-auto rounded-2xl bg-[#f6efe8] flex items-center justify-center text-[#b87d4b] mb-4">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-base text-[#2d180c]">Keranjang Masih Kosong</h3>
                            <p class="text-xs text-[#6d4630] mt-1 max-w-xs mx-auto">Yuk pilih kukis artisan favoritmu dari menu katalog!</p>
                            <button @click="$store.cart.isOpen = false" 
                                    class="mt-6 inline-block bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                                Mulai Belanja Sekarang
                            </button>
                        </div>
                    </template>

                    <!-- Items List (Modern, Minimalist & with (x) Cancel Button) -->
                    <template x-for="item in $store.cart.items" :key="item.id">
                        <div class="p-4 rounded-2xl bg-white border border-[#ede7e2] hover:border-[#b87d4b]/40 transition-colors relative group">
                            <!-- Top: Cookie image, details, and (X) Cancel button -->
                            <div class="flex items-start gap-3.5">
                                <img :src="item.image" :alt="item.name" class="w-16 h-16 rounded-xl object-cover bg-[#f6efe8] border border-[#ede7e2]/70 shrink-0">
                                
                                <div class="flex-1 min-w-0 pr-2">
                                    <h4 class="font-bold text-xs sm:text-sm text-[#2d180c] leading-snug line-clamp-2" x-text="item.name"></h4>
                                    
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs font-bold text-[#b87d4b]" x-text="$store.cart.formatRupiah(item.price)"></span>
                                        <span x-show="item.type === 'pre_order'" class="text-[9px] font-bold uppercase tracking-wider bg-[#f6efe8] text-[#b87d4b] px-1.5 py-0.5 rounded">
                                            Pre-Order
                                        </span>
                                    </div>
                                </div>

                                <!-- (x) Cancel / Remove Button -->
                                <button @click="$store.cart.removeItem(item.id)" 
                                        type="button"
                                        class="w-7 h-7 -mr-1 -mt-1 rounded-lg text-stone-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition cursor-pointer" 
                                        title="Hapus kukis dari keranjang"
                                        aria-label="Hapus kukis dari keranjang">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Bottom: Quantity stepper & item total -->
                            <div class="mt-3 pt-3 border-t border-[#f6efe8] flex items-center justify-between">
                                <div class="inline-flex items-center border border-[#ede7e2] rounded-lg bg-[#faf8f6] p-0.5">
                                    <button @click="$store.cart.updateQuantity(item.id, item.quantity - 1)" 
                                            class="w-6 h-6 rounded-md hover:bg-white text-[#2d180c] hover:text-[#b87d4b] font-bold text-xs flex items-center justify-center transition cursor-pointer"
                                            aria-label="Kurangi jumlah">
                                        -
                                    </button>
                                    <span class="font-bold text-xs text-[#2d180c] w-7 text-center select-none" x-text="item.quantity"></span>
                                    <button @click="$store.cart.updateQuantity(item.id, item.quantity + 1)" 
                                            class="w-6 h-6 rounded-md hover:bg-white text-[#2d180c] hover:text-[#b87d4b] font-bold text-xs flex items-center justify-center transition cursor-pointer"
                                            aria-label="Tambah jumlah">
                                        +
                                    </button>
                                </div>

                                <div class="text-right">
                                    <span class="text-[10px] text-stone-400 uppercase tracking-wider font-semibold block">Subtotal</span>
                                    <span class="text-xs font-bold text-[#2d180c]" x-text="$store.cart.formatRupiah(item.price * item.quantity)"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Gift Card Toggle Option in Drawer (Clean & Minimalist) -->
                    <div x-show="$store.cart.items.length > 0" class="p-4 rounded-2xl bg-[#faf8f6] border border-[#ede7e2]">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="cartGiftToggle" x-model="$store.cart.isGift" class="mt-0.5 h-4 w-4 text-[#b87d4b] focus:ring-[#b87d4b] border-stone-300 rounded cursor-pointer">
                            <label for="cartGiftToggle" class="text-xs text-[#2d180c] cursor-pointer flex-1">
                                <span class="font-bold text-[#2d180c] flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#b87d4b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V4a2 2 0 112 2h-2zm0 0V4a2 2 0 10-2 2h2m-7 4h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2a1 1 0 011-1zm2 4h10v7H7v-7z" />
                                    </svg>
                                    Kirim sebagai Kado / Hadiah?
                                </span>
                                <span class="text-[#6d4630] text-[11px] block mt-0.5 leading-relaxed">
                                    Dapatkan gratis Custom Greeting Card dengan pesan personal saat checkout!
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Drawer Footer (Clean, Sleek & Modern) -->
                <div x-show="$store.cart.items.length > 0" class="p-6 bg-white border-t border-[#ede7e2] space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#6d4630]">Subtotal Produk</span>
                        <span class="font-extrabold text-lg text-[#2d180c]" x-text="$store.cart.formatRupiah($store.cart.subtotal)"></span>
                    </div>
                    <p class="text-[11px] text-[#6d4630] leading-relaxed">
                        * Ongkos kirim (Grab/Gojek Instant / Paxel Makanan) dihitung otomatis pada halaman checkout.
                    </p>
                    <!-- Tombol Coklat Muda -->
                    <a href="{{ route('checkout') }}" 
                       class="w-full flex items-center justify-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold py-3.5 px-6 rounded-2xl shadow-xs transition transform active:scale-98 cursor-pointer">
                        <span>Lanjut ke Checkout</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Slot (Putih Bersih 60%) -->
    <main>
        @yield('content')
    </main>

    <!-- Footer (Coklat Agak Tua Sekunder 30%) -->
    <footer class="bg-[#2d180c] text-white pt-16 pb-12 border-t-4 border-[#b87d4b] mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-stone-800">
                <!-- Brand Info -->
                <div class="space-y-4 md:col-span-1">
                    <span class="font-extrabold text-2xl tracking-tight text-white block">
                        {{ $siteSettings['site_name'] ?? 'NgemilKukis' }}<span class="text-[#b87d4b]">.</span>
                    </span>
                    <p class="text-xs text-stone-300 leading-relaxed">
                        {{ $siteSettings['about_text'] ?? 'Toko kukis artisan online dengan mentega Prancis murni & cokelat Belgia terbaik. Dipanggang fresh setiap hari dengan penuh kehangatan.' }}
                    </p>
                    <!-- Social icons -->
                    <div class="flex items-center gap-2.5 pt-2">
                        <a href="{{ $siteSettings['instagram_url'] ?? 'https://instagram.com' }}" target="_blank" rel="noopener noreferrer" 
                           class="w-8 h-8 rounded-full bg-stone-800 hover:bg-[#b87d4b] flex items-center justify-center text-white transition"
                           title="Instagram {{ '@' . ($siteSettings['instagram_username'] ?? 'ngemilkukis') }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/{{ $siteSettings['whatsapp_number'] ?? '6281234567890' }}" target="_blank" rel="noopener noreferrer" 
                           class="w-8 h-8 rounded-full bg-stone-800 hover:bg-[#b87d4b] flex items-center justify-center text-white transition text-xs font-bold"
                           title="Chat WhatsApp Admin">
                            WA
                        </a>
                    </div>
                </div>

                <!-- Navigation Quick Links -->
                <div>
                    <h4 class="font-bold text-white text-sm mb-4">Navigasi</h4>
                    <ul class="space-y-2 text-xs text-stone-300">
                        <li><a href="{{ route('home') }}#katalog" class="hover:text-[#b87d4b] transition">Katalog Varian Kukis</a></li>
                        <li><a href="{{ route('customer.orders') }}" class="hover:text-[#b87d4b] text-amber-300 font-bold transition">Lacak & Riwayat Pesanan Saya</a></li>
                        <li><a href="{{ route('home') }}#preorder" class="hover:text-[#b87d4b] transition">Jadwal Pre-Order (PO)</a></li>
                        <li><a href="{{ route('home') }}#kartu-ucapan" class="hover:text-[#b87d4b] transition">Custom Kartu Ucapan</a></li>
                        <li><a href="{{ route('checkout') }}" class="hover:text-[#b87d4b] transition">Checkout Pesanan</a></li>
                        <li class="pt-1"><a href="{{ route('admin.login') }}" class="hover:text-[#b87d4b] transition">Portal Admin</a></li>
                    </ul>
                </div>

                <!-- Customer Service & Delivery -->
                <div>
                    <h4 class="font-bold text-white text-sm mb-4">Pengiriman</h4>
                    <ul class="space-y-2 text-xs text-stone-300">
                        <li>Grab / Gojek Instant</li>
                        <li>Paxel Food (Pendingin Makanan)</li>
                        <li>JNE / SiCepat Reguler</li>
                        <li class="pt-1 text-[11px] text-[#b87d4b]">
                            Dipacking aman dengan kardus tebal, bubble wrap, dan seal kedap udara.
                        </li>
                    </ul>
                </div>

                <!-- Xendit Payment Integration Badge -->
                <div>
                    <h4 class="font-bold text-white text-sm mb-4">Pembayaran</h4>
                    <p class="text-xs text-stone-300 mb-3">
                        Transaksi aman didukung oleh <strong>Xendit Payment Gateway</strong>:
                    </p>
                    <div class="flex flex-wrap gap-2 text-[10px] font-bold text-[#2d180c]">
                        <span class="bg-white px-2.5 py-1 rounded-md">QRIS</span>
                        <span class="bg-white px-2.5 py-1 rounded-md">BCA VA</span>
                        <span class="bg-white px-2.5 py-1 rounded-md">Mandiri / BRI VA</span>
                    </div>
                    <div class="mt-4 pt-3 border-t border-stone-800 text-xs text-stone-400">
                        WhatsApp: <strong>{{ $siteSettings['whatsapp_display'] ?? '+62 812-3456-7890' }}</strong>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-stone-400 gap-4">
                <p>&copy; {{ date('Y') }} {{ $siteSettings['site_name'] ?? 'NgemilKukis' }}. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.login') }}" class="hover:text-white transition">
                        Login Admin
                    </a>
                    <span class="text-[#b87d4b] font-semibold hidden sm:inline">&bull;</span>
                    <p class="text-[#b87d4b] font-semibold">Artisan Soft Baked & Crispy Cookies</p>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
