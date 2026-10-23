@extends('layouts.admin')

@section('title', 'Daftar Pesanan')
@section('page_title', 'Kelola Pesanan Masuk')

@section('content')
<div class="space-y-6">

    <!-- Top Toolbar & Status Filter Tabs (Putih 60%) -->
    <div class="bg-white rounded-3xl p-6 border border-[#ede7e2] shadow-xs space-y-4">
        
        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari no pesanan, nama, no HP..." 
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-[#ede7e2] text-xs text-[#2d180c] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 outline-none">
                <svg class="w-4 h-4 text-[#6d4630] absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="flex items-center gap-2">
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.orders.index') }}" class="text-xs text-[#b87d4b] hover:underline font-semibold">
                        Reset Filter
                    </a>
                @endif
                <button type="submit" class="bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-4 py-2.5 rounded-xl transition">
                    Cari Pesanan
                </button>
            </div>
        </form>

        <!-- Status Filter Pills -->
        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-[#ede7e2] text-xs font-bold">
            <a href="{{ route('admin.orders.index', array_merge(request()->except('status', 'page'))) }}" 
               class="px-3.5 py-2 rounded-xl transition {{ !request('status') ? 'bg-[#2d180c] text-white shadow-xs' : 'bg-[#f6efe8] text-[#6d4630] hover:text-[#2d180c]' }}">
                Semua Status
            </a>

            @php
                $statuses = [
                    'pending' => ['label' => 'Menunggu Bayar', 'icon' => '⏳'],
                    'paid' => ['label' => 'Sudah Dibayar', 'icon' => '✅'],
                    'baking' => ['label' => 'Sedang Dipanggang', 'icon' => '🔥'],
                    'shipped' => ['label' => 'Dalam Pengiriman', 'icon' => '🛵'],
                    'delivered' => ['label' => 'Selesai / Terkirim', 'icon' => '🎉'],
                    'cancelled' => ['label' => 'Dibatalkan', 'icon' => '❌'],
                ];
            @endphp

            @foreach($statuses as $key => $meta)
                <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => $key])) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request('status') === $key ? 'bg-[#b87d4b] text-white shadow-xs' : 'bg-[#f6efe8] text-[#6d4630] hover:text-[#2d180c]' }}">
                    <span>{{ $meta['icon'] }}</span>
                    <span>{{ $meta['label'] }}</span>
                    @if(isset($statusCounts[$key]))
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ request('status') === $key ? 'bg-white/20 text-white' : 'bg-white text-[#2d180c]' }}">
                            {{ $statusCounts[$key] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-[#ede7e2] shadow-xs overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-12 text-center text-[#6d4630]">
                <span class="text-4xl block mb-2">📦</span>
                <p class="text-sm font-bold text-[#2d180c]">Tidak ada pesanan yang sesuai dengan kriteria</p>
                <p class="text-xs mt-1">Coba sesuaikan filter status atau kata kunci pencarian Anda.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-[#fcfaf8] text-[#6d4630] font-bold uppercase text-[10px] tracking-wider border-b border-[#ede7e2]">
                        <tr>
                            <th class="py-3.5 px-6">No. Pesanan</th>
                            <th class="py-3.5 px-6">Pelanggan</th>
                            <th class="py-3.5 px-6">Tanggal</th>
                            <th class="py-3.5 px-6">Items & Kado</th>
                            <th class="py-3.5 px-6">Total Tagihan</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ede7e2] text-[#2d180c]">
                        @foreach($orders as $order)
                            <tr class="hover:bg-[#faf7f4] transition">
                                <td class="py-4 px-6 font-mono font-bold text-[#b87d4b]">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-bold text-[#2d180c]">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-[#6d4630]">{{ $order->customer_phone }}</p>
                                    <p class="text-[11px] text-[#6d4630] truncate max-w-xs">{{ $order->customer_city }}</p>
                                </td>
                                <td class="py-4 px-6 text-xs text-[#6d4630]">
                                    {{ $order->created_at->format('d M Y, H:i') }} WIB
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="bg-[#f6efe8] px-2 py-0.5 rounded text-xs font-semibold text-[#2d180c]">
                                            {{ $order->items->sum('quantity') }} pcs
                                        </span>
                                        @if($order->is_gift)
                                            <span class="bg-amber-100 text-amber-900 px-2 py-0.5 rounded text-[11px] font-bold" title="Custom Gift Card: {{ $order->gift_recipient_name }}">
                                                💌 Kado
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-extrabold text-[#2d180c]">
                                    {{ $order->formattedTotal() }}
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
                                       class="inline-flex items-center gap-1.5 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-3.5 py-1.5 rounded-full shadow-xs transition">
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

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="p-6 border-t border-[#ede7e2]">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
