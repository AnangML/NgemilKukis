<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - NgemilKukis Control Center</title>

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
<body class="bg-[#faf7f4] text-[#2d180c] antialiased selection:bg-[#b87d4b] selection:text-white min-h-screen flex" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-cloak x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-[#2d180c]/50 backdrop-blur-xs lg:hidden transition-opacity">
    </div>

    <!-- Sidebar Navigation (30% Dark Brown & White Card) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-[#ede7e2] transition-transform duration-300 ease-in-out flex flex-col justify-between">
        
        <div>
            <!-- Sidebar Header / Brand Logo -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-[#ede7e2]">
                <a href="{{ route('admin.dashboard') }}" class="group">
                    <span class="font-extrabold text-xl tracking-tight text-[#2d180c] block leading-none">
                        Ngemil<span class="text-[#b87d4b]">Kukis.</span>
                    </span>
                    <span class="text-[10px] font-bold tracking-wider text-[#6d4630] uppercase mt-1 block">Admin Control</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-[#6d4630] hover:text-[#2d180c]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5 text-sm font-semibold">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#b87d4b] text-white font-bold shadow-sm' : 'text-[#6d4630] hover:bg-[#f6efe8] hover:text-[#2d180c]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition {{ request()->routeIs('admin.products.*') ? 'bg-[#b87d4b] text-white font-bold shadow-sm' : 'text-[#6d4630] hover:bg-[#f6efe8] hover:text-[#2d180c]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Katalog Produk</span>
                </a>

                <a href="{{ route('admin.greeting-cards.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition {{ request()->routeIs('admin.greeting-cards.*') ? 'bg-[#b87d4b] text-white font-bold shadow-sm' : 'text-[#6d4630] hover:bg-[#f6efe8] hover:text-[#2d180c]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Kartu Ucapan</span>
                </a>

                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition {{ request()->routeIs('admin.orders.*') ? 'bg-[#b87d4b] text-white font-bold shadow-sm' : 'text-[#6d4630] hover:bg-[#f6efe8] hover:text-[#2d180c]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Pesanan Masuk</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'bg-[#b87d4b] text-white font-bold shadow-sm' : 'text-[#6d4630] hover:bg-[#f6efe8] hover:text-[#2d180c]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Pengaturan Tampilan</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer: Toko Link & Logout -->
        <div class="p-4 border-t border-[#ede7e2] space-y-3">
            <a href="{{ route('home') }}" target="_blank" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-[#f6efe8] hover:bg-[#ede7e2] text-xs font-bold text-[#2d180c] transition">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#b87d4b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Buka Toko Online</span>
                </span>
                <span class="text-[#b87d4b]">&rarr;</span>
            </a>

            <div class="pt-2 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-[#2d180c] text-white flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-bold text-[#2d180c] truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-[10px] text-[#6d4630] truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" 
                            title="Logout" 
                            class="p-2 text-[#6d4630] hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area (Putih 60% Dominan) -->
    <div class="flex-1 lg:pl-64 flex flex-col min-h-screen">
        
        <!-- Top Navbar -->
        <header class="h-20 bg-white border-b border-[#ede7e2] flex items-center justify-between px-6 lg:px-10 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-[#2d180c] p-2 hover:bg-[#f6efe8] rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-xl font-extrabold text-[#2d180c] tracking-tight">
                    @yield('page_title', 'Dashboard')
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#f6efe8] text-[#b87d4b] border border-[#ede7e2]">
                    <span class="w-2 h-2 rounded-full bg-[#b87d4b] animate-pulse"></span>
                    <span>Admin Mode Active</span>
                </span>
                <a href="{{ route('home') }}" target="_blank" 
                   class="inline-flex items-center gap-2 bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs sm:text-sm font-bold px-4 py-2 rounded-full shadow-sm transition">
                    <span>Lihat Toko</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-6 lg:p-10 max-w-7xl w-full mx-auto">
            
            <!-- Global Flash Messages -->
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">✅</span>
                        <p class="text-sm font-semibold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">⚠️</span>
                        <p class="text-sm font-semibold">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-xs">
                    <p class="text-sm font-bold mb-2">Harap periksa kesalahan input berikut:</p>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
