@extends('layouts.admin')

@section('title', 'Rincian Pesanan ' . $order->order_number)
@section('page_title', 'Rincian Pesanan: ' . $order->order_number)

@section('content')
<div class="space-y-6">

    <!-- Top Navigation / Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#6d4630] hover:text-[#2d180c]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Pesanan</span>
        </a>

        <a href="{{ route('orders.show', $order->order_number) }}" target="_blank" 
           class="inline-flex items-center gap-1.5 text-xs font-bold text-[#b87d4b] hover:underline">
            <span>Lihat Halaman Invoice Pelanggan</span>
            <span>&rarr;</span>
        </a>
    </div>

    <!-- Status Action Bar (Putih Bersih 60%) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-[#6d4630] block mb-1">Status Pesanan Saat Ini:</span>
            <div class="flex items-center gap-3">
                @php
                    $badgeStyles = [
                        'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'paid' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'baking' => 'bg-orange-50 text-orange-800 border-orange-200',
                        'shipped' => 'bg-blue-50 text-blue-800 border-blue-200',
                        'delivered' => 'bg-teal-50 text-teal-800 border-teal-200',
                        'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
                    ];
                    $style = $badgeStyles[$order->status] ?? 'bg-stone-50 text-stone-800 border-stone-200';
                @endphp
                <span class="inline-block px-4 py-1.5 rounded-full text-sm font-extrabold border {{ $style }}">
                    {{ $order->statusLabel() }}
                </span>
                <span class="text-xs text-[#6d4630]">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} WIB</span>
            </div>
        </div>

        <!-- Quick Status Update Form -->
        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="flex items-center gap-3 w-full md:w-auto">
            @csrf
            @method('PATCH')
            
            <select name="status" class="px-4 py-2.5 rounded-xl border border-[#ede7e2] text-xs font-bold text-[#2d180c] focus:border-[#b87d4b] outline-none bg-[#faf7f4]">
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Menunggu Pembayaran</option>
                <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>✅ Sudah Dibayar (Lunas)</option>
                <option value="baking" {{ $order->status === 'baking' ? 'selected' : '' }}>🔥 Sedang Dipanggang (Baking)</option>
                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>🛵 Dalam Pengiriman (Shipped)</option>
                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>🎉 Terkirim / Selesai</option>
                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Batalkan Pesanan</option>
            </select>

            <button type="submit" 
                    class="bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-xs transition active:scale-95 cursor-pointer flex-shrink-0">
                Ubah Status
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Purchased Items & Gift Card (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Items Table Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs">
                <h3 class="text-base font-extrabold text-[#2d180c] mb-6">Daftar Varian Kukis Dipesan</h3>
                
                <div class="divide-y divide-[#ede7e2]">
                    @foreach($order->items as $item)
                        <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl overflow-hidden bg-stone-100 flex-shrink-0 border border-[#ede7e2]">
                                    <img src="{{ $item->product->image ?? 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=400&q=80' }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-extrabold text-[#2d180c] text-sm">{{ $item->product_name }}</p>
                                    <p class="text-xs text-[#6d4630] mt-0.5">
                                        @if($item->product_type === 'pre_order')
                                            <span class="text-amber-800 font-semibold">📅 Pre-Order</span>
                                        @else
                                            <span class="text-emerald-800 font-semibold">⚡ Ready Stock</span>
                                        @endif
                                        &bull; Rp {{ number_format($item->price, 0, ',', '.') }} &times; {{ $item->quantity }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-extrabold text-sm text-[#2d180c]">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Price Totals Breakdown -->
                <div class="mt-8 pt-6 border-t border-[#ede7e2] space-y-2 text-xs">
                    <div class="flex justify-between text-[#6d4630]">
                        <span>Subtotal Produk</span>
                        <span class="font-bold text-[#2d180c]">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[#6d4630]">
                        <span>Biaya Pengiriman ({{ strtoupper($order->shipping_method) }})</span>
                        <span class="font-bold text-[#2d180c]">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>

                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-emerald-700 font-bold">
                            <span>Diskon Member VIP</span>
                            <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($order->coupon_discount_amount > 0)
                        <div class="flex justify-between text-purple-700 font-bold">
                            <span>Diskon Kupon Promo ({{ $order->coupon_code }})</span>
                            <span>-Rp {{ number_format($order->coupon_discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($order->points_discount_amount > 0)
                        <div class="flex justify-between text-amber-800 font-bold">
                            <span>Tukar Cookie Crumbs ({{ number_format($order->points_redeemed) }} poin)</span>
                            <span>-Rp {{ number_format($order->points_discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-sm font-extrabold text-[#2d180c] pt-2 border-t border-[#ede7e2]">
                        <span>Total Pembayaran</span>
                        <span class="text-[#b87d4b] text-base">{{ $order->formattedTotal() }}</span>
                    </div>

                    @if($order->points_earned > 0)
                        <div class="mt-3 p-3 rounded-2xl bg-[#faf7f4] border border-[#ede7e2] flex items-center justify-between text-xs text-[#6d4630]">
                            <span class="flex items-center gap-1.5">
                                <span>🍪</span>
                                <span class="font-medium">Loyalty Cookie Crumbs:</span>
                            </span>
                            <span class="font-bold text-emerald-700">+{{ number_format($order->points_earned) }} Crumbs Diberikan</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Custom Gift Card Section (If enabled) -->
            @if($order->is_gift)
                <div class="bg-gradient-to-br from-[#faf7f4] to-white rounded-3xl p-6 sm:p-8 border border-[#b87d4b]/30 shadow-xs">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-2xl">💌</span>
                        <div>
                            <h3 class="text-base font-extrabold text-[#2d180c]">Kartu Ucapan Kado Spesial</h3>
                            <p class="text-xs text-[#6d4630]">Pelanggan memesan kado dengan pesan personal</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-white p-5 rounded-2xl border border-[#ede7e2] mb-4">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block">Penerima Kado (Untuk):</span>
                            <p class="text-sm font-bold text-[#2d180c]">{{ $order->gift_recipient_name }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block">Pengirim Kado (Dari):</span>
                            <p class="text-sm font-bold text-[#2d180c]">{{ $order->gift_sender_name }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-[#ede7e2]">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block mb-1">Isi Pesan Kartu:</span>
                        <blockquote class="italic text-xs sm:text-sm text-[#2d180c] leading-relaxed">
                            "{{ $order->gift_message }}"
                        </blockquote>
                    </div>
                </div>
            @endif

            <!-- Customer Notes if any -->
            @if($order->notes)
                <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#6d4630] mb-2">Catatan dari Pelanggan:</h4>
                    <p class="text-xs text-[#2d180c] bg-[#faf7f4] p-4 rounded-xl border border-[#ede7e2]">
                        {{ $order->notes }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Right: Customer Info & Payment (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Customer Information Card -->
            <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs space-y-4">
                <h3 class="text-base font-extrabold text-[#2d180c] border-b border-[#ede7e2] pb-3">Informasi Pelanggan</h3>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block">Nama Lengkap</span>
                    <p class="text-sm font-bold text-[#2d180c]">{{ $order->customer_name }}</p>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block">Nomor WhatsApp</span>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" 
                       class="text-xs font-bold text-[#b87d4b] hover:underline flex items-center gap-1 mt-0.5">
                        <span>💬 {{ $order->customer_phone }}</span>
                    </a>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block">Email</span>
                    <p class="text-xs text-[#2d180c]">{{ $order->customer_email }}</p>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block">Kota & Alamat Lengkap</span>
                    <p class="text-xs font-bold text-[#2d180c]">{{ $order->customer_city }}</p>
                    <p class="text-xs text-[#6d4630] mt-1 leading-relaxed">{{ $order->customer_address }}</p>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block">Metode Pengiriman</span>
                    <span class="inline-block mt-1 px-3 py-1 bg-[#f6efe8] text-[#2d180c] text-xs font-bold rounded-lg border border-[#ede7e2]">
                        {{ strtoupper($order->shipping_method) }}
                    </span>
                </div>
            </div>

            <!-- Payment Gateway Card -->
            <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs space-y-4">
                <h3 class="text-base font-extrabold text-[#2d180c] border-b border-[#ede7e2] pb-3">Status Pembayaran</h3>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block">Payment Gateway</span>
                    <p class="text-xs font-bold text-[#2d180c]">Xendit Payments (QRIS / VA)</p>
                </div>

                @if($order->xendit_invoice_id)
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block">Xendit Invoice ID</span>
                        <code class="text-[11px] font-mono text-[#6d4630] break-all block">{{ $order->xendit_invoice_id }}</code>
                    </div>
                @endif

                @if($order->xendit_invoice_url)
                    <a href="{{ $order->xendit_invoice_url }}" target="_blank" 
                       class="w-full flex items-center justify-center gap-1.5 bg-[#f6efe8] hover:bg-[#ede7e2] text-[#2d180c] text-xs font-bold py-2.5 px-4 rounded-xl border border-[#ede7e2] transition">
                        <span>Buka Invoice Xendit</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                @endif

                <!-- Testing Simulation Button -->
                @if($order->status === 'pending')
                    <div class="pt-2 border-t border-[#ede7e2]">
                        <p class="text-[10px] text-[#6d4630] mb-2">Simulasi Pengujian Pembayaran Xendit:</p>
                        <form method="POST" action="{{ route('orders.simulate-pay', $order->order_number) }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold py-2.5 px-4 rounded-xl transition shadow-xs cursor-pointer">
                                ⚡ Simulasi Bayar Lunas (Testing)
                            </button>
                        </form>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
