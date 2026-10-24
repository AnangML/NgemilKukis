@extends('layouts.app')

@section('title', 'Profil & Akun Member - NgemilKukis')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Top Profile Banner (Putih 60% dengan border halus) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-[#b87d4b]">
            @else
                <div class="w-16 h-16 rounded-full bg-[#f6efe8] text-[#b87d4b] font-extrabold text-2xl flex items-center justify-center border-2 border-[#b87d4b]/30">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif

            <div class="space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-extrabold text-[#2d180c]">{{ $user->name }}</h1>
                    <span class="inline-flex items-center gap-1.5 bg-[#f6efe8] border border-[#ede7e2] px-3 py-0.5 rounded-full text-xs font-extrabold text-[#b87d4b]">
                        <span>VIP Member</span>
                    </span>
                </div>
                <p class="text-xs text-[#6d4630] flex items-center gap-2 flex-wrap">
                    <span>{{ $user->email }}</span>
                    @if($user->phone)
                        <span>&bull;</span>
                        <span>{{ $user->phone }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="bg-[#faf7f4] p-4 rounded-2xl border border-[#ede7e2] text-right sm:text-right w-full sm:w-auto">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#6d4630] block">Keuntungan Member Aktif</span>
            <p class="text-lg font-extrabold text-[#b87d4b]">Diskon Rp {{ number_format($user->member_discount_amount ?? 10000, 0, ',', '.') }}</p>
            <span class="text-[11px] text-[#6d4630]">Otomatis terpotong di halaman Checkout</span>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Profil & Alamat Utama (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Edit Profile Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs space-y-5">
                <div class="border-b border-[#ede7e2] pb-3">
                    <h3 class="text-base font-extrabold text-[#2d180c]">Data Diri & Alamat Utama</h3>
                    <p class="text-xs text-[#6d4630] mt-0.5">Data ini otomatis mengisi kolom formulir saat Anda checkout.</p>
                </div>

                <form method="POST" action="{{ route('customer.account.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                            Nama Lengkap
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                            Alamat Email
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                            No. Telepon / WhatsApp
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="081234567890"
                               class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                            Kota Pengiriman
                        </label>
                        <input type="text" name="saved_city" value="{{ old('saved_city', $user->saved_city ?? 'Jakarta') }}" placeholder="Contoh: Jakarta Selatan"
                               class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                            Alamat Lengkap Rumah / Kantor
                        </label>
                        <textarea name="saved_address" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, patokan..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">{{ old('saved_address', $user->saved_address) }}</textarea>
                    </div>

                    <button type="submit" 
                            class="w-full bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold py-3.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
                        Simpan Perubahan Profil
                    </button>
                </form>
            </div>

            <!-- Saved Address Book (Buku Alamat Kado/Kantor) -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs space-y-4" x-data="{ addingAddress: false }">
                <div class="flex items-center justify-between border-b border-[#ede7e2] pb-3">
                    <div>
                        <h3 class="text-base font-extrabold text-[#2d180c]">Buku Alamat Kado</h3>
                        <p class="text-xs text-[#6d4630] mt-0.5">Simpan alamat teman atau pacar untuk kirim hampers.</p>
                    </div>
                    <button type="button" 
                            @click="addingAddress = !addingAddress" 
                            class="text-xs font-bold text-[#b87d4b] hover:underline cursor-pointer">
                        <span x-show="!addingAddress">+ Tambah Alamat</span>
                        <span x-show="addingAddress">Tutup</span>
                    </button>
                </div>

                <!-- Form Tambah Alamat Baru -->
                <form x-show="addingAddress" x-cloak method="POST" action="{{ route('customer.addresses.store') }}" class="p-4 bg-[#faf7f4] rounded-2xl border border-[#ede7e2] space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Label Alamat (Contoh: Kantor, Rumah Pacar)</label>
                        <input type="text" name="label" required placeholder="Contoh: Rumah Sahabat" class="w-full px-3.5 py-2 rounded-xl border border-[#ede7e2] text-xs bg-white">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Nama Penerima</label>
                            <input type="text" name="recipient_name" required placeholder="Nama penerima" class="w-full px-3.5 py-2 rounded-xl border border-[#ede7e2] text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#6d4630] mb-1">No. Telp Penerima</label>
                            <input type="text" name="recipient_phone" required placeholder="08xxx" class="w-full px-3.5 py-2 rounded-xl border border-[#ede7e2] text-xs bg-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Kota</label>
                        <input type="text" name="city" required placeholder="Contoh: Tangerang Selatan" class="w-full px-3.5 py-2 rounded-xl border border-[#ede7e2] text-xs bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#6d4630] mb-1">Alamat Lengkap</label>
                        <textarea name="full_address" rows="2" required placeholder="Alamat lengkap penerima..." class="w-full px-3.5 py-2 rounded-xl border border-[#ede7e2] text-xs bg-white"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-[#b87d4b] text-white font-bold text-xs py-2.5 rounded-full transition">
                        Simpan ke Buku Alamat
                    </button>
                </form>

                <!-- List Alamat Tersimpan -->
                <div class="space-y-3">
                    @forelse($addresses as $addr)
                        <div class="p-4 rounded-2xl border border-[#ede7e2] bg-white flex items-start justify-between gap-3">
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-[#2d180c]">{{ $addr->label }}</span>
                                    <span class="text-[10px] text-[#6d4630]">({{ $addr->recipient_name }})</span>
                                </div>
                                <p class="text-[#6d4630] text-[11px] leading-relaxed">{{ $addr->full_address }}, {{ $addr->city }}</p>
                                <p class="text-[#6d4630] text-[10px]">Telp: {{ $addr->recipient_phone }}</p>
                            </div>
                            <form method="POST" action="{{ route('customer.addresses.destroy', $addr) }}" onsubmit="return confirm('Hapus alamat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-bold p-1">Hapus</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-[#6d4630] text-center py-2">Belum ada alamat kado tersimpan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Riwayat Pesanan Saya (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-[#ede7e2] shadow-xs space-y-6">
            <div class="flex items-center justify-between border-b border-[#ede7e2] pb-4">
                <div>
                    <h3 class="text-lg font-extrabold text-[#2d180c]">Riwayat Pesanan Anda</h3>
                    <p class="text-xs text-[#6d4630] mt-0.5">Daftar semua pesanan kukis yang pernah Anda buat di NgemilKukis.</p>
                </div>
                <a href="{{ route('home') }}#katalog" 
                   class="inline-flex items-center gap-1.5 text-xs font-bold text-[#b87d4b] hover:underline">
                    <span>+ Pesan Lagi</span>
                </a>
            </div>

            <div class="space-y-4">
                @forelse($orders as $order)
                    <div class="p-5 rounded-2xl border border-[#ede7e2] bg-white hover:border-[#b87d4b]/40 transition space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-[#ede7e2]">
                            <div>
                                <span class="font-extrabold text-sm text-[#2d180c]">{{ $order->order_number }}</span>
                                <span class="text-xs text-[#6d4630] block sm:inline sm:ml-2">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                            </div>

                            <!-- Status Badge -->
                            <div>
                                @php
                                    $badge = match($order->status) {
                                        'paid' => ['bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'label' => 'Sudah Dibayar'],
                                        'baking' => ['bg' => 'bg-amber-50 text-amber-900 border-amber-200', 'label' => 'Sedang Dipanggang'],
                                        'shipped' => ['bg' => 'bg-sky-50 text-sky-800 border-sky-200', 'label' => 'Dalam Pengiriman'],
                                        'delivered' => ['bg' => 'bg-emerald-100 text-emerald-900 border-emerald-300', 'label' => 'Pesanan Selesai'],
                                        'cancelled' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Dibatalkan'],
                                        default => ['bg' => 'bg-stone-100 text-stone-600 border-stone-200', 'label' => 'Menunggu Pembayaran']
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badge['bg'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Items Preview -->
                        <div class="space-y-1.5 text-xs">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between text-[#2d180c]">
                                    <span>{{ $item->quantity }}x {{ $item->product_name }}</span>
                                    <span class="font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            @if($order->discount_amount > 0)
                                <div class="flex items-center justify-between text-[#b87d4b] font-bold text-xs pt-1">
                                    <span>Potongan Diskon Member VIP</span>
                                    <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-[#ede7e2] text-xs">
                            <span class="text-[#6d4630]">Total Pembayaran: <strong class="text-[#2d180c] text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></span>
                            <a href="{{ route('orders.show', $order->order_number) }}" 
                               class="inline-flex items-center gap-1 font-bold text-[#b87d4b] hover:text-[#a36b3b] transition">
                                <span>Lihat Rincian</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 space-y-3">
                        <p class="text-sm font-bold text-[#2d180c]">Belum ada pesanan aktif</p>
                        <p class="text-xs text-[#6d4630]">Pilih varian kukis artisan favoritmu dan rasakan sensasi hangat mentega Prancis murni!</p>
                        <a href="{{ route('home') }}#katalog" 
                           class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold px-6 py-3 rounded-full shadow-sm transition">
                            <span>Mulai Belanja Kukis</span>
                        </a>
                    </div>
                @endforelse
            </div>

            @if($orders->hasPages())
                <div class="pt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
