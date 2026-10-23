@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('page_title', 'Ringkasan Dashboard')

@section('content')
<div class="space-y-8">

    <!-- KPI Metric Cards Grid (60% White cards, 30% Dark Brown headings, 10% Light Brown accents) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Revenue -->
        <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#6d4630]">Total Pendapatan</span>
                <div class="w-10 h-10 rounded-xl bg-[#f6efe8] text-[#b87d4b] flex items-center justify-center font-bold text-lg">
                    💰
                </div>
            </div>
            <div>
                <p class="text-2xl lg:text-3xl font-extrabold text-[#2d180c]">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </p>
                <p class="text-xs text-[#6d4630] mt-1">Dari transaksi berbayar & sukses</p>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#6d4630]">Total Pesanan</span>
                <div class="w-10 h-10 rounded-xl bg-[#f6efe8] text-[#b87d4b] flex items-center justify-center font-bold text-lg">
                    📦
                </div>
            </div>
            <div>
                <p class="text-2xl lg:text-3xl font-extrabold text-[#2d180c]">
                    {{ $totalOrders }}
                </p>
                <p class="text-xs text-[#6d4630] mt-1">{{ $pendingOrders }} menunggu pembayaran</p>
            </div>
        </div>

        <!-- Orders to Process (Paid/Baking) -->
        <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#6d4630]">Siap Diproses / Baking</span>
                <div class="w-10 h-10 rounded-xl bg-[#f6efe8] text-[#b87d4b] flex items-center justify-center font-bold text-lg">
                    🔥
                </div>
            </div>
            <div>
                <p class="text-2xl lg:text-3xl font-extrabold text-[#2d180c]">
                    {{ $paidOrders }}
                </p>
                <p class="text-xs text-[#6d4630] mt-1">Perlu dipanggang & dikirim</p>
            </div>
        </div>

        <!-- Products in Catalog -->
        <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#6d4630]">Varian Kukis</span>
                <div class="w-10 h-10 rounded-xl bg-[#f6efe8] text-[#b87d4b] flex items-center justify-center font-bold text-lg">
                    🍪
                </div>
            </div>
            <div>
                <p class="text-2xl lg:text-3xl font-extrabold text-[#2d180c]">
                    {{ $activeProducts }} <span class="text-sm font-normal text-[#6d4630]">/ {{ $totalProducts }}</span>
                </p>
                <p class="text-xs text-[#6d4630] mt-1">
                    @if($lowStockProducts > 0)
                        <span class="text-amber-700 font-bold">⚠️ {{ $lowStockProducts }} varian stok menipis</span>
                    @else
                        Semua stok varian aman
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Action Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="space-y-1 text-center sm:text-left">
            <h3 class="text-lg font-extrabold text-[#2d180c]">Pusat Kendali Cepat NgemilKukis</h3>
            <p class="text-xs sm:text-sm text-[#6d4630]">
                Ubah teks pengumuman, perbarui jadwal pre-order, atau tambah varian menu baru langsung dari panel ini.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.products.create') }}" 
               class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs sm:text-sm font-bold px-5 py-3 rounded-full shadow-sm transition">
                <span>+ Tambah Varian Kukis</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" 
               class="inline-flex items-center gap-2 bg-[#f6efe8] hover:bg-[#ede7e2] text-[#2d180c] text-xs sm:text-sm font-bold px-5 py-3 rounded-full border border-[#ede7e2] transition">
                <span>⚙️ Atur Tampilan Dinamis</span>
            </a>
        </div>
    </div>

    <!-- Recent Orders Table Section -->
    <div class="bg-white rounded-3xl border border-[#ede7e2] shadow-xs overflow-hidden">
        <div class="p-6 sm:px-8 border-b border-[#ede7e2] flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-[#2d180c]">Pesanan Terbaru</h3>
                <p class="text-xs text-[#6d4630] mt-0.5">Daftar transaksi pesanan pelanggan yang masuk</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" 
               class="text-xs font-bold text-[#b87d4b] hover:underline flex items-center gap-1">
                <span>Lihat Semua Pesanan</span>
                <span>&rarr;</span>
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="p-12 text-center text-[#6d4630]">
                <span class="text-4xl block mb-2">🍪</span>
                <p class="text-sm font-bold text-[#2d180c]">Belum ada pesanan yang masuk</p>
                <p class="text-xs mt-1">Pesanan pelanggan dari storefront akan muncul otomatis di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-[#fcfaf8] text-[#6d4630] font-bold uppercase text-[10px] tracking-wider border-b border-[#ede7e2]">
                        <tr>
                            <th class="py-3.5 px-6">No. Pesanan</th>
                            <th class="py-3.5 px-6">Pelanggan</th>
                            <th class="py-3.5 px-6">Total Tagihan</th>
                            <th class="py-3.5 px-6">Metode & Kado</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ede7e2] text-[#2d180c]">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-[#faf7f4] transition">
                                <td class="py-4 px-6 font-mono font-bold text-[#b87d4b]">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-bold text-[#2d180c]">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-[#6d4630]">{{ $order->customer_phone }}</p>
                                </td>
                                <td class="py-4 px-6 font-bold">
                                    {{ $order->formattedTotal() }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-[#f6efe8] px-2 py-0.5 rounded text-[11px] font-medium text-[#6d4630]">
                                            {{ strtoupper($order->shipping_method) }}
                                        </span>
                                        @if($order->is_gift)
                                            <span class="bg-amber-100 text-amber-900 px-2 py-0.5 rounded text-[11px] font-bold" title="Disertai Kartu Ucapan Kado">
                                                💌 Kado
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6">
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
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $style }}">
                                        {{ $order->statusLabel() }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="inline-flex items-center gap-1.5 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-3.5 py-1.5 rounded-full transition">
                                        <span>Rincian</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
