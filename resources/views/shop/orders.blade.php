@extends('layouts.app')

@section('title', 'Lacak & Riwayat Pesanan - NgemilKukis')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8"
     x-data="{
        localOrders: JSON.parse(localStorage.getItem('ngemilkukis_my_orders') || '[]'),
        init() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasSearch = urlParams.has('search');
            const hasOrders = urlParams.has('orders');

            // If user arrives without search/orders query and has locally saved orders, auto-load them seamlessly
            if (!hasSearch && !hasOrders && this.localOrders.length > 0) {
                const targetUrl = '{{ route('customer.orders') }}?orders=' + encodeURIComponent(this.localOrders.join(','));
                window.location.replace(targetUrl);
            }
        },
        clearDeviceHistory() {
            if (confirm('Hapus riwayat pesanan yang tersimpan di perangkat ini?')) {
                localStorage.removeItem('ngemilkukis_my_orders');
                this.localOrders = [];
                window.location.href = '{{ route('customer.orders') }}';
            }
        },
        reorderItems(items) {
            try {
                items.forEach(item => {
                    $store.cart.addItem({
                        id: item.product_id,
                        name: item.product_name,
                        price: item.price,
                        image: item.product?.image || 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=400&q=80',
                        type: item.product?.type || 'ready_stock'
                    }, item.quantity);
                });
                $store.cart.isOpen = true;
            } catch (err) {
                console.error(err);
            }
        }
     }">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs font-semibold text-[#6d4630]">
        <a href="{{ route('home') }}" class="hover:text-[#b87d4b] transition">Beranda</a>
        <span>&rsaquo;</span>
        <span class="text-[#2d180c]">Lacak & Riwayat Pesanan</span>
    </div>

    <!-- Page Header (Formula 60% White + 30% Dark Brown + 10% Caramel) -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1.5 max-w-xl">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-[#f6efe8] border border-[#ede7e2] text-[#b87d4b] text-xs font-bold">
                    <span>Live Tracking & Status Pesanan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#2d180c] tracking-tight">
                    Pesanan & Pengiriman Saya
                </h1>
                <p class="text-xs sm:text-sm text-[#6d4630] leading-relaxed">
                    Pantau proses kukis favoritmu mulai dari panggangan oven fresh-bake, pengantaran kurir di jalan, hingga tiba di tujuan.
                </p>
            </div>

            <!-- Universal Search Form -->
            <form action="{{ route('customer.orders') }}" method="GET" class="w-full md:w-auto md:min-w-[360px] space-y-2">
                <div class="relative flex items-center">
                    <input type="text" 
                           name="search" 
                           value="{{ $searchQuery }}"
                           placeholder="Nomor Pesanan (NK-...) / No. WhatsApp" 
                           class="w-full pl-4 pr-24 py-3 rounded-2xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 text-xs sm:text-sm font-medium outline-none bg-[#faf8f6] transition">
                    <button type="submit" 
                            class="absolute right-1.5 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                        Cari
                    </button>
                </div>
                @if(!empty($searchQuery))
                    <div class="flex items-center justify-between text-[11px] text-[#6d4630] px-1">
                        <span>Mencari: <strong class="text-[#2d180c]">{{ $searchQuery }}</strong></span>
                        <a href="{{ route('customer.orders') }}" class="text-rose-600 font-bold hover:underline">Hapus Filter &times;</a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Filter Status Tabs (Di Jalan, Sudah Sampai, Dibatalkan, Diproses) -->
        <div class="pt-4 border-t border-[#ede7e2] flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
            @php
                $tabParams = function($statusKey) use ($searchQuery, $orderNumbersString) {
                    $params = ['status' => $statusKey];
                    if (!empty($searchQuery)) $params['search'] = $searchQuery;
                    if (!empty($orderNumbersString)) $params['orders'] = $orderNumbersString;
                    return route('customer.orders', $params);
                };
            @endphp

            <!-- Tab: Semua -->
            <a href="{{ $tabParams('all') }}" 
               class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap flex items-center gap-2 {{ $activeStatus === 'all' ? 'bg-[#2d180c] text-white shadow-xs' : 'bg-[#faf8f6] text-[#6d4630] hover:bg-[#ede7e2] border border-[#ede7e2]' }}">
                <span>Semua</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeStatus === 'all' ? 'bg-white/20 text-white' : 'bg-stone-200 text-stone-700' }}">
                    {{ $statusCounts['all'] }}
                </span>
            </a>

            <!-- Tab: Di Jalan (shipped) -->
            <a href="{{ $tabParams('shipped') }}" 
               class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap flex items-center gap-2 {{ $activeStatus === 'shipped' ? 'bg-amber-600 text-white shadow-xs' : 'bg-[#faf8f6] text-amber-900 hover:bg-amber-100/50 border border-amber-200' }}">
                <span>Di Jalan</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeStatus === 'shipped' ? 'bg-white/20 text-white' : 'bg-amber-200 text-amber-900' }}">
                    {{ $statusCounts['shipped'] }}
                </span>
            </a>

            <!-- Tab: Sudah Sampai (delivered) -->
            <a href="{{ $tabParams('delivered') }}" 
               class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap flex items-center gap-2 {{ $activeStatus === 'delivered' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-[#faf8f6] text-emerald-900 hover:bg-emerald-100/50 border border-emerald-200' }}">
                <span>Sudah Sampai</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeStatus === 'delivered' ? 'bg-white/20 text-white' : 'bg-emerald-200 text-emerald-900' }}">
                    {{ $statusCounts['delivered'] }}
                </span>
            </a>

            <!-- Tab: Sedang Diproses (baking / paid) -->
            <a href="{{ $tabParams('processing') }}" 
               class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap flex items-center gap-2 {{ $activeStatus === 'processing' ? 'bg-[#b87d4b] text-white shadow-xs' : 'bg-[#faf8f6] text-[#6d4630] hover:bg-[#ede7e2] border border-[#ede7e2]' }}">
                <span>Sedang Diproses</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeStatus === 'processing' ? 'bg-white/20 text-white' : 'bg-stone-200 text-stone-700' }}">
                    {{ $statusCounts['processing'] }}
                </span>
            </a>

            <!-- Tab: Dibatalkan (cancelled) -->
            <a href="{{ $tabParams('cancelled') }}" 
               class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap flex items-center gap-2 {{ $activeStatus === 'cancelled' ? 'bg-rose-600 text-white shadow-xs' : 'bg-[#faf8f6] text-rose-800 hover:bg-rose-100/50 border border-rose-200' }}">
                <span>Dibatalkan</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeStatus === 'cancelled' ? 'bg-white/20 text-white' : 'bg-rose-200 text-rose-900' }}">
                    {{ $statusCounts['cancelled'] }}
                </span>
            </a>

            <!-- Tab: Menunggu Bayar (pending) -->
            @if($statusCounts['pending'] > 0)
                <a href="{{ $tabParams('pending') }}" 
                   class="px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap flex items-center gap-2 {{ $activeStatus === 'pending' ? 'bg-stone-700 text-white shadow-xs' : 'bg-[#faf8f6] text-[#6d4630] hover:bg-[#ede7e2] border border-[#ede7e2]' }}">
                    <span>Menunggu Bayar</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeStatus === 'pending' ? 'bg-white/20 text-white' : 'bg-stone-200 text-stone-700' }}">
                        {{ $statusCounts['pending'] }}
                    </span>
                </a>
            @endif
        </div>
    </div>

    <!-- Active Device Sync Indicator -->
    @if($hasFilter && !empty($orderNumbersString) && empty($searchQuery))
        <div class="flex items-center justify-between px-4 py-2.5 rounded-2xl bg-[#faf8f6] border border-[#ede7e2] text-xs text-[#6d4630]">
            <span>Menampilkan riwayat pesanan yang dibuat dari perangkat/browser ini.</span>
            <button @click="clearDeviceHistory()" type="button" class="text-stone-400 hover:text-rose-600 transition font-semibold cursor-pointer">
                Hapus Riwayat Perangkat &times;
            </button>
        </div>
    @endif

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs space-y-6 transition hover:border-[#b87d4b]/40">
                    
                    <!-- Card Top Bar: Order Info & Status Badge -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-[#ede7e2]">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <span class="text-xs font-bold uppercase tracking-wider text-[#6d4630]">Pesanan</span>
                                <h3 class="text-lg font-extrabold text-[#2d180c] tracking-tight">
                                    {{ $order->order_number }}
                                </h3>
                                @if($order->is_gift)
                                    <span class="bg-pink-50 text-pink-700 border border-pink-200 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        Hampers Kado
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-[#6d4630]">
                                Dipesan pada {{ $order->created_at->format('d M Y, H:i') }} WIB &bull; Kurir: <strong>{{ $order->shippingMethodLabel() }}</strong>
                            </p>
                        </div>

                        <!-- Status Badge (No Emojis) -->
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-extrabold border {{ $order->statusBadgeClass() }}">
                                {{ $order->statusLabel() }}
                            </span>
                        </div>
                    </div>

                    <!-- Visual Step Progress Tracker (Clean Numbered Stepper) -->
                    @if($order->isCancelled())
                        <!-- Cancelled Banner -->
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                            <strong class="font-bold block text-rose-900 mb-0.5">Pesanan Dibatalkan</strong>
                            <span>{{ $order->statusDescription() }} Jika ada kendala pembayaran, silakan hubungi Customer Service.</span>
                        </div>
                    @else
                        <!-- Active 5-Step Stepper -->
                        @php
                            $currentStep = $order->statusStepIndex();
                            $steps = [
                                1 => 'Pesanan Dibuat',
                                2 => 'Terbayar',
                                3 => 'Dipanggang & Dipacking',
                                4 => 'Di Jalan (Kurir)',
                                5 => 'Sudah Sampai',
                            ];
                        @endphp
                        <div class="py-2">
                            <div class="grid grid-cols-5 gap-2 relative">
                                @foreach($steps as $stepNum => $stepLabel)
                                    @php
                                        $isCompleted = $stepNum <= $currentStep;
                                        $isCurrent = $stepNum === $currentStep;
                                    @endphp
                                    <div class="flex flex-col items-center text-center space-y-1.5 group">
                                        <!-- Step Circle -->
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-2xl flex items-center justify-center text-xs sm:text-sm font-extrabold transition {{ $isCurrent ? 'bg-[#b87d4b] text-white ring-4 ring-[#b87d4b]/20 shadow-sm' : ($isCompleted ? 'bg-emerald-600 text-white' : 'bg-[#faf8f6] text-stone-400 border border-[#ede7e2]') }}">
                                            @if($isCompleted && !$isCurrent)
                                                ✓
                                            @else
                                                <span>{{ $stepNum }}</span>
                                            @endif
                                        </div>
                                        <!-- Step Label -->
                                        <span class="text-[10px] sm:text-xs font-bold leading-tight {{ $isCurrent ? 'text-[#b87d4b]' : ($isCompleted ? 'text-[#2d180c]' : 'text-stone-400') }}">
                                            {{ $stepLabel }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Detailed Live Status Explanation -->
                            <div class="mt-4 p-3.5 rounded-2xl bg-[#faf8f6] border border-[#ede7e2] text-xs text-[#2d180c] flex items-center justify-between gap-3">
                                <span><strong>Info Status:</strong> {{ $order->statusDescription() }}</span>
                                @if($order->isShipped())
                                    <span class="text-[11px] font-bold text-amber-800 bg-amber-100/70 px-2.5 py-1 rounded-full shrink-0">
                                        Dalam Perjalanan
                                    </span>
                                @elseif($order->isDelivered())
                                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100/70 px-2.5 py-1 rounded-full shrink-0">
                                        Terkirim
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Purchased Cookie Items -->
                    <div class="space-y-3 pt-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#6d4630]">Rincian Kukis:</span>
                        <div class="divide-y divide-[#ede7e2] border border-[#ede7e2] rounded-2xl p-4 bg-[#faf8f6]/50">
                            @foreach($order->items as $item)
                                <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $item->product->image ?? 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=400&q=80' }}" 
                                             alt="{{ $item->product_name }}" 
                                             class="w-11 h-11 rounded-xl object-cover border border-[#ede7e2] shrink-0">
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-[#2d180c] truncate">{{ $item->product_name }}</h4>
                                            <p class="text-[#6d4630] text-[11px]">
                                                {{ $item->quantity }} pcs &times; Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="font-extrabold text-[#2d180c] shrink-0">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Destination & Total -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 text-xs">
                        <div class="p-4 rounded-2xl bg-[#faf8f6] border border-[#ede7e2] space-y-1">
                            <span class="font-bold text-[#6d4630] block text-[11px] uppercase tracking-wider">Tujuan Pengiriman:</span>
                            <p class="font-bold text-[#2d180c]">{{ $order->customer_name }} ({{ $order->customer_phone }})</p>
                            <p class="text-[#6d4630] line-clamp-2">{{ $order->customer_address }}, {{ $order->customer_city ?? 'Jakarta' }}</p>
                            @if($order->is_gift && !empty($order->gift_recipient_name))
                                <p class="text-[11px] text-[#b87d4b] font-semibold pt-1">
                                    Penerima Kado: {{ $order->gift_recipient_name }}
                                </p>
                            @endif
                        </div>

                        <div class="p-4 rounded-2xl bg-[#faf8f6] border border-[#ede7e2] flex flex-col justify-between space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[#6d4630]">Total Pembayaran:</span>
                                <strong class="text-base font-extrabold text-[#b87d4b]">{{ $order->formattedTotal() }}</strong>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-[#6d4630]">
                                <span>Metode: {{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</span>
                                <span>Ongkir: Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-[#ede7e2]">
                        <div class="flex items-center gap-2">
                            <!-- WhatsApp Driver / CS Assistance -->
                            @php
                                $waMessage = urlencode("Halo Admin NgemilKukis, saya ingin menanyakan status pesanan #{$order->order_number} atas nama {$order->customer_name}.");
                            @endphp
                            <a href="https://wa.me/6281234567890?text={{ $waMessage }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-[#ede7e2] hover:border-[#b87d4b] text-[#2d180c] hover:text-[#b87d4b] text-xs font-bold transition">
                                Tanya Kurir / CS
                            </a>

                            <!-- View Receipt / Invoice -->
                            <a href="{{ route('orders.show', $order->order_number) }}" 
                               class="inline-flex items-center gap-1 px-4 py-2 rounded-xl border border-[#ede7e2] hover:border-[#b87d4b] text-[#2d180c] hover:text-[#b87d4b] text-xs font-bold transition">
                                Rincian & Nota
                            </a>
                        </div>

                        <!-- Main Contextual Action Button (Tombol Coklat Muda 10%) -->
                        <div>
                            @if($order->isPending() && !empty($order->xendit_invoice_url))
                                <a href="{{ $order->xendit_invoice_url }}" 
                                   class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-6 py-2.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
                                    <span>Bayar Sekarang via Xendit</span>
                                    <span>&rarr;</span>
                                </a>
                            @elseif($order->isShipped())
                                <a href="https://wa.me/6281234567890?text={{ $waMessage }}" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-6 py-2.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
                                    <span>Cek Posisi Kurir</span>
                                    <span>&rarr;</span>
                                </a>
                            @elseif($order->isDelivered())
                                <button @click="reorderItems(@js($order->items))" 
                                        type="button" 
                                        class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-6 py-2.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
                                    <span>Pesan Lagi Kukis Ini</span>
                                    <span>+</span>
                                </button>
                            @else
                                <a href="{{ route('orders.show', $order->order_number) }}" 
                                   class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-6 py-2.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
                                    <span>Pantau Progres</span>
                                    <span>&rarr;</span>
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach

            <!-- Pagination -->
            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <!-- Empty State (Clean Minimalist Monogram) -->
        <div class="bg-white rounded-3xl p-12 sm:p-16 border border-[#ede7e2] shadow-xs text-center space-y-5 max-w-lg mx-auto">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-[#f6efe8] flex items-center justify-center text-[#b87d4b] font-extrabold text-sm tracking-wider uppercase">
                NK
            </div>
            <div class="space-y-1.5">
                <h3 class="text-xl font-extrabold text-[#2d180c]">
                    @if(!empty($searchQuery))
                        Pesanan Tidak Ditemukan
                    @elseif($activeStatus !== 'all')
                        Tidak Ada Pesanan dengan Status Ini
                    @else
                        Belum Ada Pesanan yang Dilacak
                    @endif
                </h3>
                <p class="text-xs sm:text-sm text-[#6d4630] leading-relaxed">
                    @if(!empty($searchQuery))
                        Tidak ditemukan pesanan dengan kata kunci "<strong class="text-[#2d180c]">{{ $searchQuery }}</strong>". Pastikan nomor pesanan atau nomor WhatsApp sudah sesuai.
                    @else
                        Masukkan Nomor Pesanan (contoh: <span class="font-mono text-[#2d180c] font-bold">NK-20261023-XXXXX</span>) atau Nomor WhatsApp pada kolom pencarian di atas.
                    @endif
                </p>
            </div>

            <div class="pt-3 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}#katalog" 
                   class="w-full sm:w-auto bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold text-xs px-6 py-3 rounded-full shadow-sm transition">
                    Lihat Katalog Kukis &rarr;
                </a>
                @if(!empty($searchQuery) || $activeStatus !== 'all')
                    <a href="{{ route('customer.orders') }}" 
                       class="w-full sm:w-auto border border-[#ede7e2] hover:border-[#b87d4b] text-[#2d180c] font-bold text-xs px-6 py-3 rounded-full transition">
                        Reset Pencarian
                    </a>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
