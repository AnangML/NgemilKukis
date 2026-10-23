<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk Admin - NgemilKukis</title>

    <!-- Modern Clean Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body class="bg-[#faf7f4] text-[#2d180c] antialiased selection:bg-[#b87d4b] selection:text-white min-h-screen flex items-center justify-center p-4 sm:p-6">

    <div class="w-full max-w-md">
        <!-- Brand Logo Header -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block group">
                <span class="font-extrabold text-3xl tracking-tight text-[#2d180c] block leading-none">
                    Ngemil<span class="text-[#b87d4b]">Kukis.</span>
                </span>
                <span class="text-xs font-bold tracking-wider text-[#6d4630] uppercase mt-1.5 block">Admin Control Panel</span>
            </a>
        </div>

        <!-- Login Card (Putih Bersih 60%) -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl border border-[#ede7e2]">
            <h2 class="text-2xl font-extrabold text-[#2d180c] tracking-tight">Selamat Datang</h2>
            <p class="text-xs text-[#6d4630] mt-1 mb-6">Silakan login dengan kredensial administrator Anda.</p>

            @if(session('error'))
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-bold text-[#2d180c] mb-1.5 uppercase tracking-wider">
                        Email Administrator
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', 'admin@ngemilkukis.com') }}" 
                           required 
                           autofocus
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 outline-none text-sm text-[#2d180c] transition bg-[#fdfbf9]">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-bold text-[#2d180c] mb-1.5 uppercase tracking-wider">
                        Kata Sandi
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           value="password"
                           required 
                           class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] focus:ring-2 focus:ring-[#b87d4b]/20 outline-none text-sm text-[#2d180c] transition bg-[#fdfbf9]">
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-[#6d4630]">
                        <input type="checkbox" name="remember" value="1" checked class="rounded border-[#ede7e2] text-[#b87d4b] focus:ring-[#b87d4b]">
                        <span>Ingat sesi saya</span>
                    </label>
                    <a href="{{ route('home') }}" class="text-[#b87d4b] hover:underline font-semibold">
                        Kembali ke Toko
                    </a>
                </div>

                <!-- Submit Button (Tombol Coklat Muda 10%) -->
                <button type="submit" 
                        class="w-full bg-[#b87d4b] hover:bg-[#a36b3b] text-white font-bold py-3.5 px-6 rounded-2xl shadow-sm transition transform active:scale-98 cursor-pointer flex items-center justify-center gap-2">
                    <span>Masuk ke Admin Panel</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <!-- Test hint box -->
            <div class="mt-6 pt-5 border-t border-[#ede7e2] text-center">
                <p class="text-[11px] text-[#6d4630]">
                    Kredensial Default: <code class="font-bold text-[#2d180c]">admin@ngemilkukis.com</code> / <code class="font-bold text-[#2d180c]">password</code>
                </p>
            </div>
        </div>
    </div>

</body>
</html>
