@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number . ' - NgemilKukis')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8"
     x-data="{
        isPending: {{ $order->status === 'pending' ? 'true' : 'false' }},
        orderNumber: '{{ $order->order_number }}',
        invoiceUrl: '{{ $order->xendit_invoice_url }}',
        openXenditPopup(url) {
            const width = 540;
            const height = 780;
            const left = Math.max(0, (window.screen.width - width) / 2);
            const top = Math.max(0, (window.screen.height - height) / 2);
            const win = window.open(url, 'XenditPaymentPopup', `width=${width},height=${height},top=${top},left=${left},scrollbars=yes,status=no,resizable=yes`);
            if (win && win.focus) win.focus();
        },
        init() {
            if (this.isPending) {
                const interval = setInterval(async () => {
                    try {
                        const res = await fetch(`/order/${this.orderNumber}/status`);
                        if (res.ok) {
                            const data = await res.json();
                            if (data.is_paid) {
                                clearInterval(interval);
                                window.location.reload();
                            }
                        }
                    } catch (e) {
                        console.error('Polling status error', e);
                    }
                }, 3000);
            }
        }
     }">
    
    <!-- Flash Status Message if simulated -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-[#f6efe8] border border-[#ede7e2] text-[#2d180c] text-sm font-semibold flex items-center gap-3">
            <span class="text-xl">✅</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Top Card: Order Header (Putih Bersih 60%) -->
    <div class="bg-white p-7 sm:p-10 rounded-3xl border border-[#ede7e2] shadow-sm text-center space-y-4 relative overflow-hidden">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-[#f6efe8] flex items-center justify-center text-2xl text-[#b87d4b]">
            @if ($order->status === 'paid')
                🍪
            @else
                💳
            @endif
        </div>

        <div class="space-y-1">
            <span class="text-xs font-bold uppercase tracking-widest text-[#b87d4b]">Konfirmasi Pesanan</span>
            <h1 class="font-extrabold text-3xl sm:text-4xl text-[#2d180c]">
                @if ($order->status === 'paid')
                    Terima Kasih, Pesananmu Sedang Diproses!
                @else
                    Pesanan Berhasil Dibuat!
                @endif
            </h1>
            <p class="text-sm text-[#6d4630]">
                Nomor Pesanan: <strong class="text-[#2d180c] tracking-wider">{{ $order->order_number }}</strong>
            </p>
        </div>

        <!-- Status Badge -->
        <div class="pt-2">
            @if ($order->status === 'paid')
                <span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-800 font-bold text-xs px-4 py-1.5 rounded-full border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Sudah Dibayar &bull; Siap Dipanggang / Dikirim</span>
                </span>
            @elseif ($order->status === 'pending')
                <span class="inline-flex items-center gap-2 bg-[#f6efe8] text-[#b87d4b] font-bold text-xs px-4 py-1.5 rounded-full border border-[#ede7e2]">
                    <span class="w-2 h-2 rounded-full bg-[#b87d4b] animate-pulse"></span>
                    <span>Menunggu Pembayaran (Otomatis Diperbarui)</span>
                </span>
            @else
                <span class="inline-block bg-stone-100 text-stone-800 font-bold text-xs px-4 py-1.5 rounded-full">
                    {{ $order->statusLabel() }}
                </span>
            @endif
        </div>
    </div>

    <!-- Xendit Payment Box -->
    @if ($order->status === 'pending')
        <div class="bg-white p-7 sm:p-10 rounded-3xl border border-[#ede7e2] shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#ede7e2]">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⚡</span>
                    <div>
                        <h3 class="font-bold text-lg text-[#2d180c]">Instruksi Pembayaran Xendit</h3>
                        <p class="text-xs text-[#6d4630]">Selesaikan pembayaran sebelum batas waktu berakhir</p>
                    </div>
                </div>
                <span class="font-extrabold text-xl sm:text-2xl text-[#b87d4b]">
                    {{ $order->formattedTotal() }}
                </span>
            </div>

            @if($order->xendit_invoice_url && str_contains($order->xendit_invoice_url, 'xendit.co'))
                <!-- Xendit Pop-up Window Launcher Banner -->
                <div class="p-4 bg-[#f6efe8] rounded-2xl border border-[#ede7e2] flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="space-y-0.5 text-center sm:text-left">
                        <span class="font-bold text-sm text-[#2d180c] block">💳 Bayar dengan Xendit (Kartu Kredit / QRIS / VA)</span>
                        <span class="text-xs text-[#6d4630]">Buka pop-up pembayaran resmi Xendit, halaman ini akan otomatis berganti ke status Lunas.</span>
                    </div>
                    <button @click="openXenditPopup(invoiceUrl)" 
                            type="button"
                            class="bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-xs px-5 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow-xs cursor-pointer whitespace-nowrap">
                        <span>Buka Jendela Pembayaran</span>
                        <span>↗</span>
                    </button>
                </div>
            @endif

            @if ($order->payment_method === 'xendit_qris')
                <!-- QRIS Display -->
                <div class="flex flex-col sm:flex-row items-center gap-8 justify-center py-4">
                    <div class="bg-white p-4 rounded-2xl border border-[#ede7e2] shadow-xs text-center space-y-2">
                        <!-- Simulated QR Code SVG -->
                        <div class="w-48 h-48 bg-[#2d180c] p-2 rounded-xl flex items-center justify-center text-white text-xs font-mono">
                            <div class="bg-white w-full h-full p-2 flex flex-col justify-between items-center rounded-lg">
                                <div class="w-full flex justify-between">
                                    <div class="w-10 h-10 border-4 border-black flex items-center justify-center">
                                        <div class="w-4 h-4 bg-black"></div>
                                    </div>
                                    <div class="w-10 h-10 border-4 border-black flex items-center justify-center">
                                        <div class="w-4 h-4 bg-black"></div>
                                    </div>
                                </div>
                                <div class="font-extrabold text-[10px] tracking-wider text-stone-800 uppercase">
                                    QRIS NGEMILKUKIS
                                </div>
                                <div class="w-full flex justify-between">
                                    <div class="w-10 h-10 border-4 border-black flex items-center justify-center">
                                        <div class="w-4 h-4 bg-black"></div>
                                    </div>
                                    <div class="text-[9px] font-bold text-stone-600">
                                        XENDIT
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-[#2d180c] block">Scan dengan Aplikasi Bank / E-Wallet</span>
                    </div>

                    <div class="space-y-3 max-w-sm text-sm text-[#2d180c]">
                        <p class="font-bold text-[#2d180c]">Cara Membayar dengan QRIS:</p>
                        <ol class="list-decimal list-inside space-y-1.5 text-xs text-[#6d4630]">
                            <li>Buka BCA Mobile, GoPay, OVO, ShopeePay, DANA, atau m-Banking Anda.</li>
                            <li>Pilih menu <strong>Scan / Bayar QRIS</strong>.</li>
                            <li>Arahkan kamera ke kode QR di samping.</li>
                            <li>Periksa nominal tagihan sebesar <strong>{{ $order->formattedTotal() }}</strong>.</li>
                            <li>Konfirmasi PIN pembayaran Anda.</li>
                        </ol>
                    </div>
                </div>
            @elseif ($order->payment_method === 'xendit_va')
                <!-- Virtual Account Display -->
                <div class="p-5 bg-white rounded-2xl border border-[#ede7e2] space-y-3">
                    <p class="text-xs text-[#6d4630] uppercase font-bold tracking-wider">Nomor Virtual Account (BCA / Mandiri / BRI / BNI):</p>
                    <div class="flex items-center justify-between bg-[#f6efe8] p-4 rounded-xl border border-[#ede7e2]">
                        <span class="font-mono font-bold text-xl text-[#2d180c]">8808 2026 1023 9988</span>
                        <!-- Tombol Coklat Muda -->
                        <button onclick="navigator.clipboard.writeText('8808202610239988'); alert('Nomor Virtual Account berhasil disalin!');" 
                                class="bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-xs px-4 py-2 rounded-full transition cursor-pointer">
                            Salin VA
                        </button>
                    </div>
                    <p class="text-xs text-[#6d4630]">Total Pembayaran: <strong>{{ $order->formattedTotal() }}</strong></p>
                </div>
            @endif

            <!-- Local Development Simulation Button (Tombol Coklat Muda 10%) -->
            <div class="pt-4 border-t border-[#ede7e2] flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-[#6d4630]">
                    <span class="font-bold block text-[#2d180c]">💡 Ingin mencoba alur pelunasan instan?</span>
                    Klik tombol di samping untuk mensimulasikan status webhook pembayaran Xendit.
                </div>
                <form action="{{ route('orders.simulate-pay', $order->order_number) }}" 
                      method="POST"
                      x-data="{ isSubmitting: false }"
                      @submit="if (isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true">
                    @csrf
                    <!-- Tombol Coklat Muda -->
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-xs px-6 py-3 rounded-full shadow-xs transition transform active:scale-95 whitespace-nowrap cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">✓ Simulasikan Bayar Lunas Sekarang</span>
                        <span x-show="isSubmitting" class="flex items-center gap-2">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Memproses Verifikasi...</span>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Gift Card Preview (if order has gift) -->
    @if ($order->is_gift)
        <div class="bg-white p-7 sm:p-10 rounded-3xl border border-[#ede7e2] shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#ede7e2]">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">💌</span>
                    <div>
                        <h3 class="font-bold text-lg text-[#2d180c]">Kartu Ucapan Kado Kamu</h3>
                        <p class="text-xs text-[#6d4630]">Kartu ini akan dicetak dan dimasukkan ke dalam kemasan boks kukis.</p>
                    </div>
                </div>
                <button onclick="window.print()" class="text-xs font-bold text-[#b87d4b] hover:underline flex items-center gap-1 cursor-pointer">
                    <span>🖨️ Cetak / Print Kartu</span>
                </button>
            </div>

            <!-- Card physical render -->
            <div class="max-w-lg mx-auto bg-[#fbfbfb] p-6 sm:p-8 rounded-2xl border border-[#ede7e2] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#ede7e2]">
                    <span class="font-bold text-xs text-[#b87d4b] uppercase tracking-wider">
                        {{ $order->greetingCardTemplate?->name ?? 'Special Gift Card' }}
                    </span>
                    <span class="text-xl">{{ $order->greetingCardTemplate?->icon ?? '🎁' }}</span>
                </div>

                <div class="space-y-3">
                    <div class="text-xs font-bold text-[#6d4630] uppercase">
                        Dear: <span class="text-sm font-bold text-[#2d180c] normal-case">{{ $order->gift_recipient_name ?? 'Sahabatku' }}</span>
                    </div>

                    <blockquote class="text-sm text-[#2d180c] leading-relaxed border-l-2 border-[#b87d4b] pl-4 py-1">
                        "{{ $order->gift_message }}"
                    </blockquote>

                    <div class="text-right pt-2">
                        <span class="text-xs text-[#6d4630] block">Hangat dari,</span>
                        <span class="font-bold text-sm text-[#2d180c]">{{ $order->gift_sender_name ?? 'Teman Manismu' }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Order Items Breakdown -->
    <div class="bg-white p-7 sm:p-10 rounded-3xl border border-[#ede7e2] shadow-xs space-y-6">
        <h3 class="font-bold text-base text-[#2d180c] pb-3 border-b border-[#ede7e2]">
            Rincian Kukis yang Dipesan
        </h3>

        <div class="divide-y divide-[#ede7e2]">
            @foreach ($order->items as $item)
                <div class="py-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        @if ($item->product?->image)
                            <img src="{{ $item->product->image }}" alt="{{ $item->product_name }}" 
                                 class="w-14 h-14 rounded-xl object-cover border border-[#ede7e2]">
                        @else
                            <div class="w-14 h-14 rounded-xl bg-[#f6efe8] flex items-center justify-center text-2xl">
                                🍪
                            </div>
                        @endif
                        <div>
                            <h4 class="font-bold text-sm text-[#2d180c]">{{ $item->product_name }}</h4>
                            <p class="text-xs text-[#6d4630]">
                                {{ $item->quantity }} pcs × Rp {{ number_format($item->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <span class="font-bold text-sm text-[#2d180c]">
                        {{ $item->formattedSubtotal() }}
                    </span>
                </div>
            @endforeach
        </div>

        <!-- Calculations -->
        <div class="pt-4 border-t border-[#ede7e2] space-y-2 text-sm">
            <div class="flex justify-between text-[#6d4630] text-xs">
                <span>Subtotal Kukis</span>
                <span class="font-bold text-[#2d180c]">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-[#6d4630] text-xs">
                <span>Ongkos Kirim ({{ ucfirst(str_replace('_', ' ', $order->shipping_method)) }})</span>
                <span class="font-bold text-[#2d180c]">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            <div class="pt-3 border-t border-[#ede7e2] flex justify-between items-center text-base">
                <span class="font-bold text-[#2d180c]">Total Pembayaran</span>
                <span class="font-extrabold text-2xl text-[#b87d4b]">
                    {{ $order->formattedTotal() }}
                </span>
            </div>
        </div>

        <!-- Shipping & Recipient Details -->
        <div class="pt-4 border-t border-[#ede7e2] grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-[#2d180c]">
            <div class="space-y-1">
                <span class="font-bold uppercase tracking-wider text-[#6d4630] block">Tujuan Pengiriman:</span>
                <p class="font-bold text-sm text-[#2d180c]">{{ $order->customer_name }} ({{ $order->customer_phone }})</p>
                <p class="text-[#6d4630] leading-relaxed">{{ $order->customer_address }}</p>
                @if ($order->customer_city)
                    <p class="text-[#6d4630]">{{ $order->customer_city }}</p>
                @endif
            </div>

            <div class="space-y-1">
                <span class="font-bold uppercase tracking-wider text-[#6d4630] block">Metode Pembayaran:</span>
                <p class="font-bold text-sm text-[#2d180c] uppercase">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                @if ($order->notes)
                    <span class="font-bold uppercase tracking-wider text-[#6d4630] block mt-2">Catatan Pesanan:</span>
                    <p class="text-[#6d4630] italic">"{{ $order->notes }}"</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions & Social Connect (Semua Tombol Menggunakan Coklat Muda 10%) -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
        <!-- WhatsApp Share / Confirmation -->
        @php
            $waMessage = urlencode("Halo Admin NgemilKukis! Saya mau konfirmasi pesanan *" . $order->order_number . "* atas nama *" . $order->customer_name . "* dengan total *" . $order->formattedTotal() . "*. Mohon dicek yaa 🙏🍪");
        @endphp
        <a href="https://wa.me/6281234567890?text={{ $waMessage }}" 
           target="_blank" rel="noopener noreferrer"
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#2d180c] hover:bg-[#b87d4b] text-white font-bold text-xs px-7 py-3.5 rounded-full shadow-xs transition cursor-pointer">
            <span>💬</span>
            <span>Konfirmasi di WhatsApp</span>
        </a>

        <!-- Instagram Visit (Tombol Coklat Muda 10%) -->
        <a href="https://instagram.com" 
           target="_blank" rel="noopener noreferrer"
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-xs px-7 py-3.5 rounded-full shadow-xs transition cursor-pointer">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
            <span>Tag Kami di Instagram @ngemilkukis</span>
        </a>

        <!-- Back to Home -->
        <a href="{{ route('home') }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#f6efe8] hover:bg-[#ede7e2] text-[#2d180c] font-bold text-xs px-7 py-3.5 rounded-full border border-[#ede7e2] transition cursor-pointer">
            <span>&larr; Belanja Lagi</span>
        </a>
    </div>

</div>
@endsection
