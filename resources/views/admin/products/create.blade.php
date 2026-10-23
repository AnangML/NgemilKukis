@extends('layouts.admin')

@section('title', 'Tambah Varian Produk Baru')
@section('page_title', 'Tambah Varian Produk Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#6d4630] hover:text-[#2d180c]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Katalog</span>
        </a>
    </div>

    <!-- Form Card (Putih Bersih 60%) -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-[#ede7e2] shadow-xs" x-data="{ productType: '{{ old('type', 'ready_stock') }}' }">
        <div class="border-b border-[#ede7e2] pb-6 mb-8">
            <h2 class="text-xl font-extrabold text-[#2d180c]">Informasi Varian Kukis</h2>
            <p class="text-xs text-[#6d4630] mt-1">Lengkapi data produk kukis artisan untuk dipajang di etalase toko.</p>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-6">
            @csrf

            <!-- Product Name & Tagline -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Nama Produk Kukis <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}" 
                           placeholder="Contoh: Signature Dark Choco Chunk" 
                           required 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 outline-none text-sm text-[#2d180c]">
                    @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tagline" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Tagline / Kalimat Pemikat Singkat
                    </label>
                    <input type="text" 
                           name="tagline" 
                           id="tagline" 
                           value="{{ old('tagline') }}" 
                           placeholder="Contoh: 70% Dark Belgian Chocolate & Sea Salt" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 outline-none text-sm text-[#2d180c]">
                    @error('tagline') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Product Type (Ready Stock vs Pre-Order) -->
            <div class="bg-[#faf7f4] p-5 rounded-2xl border border-[#ede7e2]">
                <label class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-3">
                    Model Penjualan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="relative flex items-center gap-3 p-4 bg-white rounded-xl border cursor-pointer transition"
                           :class="productType === 'ready_stock' ? 'border-[#b87d4b] ring-2 ring-[#b87d4b]/20' : 'border-[#ede7e2]'">
                        <input type="radio" name="type" value="ready_stock" x-model="productType" class="text-[#b87d4b] focus:ring-[#b87d4b]">
                        <div>
                            <span class="block text-sm font-bold text-[#2d180c]">⚡ Ready Stock</span>
                            <span class="block text-xs text-[#6d4630]">Bisa langsung dikirim via Sameday / Instant hari ini</span>
                        </div>
                    </label>

                    <label class="relative flex items-center gap-3 p-4 bg-white rounded-xl border cursor-pointer transition"
                           :class="productType === 'pre_order' ? 'border-[#b87d4b] ring-2 ring-[#b87d4b]/20' : 'border-[#ede7e2]'">
                        <input type="radio" name="type" value="pre_order" x-model="productType" class="text-[#b87d4b] focus:ring-[#b87d4b]">
                        <div>
                            <span class="block text-sm font-bold text-[#2d180c]">📅 Pre-Order (PO)</span>
                            <span class="block text-xs text-[#6d4630]">Dipanggang sesuai jadwal batch baking terjadwal</span>
                        </div>
                    </label>
                </div>

                <!-- Conditional Baking Schedule Input -->
                <div x-show="productType === 'pre_order'" class="mt-4 pt-4 border-t border-[#ede7e2]">
                    <label for="baking_schedule" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Jadwal Baking / Pengiriman Spesial
                    </label>
                    <input type="text" 
                           name="baking_schedule" 
                           id="baking_schedule" 
                           value="{{ old('baking_schedule', 'Baking Batch: Setiap Jumat & Selasa') }}" 
                           placeholder="Contoh: Baking Setiap Jumat & Selasa (Pukul 08:00 WIB)" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c] bg-white">
                </div>
            </div>

            <!-- Price & Sale Price -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="price" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Harga Normal (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           name="price" 
                           id="price" 
                           value="{{ old('price', 28000) }}" 
                           min="0" 
                           step="500" 
                           required 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                    @error('price') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sale_price" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Harga Coret / Promo (Rp, Opsional)
                    </label>
                    <input type="number" 
                           name="sale_price" 
                           id="sale_price" 
                           value="{{ old('sale_price') }}" 
                           min="0" 
                           step="500" 
                           placeholder="Kosongkan jika tidak sedang diskon" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                    @error('sale_price') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Stock & Weight -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="stock" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Jumlah Stok (Pcs) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           name="stock" 
                           id="stock" 
                           value="{{ old('stock', 20) }}" 
                           min="0" 
                           required 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                    @error('stock') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="weight_grams" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Berat Bersih (Gram)
                    </label>
                    <input type="number" 
                           name="weight_grams" 
                           id="weight_grams" 
                           value="{{ old('weight_grams', 85) }}" 
                           min="1" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                    @error('weight_grams') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="badge" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Label Badge (Opsional)
                    </label>
                    <input type="text" 
                           name="badge" 
                           id="badge" 
                           value="{{ old('badge') }}" 
                           placeholder="Misal: Best Seller / New" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>
            </div>

            <!-- Ingredients & Image URL -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="ingredients" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        Bahan Utama (Ingredients)
                    </label>
                    <input type="text" 
                           name="ingredients" 
                           id="ingredients" 
                           value="{{ old('ingredients') }}" 
                           placeholder="French Butter, Callebaut 70%, Organic Cane Sugar" 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>

                <div>
                    <label for="image" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                        URL Foto Produk (Unsplash / CDN)
                    </label>
                    <input type="url" 
                           name="image" 
                           id="image" 
                           value="{{ old('image', 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=800&q=80') }}" 
                           placeholder="https://..." 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-[#2d180c] mb-1.5">
                    Deskripsi Lengkap Rasa & Karakter Kukis <span class="text-rose-500">*</span>
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4" 
                          required 
                          placeholder="Jelaskan aroma butter, tekstur crispy pinggir, dan lelehan cokelat di tengah..." 
                          class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-sm text-[#2d180c]">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Toggles: Is Active & Is Featured -->
            <div class="flex flex-wrap gap-6 pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer text-sm font-semibold text-[#2d180c]">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-[#ede7e2] text-[#b87d4b] focus:ring-[#b87d4b]">
                    <span>Tampilkan di Katalog (Aktif)</span>
                </label>

                <label class="flex items-center gap-2.5 cursor-pointer text-sm font-semibold text-[#2d180c]">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-[#ede7e2] text-[#b87d4b] focus:ring-[#b87d4b]">
                    <span>Tampilkan sebagai Produk Unggulan</span>
                </label>
            </div>

            <!-- Submit Button (10% Coklat Muda) -->
            <div class="pt-6 border-t border-[#ede7e2] flex items-center justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="px-5 py-3 rounded-full text-xs font-bold text-[#6d4630] hover:bg-[#f6efe8] transition">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs sm:text-sm font-bold px-8 py-3.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
                    Simpan Varian Kukis
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
