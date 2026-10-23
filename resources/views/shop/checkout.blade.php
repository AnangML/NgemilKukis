@extends('layouts.app')

@section('title', 'Checkout Pesanan - NgemilKukis')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" 
     x-data="{
        name: '',
        phone: '',
        email: '',
        address: '',
        city: 'Jakarta',
        shippingMethod: 'instant_grab',
        shippingCosts: {
            'instant_grab': {{ (int) ($siteSettings['shipping_instant_grab'] ?? 25000) }},
            'sameday_gojek': {{ (int) ($siteSettings['shipping_sameday_gojek'] ?? 18000) }},
            'paxel_food': {{ (int) ($siteSettings['shipping_paxel_food'] ?? 22000) }},
            'regular_jne': {{ (int) ($siteSettings['shipping_regular_jne'] ?? 12000) }}
        },
        idempotencyKey: (window.crypto && typeof crypto.randomUUID === 'function') 
            ? crypto.randomUUID() 
            : 'nk_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9),
        paymentMethod: 'xendit_qris',
        notes: '',
        isLoading: false,
        errorMessage: '',

        get shippingCost() {
            return this.shippingCosts[this.shippingMethod] || 15000;
        },

        get grandTotal() {
            return $store.cart.subtotal + this.shippingCost;
        },

        formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        },

        async submitOrder() {
            // Idempotency: Prevent duplicate submissions on double-click
            if (this.isLoading) {
                return;
            }

            if ($store.cart.items.length === 0) {
                this.errorMessage = 'Keranjang belanja kamu masih kosong!';
                return;
            }

            if (!this.name || !this.name.trim() || !this.phone || !this.phone.trim() || !this.address || !this.address.trim()) {
                this.errorMessage = 'Mohon lengkapi Nama Penerima, Nomor WhatsApp, dan Alamat Pengiriman.';
                if (!this.name || !this.name.trim()) {
                    const el = document.getElementById('input_customer_name');
                    el?.focus();
                    el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else if (!this.phone || !this.phone.trim()) {
                    const el = document.getElementById('input_customer_phone');
                    el?.focus();
                    el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else if (!this.address || !this.address.trim()) {
                    const el = document.getElementById('input_customer_address');
                    el?.focus();
                    el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            this.isLoading = true;
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route('checkout.place') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        'X-Idempotency-Key': this.idempotencyKey,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        idempotency_key: this.idempotencyKey,
                        customer_name: this.name,
                        customer_phone: this.phone,
                        customer_email: this.email,
                        customer_address: this.address,
                        customer_city: this.city,
                        shipping_method: this.shippingMethod,
                        payment_method: this.paymentMethod,
                        items: $store.cart.items,
                        is_gift: $store.cart.isGift,
                        gift_recipient_name: $store.cart.giftData.recipientName,
                        gift_sender_name: $store.cart.giftData.senderName,
                        gift_card_template_id: $store.cart.giftData.templateId,
                        gift_message: $store.cart.giftData.message,
                        notes: this.notes
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    $store.cart.clearCart();

                    // Save to device order history for customer tracking menu
                    try {
                        const myOrders = JSON.parse(localStorage.getItem('ngemilkukis_my_orders') || '[]');
                        if (data.order_number && !myOrders.includes(data.order_number)) {
                            myOrders.unshift(data.order_number);
                            localStorage.setItem('ngemilkukis_my_orders', JSON.stringify(myOrders.slice(0, 25)));
                        }
                    } catch (e) {
                        console.error('Error saving local order history', e);
                    }

                    // Direct reliable redirect to Xendit invoice URL
                    window.location.href = data.redirect_url;
                } else {
                    let errMsg = data.message;
                    if (data.errors && typeof data.errors === 'object') {
                        const firstKey = Object.keys(data.errors)[0];
                        if (firstKey && data.errors[firstKey].length > 0) {
                            errMsg = data.errors[firstKey][0];
                        }
                    }
                    this.errorMessage = errMsg || 'Terjadi kesalahan saat memproses pesanan. Silakan periksa kembali formulir.';
                    this.isLoading = false;
                }
            } catch (err) {
                console.error(err);
                this.errorMessage = 'Gagal menghubungi server. Silakan coba lagi.';
                this.isLoading = false;
            }
        }
     }">

    <!-- Breadcrumb -->
    <div class="mb-8 flex items-center gap-2 text-xs font-semibold text-[#6d4630]">
        <a href="{{ route('home') }}" class="hover:text-[#b87d4b]">Beranda</a>
        <span>&rsaquo;</span>
        <span class="text-[#2d180c]">Checkout & Pembayaran</span>
    </div>

    <!-- Error Alert -->
    <div x-cloak x-show="errorMessage" class="mb-8 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center gap-3">
        <span>⚠️</span>
        <span x-text="errorMessage"></span>
    </div>

    <!-- Empty Cart Notice -->
    <div x-show="$store.cart.items.length === 0" class="py-16 text-center bg-white rounded-3xl border border-[#ede7e2] p-8 max-w-lg mx-auto">
        <span class="text-4xl block mb-4">🍪</span>
        <h2 class="font-bold text-2xl text-[#2d180c]">Keranjangmu Masih Kosong</h2>
        <p class="text-xs text-[#6d4630] mt-1.5 mb-6">Pilih kukis lumer favoritmu terlebih dahulu sebelum melakukan checkout.</p>
        <!-- Tombol Coklat Muda -->
        <a href="{{ route('home') }}" class="inline-block bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-xs px-7 py-3.5 rounded-full shadow-sm transition">
            Lihat Menu Kukis &rarr;
        </a>
    </div>

    <!-- Main Checkout Grid (Putih 60% + Coklat Tua 30% + Tombol Coklat Muda 10%) -->
    <div x-show="$store.cart.items.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        
        <!-- Left: Form Input Details -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Section 1: Customer Info -->
            <div class="bg-white p-7 sm:p-8 rounded-3xl border border-[#ede7e2] shadow-xs space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-[#ede7e2]">
                    <span class="w-7 h-7 rounded-lg bg-[#b87d4b] text-white font-bold text-xs flex items-center justify-center">1</span>
                    <h3 class="font-bold text-base sm:text-lg text-[#2d180c]">Data Penerima & Kontak</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">Nama Lengkap Penerima *</label>
                        <input type="text" id="input_customer_name" x-model="name" placeholder="Contoh: Anang Pratama" required
                               class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 text-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">Nomor WhatsApp Aktif *</label>
                            <input type="tel" id="input_customer_phone" x-model="phone" placeholder="081234567890" required
                                   class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 text-sm">
                            <span class="text-[11px] text-[#6d4630] mt-1 block">Untuk konfirmasi & link tracking kurir</span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">Email (Opsional)</label>
                            <input type="email" id="input_customer_email" x-model="email" placeholder="nama@email.com"
                                   class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 text-sm">
                            <span class="text-[11px] text-[#6d4630] mt-1 block">Salinan invoice dikirim ke sini</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">Alamat Pengiriman Lengkap *</label>
                        <textarea id="input_customer_address" x-model="address" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, patokan..." required
                                  class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 text-sm leading-relaxed"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">Kota / Area</label>
                        <input type="text" x-model="city" placeholder="Jakarta Selatan / Tangerang / Kota lainnya"
                               class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 text-sm">
                    </div>
                </div>
            </div>

            <!-- Section 2: Delivery Method -->
            <div class="bg-white p-7 sm:p-8 rounded-3xl border border-[#ede7e2] shadow-xs space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-[#ede7e2]">
                    <span class="w-7 h-7 rounded-lg bg-[#b87d4b] text-white font-bold text-xs flex items-center justify-center">2</span>
                    <div>
                        <h3 class="font-bold text-base sm:text-lg text-[#2d180c]">Pilihan Layanan Pengiriman</h3>
                        <p class="text-xs text-[#6d4630]">Pilih kurir yang sesuai dengan lokasi dan kebutuhan kesegaran kukis.</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <!-- Option 1: Instant -->
                    <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition"
                           :class="shippingMethod === 'instant_grab' ? 'border-[#b87d4b] bg-[#f6efe8] ring-2 ring-[#b87d4b]' : 'border-[#ede7e2] hover:bg-stone-50'">
                        <div class="flex items-center gap-3">
                            <input type="radio" value="instant_grab" x-model="shippingMethod" class="text-[#b87d4b] focus:ring-[#b87d4b]">
                            <div>
                                <span class="font-bold text-sm text-[#2d180c] block">Grab / Gojek Instant</span>
                                <span class="text-xs text-[#6d4630]">Kukis hangat sampai dalam 1-2 jam (Area Lokal)</span>
                            </div>
                        </div>
                        <span class="font-bold text-sm text-[#2d180c]">Rp <span x-text="Number(shippingCosts['instant_grab']).toLocaleString('id-ID')"></span></span>
                    </label>

                    <!-- Option 2: Sameday -->
                    <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition"
                           :class="shippingMethod === 'sameday_gojek' ? 'border-[#b87d4b] bg-[#f6efe8] ring-2 ring-[#b87d4b]' : 'border-[#ede7e2] hover:bg-stone-50'">
                        <div class="flex items-center gap-3">
                            <input type="radio" value="sameday_gojek" x-model="shippingMethod" class="text-[#b87d4b] focus:ring-[#b87d4b]">
                            <div>
                                <span class="font-bold text-sm text-[#2d180c] block">Sameday Delivery</span>
                                <span class="text-xs text-[#6d4630]">Sampai di hari yang sama (Sebelum pukul 18.00 WIB)</span>
                            </div>
                        </div>
                        <span class="font-bold text-sm text-[#2d180c]">Rp <span x-text="Number(shippingCosts['sameday_gojek']).toLocaleString('id-ID')"></span></span>
                    </label>

                    <!-- Option 3: Paxel Food -->
                    <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition"
                           :class="shippingMethod === 'paxel_food' ? 'border-[#b87d4b] bg-[#f6efe8] ring-2 ring-[#b87d4b]' : 'border-[#ede7e2] hover:bg-stone-50'">
                        <div class="flex items-center gap-3">
                            <input type="radio" value="paxel_food" x-model="shippingMethod" class="text-[#b87d4b] focus:ring-[#b87d4b]">
                            <div>
                                <span class="font-bold text-sm text-[#2d180c] block">Paxel Food (Pendingin Khusus Makanan)</span>
                                <span class="text-xs text-[#6d4630]">Pengiriman antarkota dengan cold storage & jaminan aman</span>
                            </div>
                        </div>
                        <span class="font-bold text-sm text-[#2d180c]">Rp <span x-text="Number(shippingCosts['paxel_food']).toLocaleString('id-ID')"></span></span>
                    </label>

                    <!-- Option 4: Regular -->
                    <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition"
                           :class="shippingMethod === 'regular_jne' ? 'border-[#b87d4b] bg-[#f6efe8] ring-2 ring-[#b87d4b]' : 'border-[#ede7e2] hover:bg-stone-50'">
                        <div class="flex items-center gap-3">
                            <input type="radio" value="regular_jne" x-model="shippingMethod" class="text-[#b87d4b] focus:ring-[#b87d4b]">
                            <div>
                                <span class="font-bold text-sm text-[#2d180c] block">JNE / SiCepat Reguler (1-2 Hari)</span>
                                <span class="text-xs text-[#6d4630]">Dipacking dengan bubble wrap tebal & seal kedap udara</span>
                            </div>
                        </div>
                        <span class="font-bold text-sm text-[#2d180c]">Rp <span x-text="Number(shippingCosts['regular_jne']).toLocaleString('id-ID')"></span></span>
                    </label>
                </div>
            </div>

            <!-- Section 3: Custom Greeting Card (Gift Option) -->
            <div class="bg-white p-7 sm:p-8 rounded-3xl border border-[#ede7e2] shadow-xs space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-[#ede7e2]">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-[#b87d4b] text-white font-bold text-xs flex items-center justify-center">3</span>
                        <div>
                            <h3 class="font-bold text-base sm:text-lg text-[#2d180c]">🎁 Kirim sebagai Kado / Hadiah?</h3>
                            <p class="text-xs text-[#6d4630]">Gratis kartu ucapan eksklusif berpita untuk penerima.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="$store.cart.isGift" class="sr-only peer">
                        <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#b87d4b]"></div>
                    </label>
                </div>

                <!-- Gift details when enabled -->
                <div x-show="$store.cart.isGift" x-transition class="space-y-4 pt-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#2d180c] mb-1">Nama Penerima Kado (To:)</label>
                            <input type="text" x-model="$store.cart.giftData.recipientName" placeholder="Contoh: Sahabatku Maya"
                                   class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] bg-white text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#2d180c] mb-1">Nama Pengirim (From:)</label>
                            <input type="text" x-model="$store.cart.giftData.senderName" placeholder="Contoh: Dari Rian"
                                   class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] bg-white text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#2d180c] mb-1">Pesan di Kartu Ucapan:</label>
                        <textarea x-model="$store.cart.giftData.message" rows="3" placeholder="Tuliskan ucapan manismu..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] bg-white text-sm leading-relaxed"></textarea>
                    </div>

                    <!-- Selected Template Indicator -->
                    <div class="p-3 bg-[#f6efe8] rounded-xl border border-[#ede7e2] text-xs text-[#2d180c] flex items-center justify-between">
                        <span>Tema: <strong x-text="$store.cart.giftData.templateName"></strong></span>
                        <a href="{{ route('home') }}#kartu-ucapan" class="text-[#b87d4b] font-bold hover:underline">Ganti Desain &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Section 4: Payment Method (Xendit) -->
            <div class="bg-white p-7 sm:p-8 rounded-3xl border border-[#ede7e2] shadow-xs space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-[#ede7e2]">
                    <span class="w-7 h-7 rounded-lg bg-[#b87d4b] text-white font-bold text-xs flex items-center justify-center">4</span>
                    <div>
                        <h3 class="font-bold text-base sm:text-lg text-[#2d180c]">Metode Pembayaran (Otomatis & Aman)</h3>
                        <p class="text-xs text-[#6d4630]">Didukung oleh Xendit Payment Gateway untuk verifikasi otomatis.</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <!-- Option 1: QRIS -->
                    <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition"
                           :class="paymentMethod === 'xendit_qris' ? 'border-[#b87d4b] bg-[#f6efe8] ring-2 ring-[#b87d4b]' : 'border-[#ede7e2] hover:bg-stone-50'">
                        <div class="flex items-center gap-3">
                            <input type="radio" value="xendit_qris" x-model="paymentMethod" class="text-[#b87d4b] focus:ring-[#b87d4b]">
                            <div>
                                <span class="font-bold text-sm text-[#2d180c] flex items-center gap-2">
                                    <span>📱 Xendit QRIS (Semua Bank & E-Wallet)</span>
                                    <span class="bg-[#b87d4b] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Instan</span>
                                </span>
                                <span class="text-xs text-[#6d4630] mt-0.5 block">BCA Mobile, GoPay, OVO, ShopeePay, DANA, Livin Mandiri</span>
                            </div>
                        </div>
                    </label>

                    <!-- Option 2: Virtual Account -->
                    <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition"
                           :class="paymentMethod === 'xendit_va' ? 'border-[#b87d4b] bg-[#f6efe8] ring-2 ring-[#b87d4b]' : 'border-[#ede7e2] hover:bg-stone-50'">
                        <div class="flex items-center gap-3">
                            <input type="radio" value="xendit_va" x-model="paymentMethod" class="text-[#b87d4b] focus:ring-[#b87d4b]">
                            <div>
                                <span class="font-bold text-sm text-[#2d180c] block">🏦 Xendit Virtual Account</span>
                                <span class="text-xs text-[#6d4630]">BCA, Mandiri, BRI, BNI Virtual Account otomatis</span>
                            </div>
                        </div>
                    </label>

                    <!-- Option 3: International Credit/Debit Card (Visa / Mastercard - Cocok untuk Pembeli Luar Negeri / Thailand) -->
                    <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition"
                           :class="paymentMethod === 'xendit_card' ? 'border-[#b87d4b] bg-[#f6efe8] ring-2 ring-[#b87d4b]' : 'border-[#ede7e2] hover:bg-stone-50'">
                        <div class="flex items-center gap-3">
                            <input type="radio" value="xendit_card" x-model="paymentMethod" class="text-[#b87d4b] focus:ring-[#b87d4b]">
                            <div>
                                <span class="font-bold text-sm text-[#2d180c] flex items-center gap-2">
                                    <span>💳 Kartu Kredit / Debit Internasional</span>
                                    <span class="bg-[#b87d4b] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Visa &bull; Mastercard</span>
                                </span>
                                <span class="text-xs text-[#6d4630] mt-0.5 block">Mendukung kartu luar negeri (Thailand, Singapura, dll) dengan verifikasi aman 3D Secure</span>
                            </div>
                        </div>
                    </label>

                    <!-- Option 4: WhatsApp Direct -->
                    <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition"
                           :class="paymentMethod === 'whatsapp_direct' ? 'border-[#b87d4b] bg-[#f6efe8] ring-2 ring-[#b87d4b]' : 'border-[#ede7e2] hover:bg-stone-50'">
                        <div class="flex items-center gap-3">
                            <input type="radio" value="whatsapp_direct" x-model="paymentMethod" class="text-[#b87d4b] focus:ring-[#b87d4b]">
                            <div>
                                <span class="font-bold text-sm text-[#2d180c] block">💬 Order via WhatsApp Bisnis</span>
                                <span class="text-xs text-[#6d4630]">Formulir terisi otomatis ke WhatsApp admin untuk bantuan manual</span>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Notes for Baker -->
                <div class="pt-2">
                    <label class="block text-xs font-semibold text-[#2d180c] mb-1">Catatan Tambahan untuk Baker (Opsional):</label>
                    <input type="text" x-model="notes" placeholder="Contoh: Tolong kirim sebelum jam 3 sore yaa / packing pita warna coklat"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] text-sm">
                </div>
            </div>

        </div>

        <!-- Right: Order Summary Sidebar (Putih Bersih 60%) -->
        <div class="lg:col-span-5 sticky top-28 space-y-6">
            <div class="bg-white p-7 rounded-3xl border border-[#ede7e2] shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-[#ede7e2]">
                    <h3 class="font-bold text-base text-[#2d180c]">Ringkasan Pesanan</h3>
                    <span class="text-xs font-bold text-[#b87d4b]" x-text="$store.cart.count + ' kukis' "></span>
                </div>

                <!-- Items list -->
                <div class="space-y-3.5 max-h-72 overflow-y-auto pr-1">
                    <template x-for="item in $store.cart.items" :key="item.id">
                        <div class="flex items-center justify-between gap-3 text-sm py-1 border-b border-[#f6efe8] last:border-b-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <img :src="item.image" :alt="item.name" class="w-12 h-12 rounded-xl object-cover border border-[#ede7e2] shrink-0">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-xs text-[#2d180c] truncate max-w-[170px]" x-text="item.name"></h4>
                                    <span class="text-[11px] text-[#6d4630]" x-text="item.quantity + ' pcs × ' + formatRupiah(item.price)"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="font-bold text-xs text-[#2d180c]" x-text="formatRupiah(item.price * item.quantity)"></span>
                                <button @click="$store.cart.removeItem(item.id)" 
                                        type="button"
                                        class="w-6 h-6 rounded-md text-stone-300 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition cursor-pointer"
                                        title="Hapus kukis dari keranjang"
                                        aria-label="Hapus kukis dari keranjang">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Price Calculations -->
                <div class="pt-4 border-t border-[#ede7e2] space-y-2.5 text-sm">
                    <div class="flex items-center justify-between text-stone-600 text-xs">
                        <span>Subtotal Produk</span>
                        <span class="font-bold text-[#2d180c]" x-text="formatRupiah($store.cart.subtotal)"></span>
                    </div>

                    <div class="flex items-center justify-between text-stone-600 text-xs">
                        <span>Ongkos Kirim</span>
                        <span class="font-bold text-[#2d180c]" x-text="formatRupiah(shippingCost)"></span>
                    </div>

                    <div x-show="$store.cart.isGift" class="flex items-center justify-between text-[#b87d4b] text-xs font-semibold">
                        <span>Custom Greeting Card</span>
                        <span class="uppercase text-[10px] bg-[#f6efe8] text-[#b87d4b] px-2 py-0.5 rounded-full font-bold">Gratis</span>
                    </div>

                    <div class="pt-3 border-t border-[#ede7e2] flex items-center justify-between">
                        <span class="font-bold text-sm text-[#2d180c]">Total Pembayaran</span>
                        <span class="font-extrabold text-2xl text-[#b87d4b]" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <!-- Inline Error Alert right above button -->
                <div x-cloak x-show="errorMessage" class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5">
                    <span class="text-base shrink-0">⚠️</span>
                    <span x-text="errorMessage"></span>
                </div>

                <!-- Submit Button (Tombol Coklat Muda 10%) -->
                <button @click="submitOrder()" 
                        :disabled="isLoading"
                        type="button"
                        id="btn-pay-order"
                        class="w-full bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold py-4 px-6 rounded-full shadow-sm transition transform active:scale-98 flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed cursor-pointer">
                    <span x-show="!isLoading">Bayar Pesanan Sekarang &rarr;</span>
                    <span x-show="isLoading" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Menyiapkan Invoice Xendit...</span>
                    </span>
                </button>

                <!-- Secure Guarantee -->
                <div class="pt-2 text-center text-[11px] text-[#6d4630] space-y-1">
                    <p>🔒 Transaksi aman dan terverifikasi otomatis.</p>
                    <p>Butuh bantuan? <a href="https://wa.me/6281234567890" target="_blank" class="text-[#b87d4b] font-bold underline">Hubungi CS WhatsApp</a></p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
