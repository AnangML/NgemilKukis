<!-- Customer Auth Modal (Google, Email, Phone OTP Prototype) -->
<div x-data="{
    isOpen: false,
    activeTab: 'email', // 'google', 'email', 'phone'
    emailMode: 'login', // 'login' or 'register'
    
    // Phone OTP State
    phone: '',
    customerName: '',
    otpCode: '',
    otpStep: 1, // 1: input phone, 2: input code
    otpLoading: false,
    otpError: '',
    otpSuccessMsg: '',
    simulatedCode: '',
    countdown: 0,
    timer: null,

    open(tab = 'email', mode = 'login') {
        this.activeTab = tab;
        this.emailMode = mode;
        this.isOpen = true;
        this.otpError = '';
    },

    close() {
        this.isOpen = false;
        this.otpError = '';
    },

    startCountdown() {
        this.countdown = 60;
        if (this.timer) clearInterval(this.timer);
        this.timer = setInterval(() => {
            if (this.countdown > 0) {
                this.countdown--;
            } else {
                clearInterval(this.timer);
            }
        }, 1000);
    },

    async sendPhoneOtp() {
        if (!this.phone || this.phone.length < 9) {
            this.otpError = 'Masukkan nomor telepon yang valid (minimal 9 digit).';
            return;
        }

        this.otpLoading = true;
        this.otpError = '';
        this.otpSuccessMsg = '';

        try {
            const res = await fetch('{{ route('auth.phone.send-otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ phone: this.phone })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                this.otpStep = 2;
                this.simulatedCode = data.simulation_code || '';
                this.otpSuccessMsg = data.message;
                this.startCountdown();
            } else {
                this.otpError = data.message || 'Gagal mengirim kode OTP. Coba lagi.';
            }
        } catch (e) {
            this.otpError = 'Terjadi kesalahan jaringan saat mengirim OTP.';
        } finally {
            this.otpLoading = false;
        }
    },

    async verifyPhoneOtp() {
        if (!this.otpCode || this.otpCode.length !== 4) {
            this.otpError = 'Masukkan 4 digit kode OTP.';
            return;
        }

        this.otpLoading = true;
        this.otpError = '';

        try {
            const res = await fetch('{{ route('auth.phone.verify-otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    phone: this.phone,
                    otp_code: this.otpCode,
                    name: this.customerName
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                window.location.reload();
            } else {
                this.otpError = data.message || 'Kode OTP salah atau kedaluwarsa.';
            }
        } catch (e) {
            this.otpError = 'Terjadi kesalahan saat memverifikasi OTP.';
        } finally {
            this.otpLoading = false;
        }
    }
}"
@open-auth-modal.window="open($event.detail?.tab || 'email', $event.detail?.mode || 'login')"
@keydown.escape.window="close()"
x-cloak
x-show="isOpen"
class="fixed inset-0 z-50 overflow-y-auto"
aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <!-- Backdrop (30% Dark Brown Overlay) -->
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 bg-[#2d180c]/60 backdrop-blur-xs transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <!-- Modal Card (60% Dominant White) -->
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-[#ede7e2] p-6 sm:p-8">

            <!-- Close Button -->
            <button @click="close()" 
                    class="absolute top-5 right-5 text-[#6d4630] hover:text-[#2d180c] p-2 rounded-full hover:bg-stone-100 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Brand Header & VIP Club Perk -->
            <div class="text-center space-y-1.5 pb-5 border-b border-[#ede7e2]">
                <div class="inline-flex items-center gap-1.5 bg-[#f6efe8] border border-[#ede7e2] px-3.5 py-1 rounded-full text-[11px] font-bold text-[#b87d4b]">
                    <span>Kukis VIP Club</span>
                    <span>•</span>
                    <span>Diskon Member Rp 10.000</span>
                </div>
                <h3 class="text-xl font-extrabold text-[#2d180c]" id="modal-title">
                    Masuk ke Akun Toko
                </h3>
                <p class="text-xs text-[#6d4630]">
                    Nikmati potongan belanja otomatis dan checkout kilat tanpa repot ketik alamat.
                </p>
            </div>

            <!-- 3 Main Tabs (Google, Email, No. Telepon) -->
            <div class="grid grid-cols-3 gap-1 bg-[#faf7f4] p-1.5 rounded-2xl border border-[#ede7e2] my-5 text-xs font-bold text-center">
                <button type="button" 
                        @click="activeTab = 'google'" 
                        :class="activeTab === 'google' ? 'bg-[#b87d4b] text-white shadow-xs' : 'text-[#6d4630] hover:text-[#2d180c]'"
                        class="py-2.5 rounded-xl transition cursor-pointer">
                    Google
                </button>
                <button type="button" 
                        @click="activeTab = 'email'" 
                        :class="activeTab === 'email' ? 'bg-[#b87d4b] text-white shadow-xs' : 'text-[#6d4630] hover:text-[#2d180c]'"
                        class="py-2.5 rounded-xl transition cursor-pointer">
                    Email
                </button>
                <button type="button" 
                        @click="activeTab = 'phone'" 
                        :class="activeTab === 'phone' ? 'bg-[#b87d4b] text-white shadow-xs' : 'text-[#6d4630] hover:text-[#2d180c]'"
                        class="py-2.5 rounded-xl transition cursor-pointer">
                    No. Telepon
                </button>
            </div>

            <!-- TAB 1: GOOGLE 1-CLICK LOGIN -->
            <div x-show="activeTab === 'google'" class="space-y-4 pt-1">
                <p class="text-xs text-[#6d4630] text-center leading-relaxed">
                    Masuk instan menggunakan akun Google Anda dalam 1 klik. Profil dan email otomatis terhubung.
                </p>

                <a href="{{ route('auth.google.redirect') }}" 
                   class="w-full flex items-center justify-center gap-3 bg-white hover:bg-stone-50 text-[#2d180c] text-sm font-bold py-3.5 px-4 rounded-2xl border border-[#ede7e2] shadow-xs transition transform active:scale-98">
                    <!-- Google Official Icon -->
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Lanjutkan dengan Google</span>
                </a>

                <!-- Demo Simulation Button for Local Testing -->
                <div class="pt-2 text-center">
                    <a href="{{ route('auth.google.simulate') }}" 
                       class="text-[11px] font-semibold text-[#b87d4b] hover:underline">
                        Mode Demo: Klik di sini untuk simulasi login instan Google
                    </a>
                </div>
            </div>

            <!-- TAB 2: EMAIL & PASSWORD (OPTION 1: INSTANT ACTIVATION) -->
            <div x-show="activeTab === 'email'" class="space-y-4 pt-1">
                <!-- Sub-toggle: Masuk vs Daftar Baru -->
                <div class="flex items-center justify-center gap-6 border-b border-[#ede7e2] pb-3 text-xs font-bold">
                    <button type="button" 
                            @click="emailMode = 'login'" 
                            :class="emailMode === 'login' ? 'text-[#b87d4b] border-b-2 border-[#b87d4b] pb-1' : 'text-[#6d4630] hover:text-[#2d180c]'"
                            class="cursor-pointer transition">
                        Masuk Akun
                    </button>
                    <button type="button" 
                            @click="emailMode = 'register'" 
                            :class="emailMode === 'register' ? 'text-[#b87d4b] border-b-2 border-[#b87d4b] pb-1' : 'text-[#6d4630] hover:text-[#2d180c]'"
                            class="cursor-pointer transition">
                        Daftar Baru (Langsung Aktif)
                    </button>
                </div>

                <!-- SUB-VIEW 1: LOGIN FORM -->
                <form x-show="emailMode === 'login'" method="POST" action="{{ route('customer.login') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                            Alamat Email
                        </label>
                        <input type="email" name="email" required placeholder="nama@email.com"
                               class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                            Kata Sandi (Password)
                        </label>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-[#6d4630]">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" value="1" class="rounded text-[#b87d4b] focus:ring-[#b87d4b]">
                            <span>Ingat Saya</span>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold py-3.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
                        Masuk ke Akun
                    </button>
                </form>

                <!-- SUB-VIEW 2: REGISTER FORM (OPTION 1 INSTANT) -->
                <form x-show="emailMode === 'register'" method="POST" action="{{ route('customer.register') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="Contoh: Sarah Anindya"
                               class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                                Email <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" required placeholder="sarah@email.com"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                                No. Telepon / WA
                            </label>
                            <input type="text" name="phone" placeholder="08123456789"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                                Kata Sandi <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                                Ulangi Sandi <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi sandi"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                        </div>
                    </div>

                    <p class="text-[10px] text-[#6d4630] leading-relaxed pt-1">
                        Pendaftaran langsung aktif seketika tanpa jeda verifikasi email. Diskon member Rp 10.000 otomatis aktif.
                    </p>

                    <button type="submit" 
                            class="w-full bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold py-3.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer">
                        Daftar & Klaim Diskon Rp 10.000
                    </button>
                </form>
            </div>

            <!-- TAB 3: PHONE NUMBER (PROTOTYPE / DEMO SIMULATOR) -->
            <div x-show="activeTab === 'phone'" class="space-y-4 pt-1">
                <!-- Banner Prototype Notice -->
                <div class="bg-[#faf7f4] border border-[#ede7e2] p-3 rounded-2xl text-[11px] text-[#6d4630] space-y-1">
                    <p class="font-bold text-[#2d180c]">Mode Prototype Telepon (Rp 0 Biaya)</p>
                    <p>Kode OTP simulasi akan ditampilkan langsung di layar ini sehingga tidak memerlukan gateway SMS berbayar.</p>
                </div>

                <!-- Error Alert -->
                <div x-show="otpError" class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 font-semibold" x-text="otpError"></div>

                <!-- Simulation Code Alert Box -->
                <div x-show="simulatedCode && otpStep === 2" class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-center space-y-1 animate-pulse">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800">Kode OTP Simulasi Anda:</span>
                    <p class="text-2xl font-extrabold tracking-widest text-[#2d180c]" x-text="simulatedCode"></p>
                    <span class="text-[10px] text-amber-700 block">Salin 4 digit angka di atas ke kolom input verifikasi di bawah</span>
                </div>

                <!-- STEP 1: INPUT PHONE -->
                <div x-show="otpStep === 1" class="space-y-3.5">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="tel" 
                               x-model="phone" 
                               placeholder="Contoh: 081234567890" 
                               class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-[#2d180c] mb-1">
                            Nama Anda (Opsional)
                        </label>
                        <input type="text" 
                               x-model="customerName" 
                               placeholder="Nama panggilan Anda" 
                               class="w-full px-4 py-2.5 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-xs text-[#2d180c]">
                    </div>

                    <button type="button" 
                            @click="sendPhoneOtp()"
                            :disabled="otpLoading"
                            class="w-full bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold py-3.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer disabled:opacity-50">
                        <span x-show="!otpLoading">Kirim Kode OTP (Simulasi)</span>
                        <span x-show="otpLoading">Mengirim...</span>
                    </button>
                </div>

                <!-- STEP 2: VERIFY CODE -->
                <div x-show="otpStep === 2" class="space-y-3.5">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[11px] font-bold uppercase tracking-wider text-[#2d180c]">
                                Masukkan 4 Digit OTP
                            </label>
                            <button type="button" @click="otpStep = 1; otpError = '';" class="text-[10px] font-bold text-[#b87d4b] hover:underline">
                                Ganti Nomor
                            </button>
                        </div>
                        <input type="text" 
                               x-model="otpCode" 
                               maxlength="4"
                               placeholder="Contoh: 1234" 
                               class="w-full px-4 py-3 rounded-xl border border-[#ede7e2] focus:border-[#b87d4b] outline-none text-center text-lg font-extrabold tracking-widest text-[#2d180c]">
                    </div>

                    <button type="button" 
                            @click="verifyPhoneOtp()"
                            :disabled="otpLoading"
                            class="w-full bg-[#b87d4b] hover:bg-[#a36b3b] text-white text-xs font-bold py-3.5 rounded-full shadow-sm transition transform active:scale-95 cursor-pointer disabled:opacity-50">
                        <span x-show="!otpLoading">Verifikasi & Masuk Akun</span>
                        <span x-show="otpLoading">Memverifikasi...</span>
                    </button>

                    <div class="text-center pt-1">
                        <button type="button" 
                                @click="sendPhoneOtp()" 
                                :disabled="countdown > 0"
                                class="text-[11px] font-semibold text-[#6d4630] hover:text-[#2d180c] disabled:opacity-50">
                            <span x-show="countdown > 0">Kirim ulang dalam <span x-text="countdown"></span> detik</span>
                            <span x-show="countdown === 0" class="text-[#b87d4b] underline">Kirim Ulang Kode OTP</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Guest Checkout Reassurance Footer -->
            <div class="mt-6 pt-4 border-t border-[#ede7e2] text-center">
                <button type="button" 
                        @click="close()" 
                        class="text-xs font-semibold text-[#6d4630] hover:text-[#2d180c] transition">
                    Tetap Lanjutkan Belanja Sebagai Tamu &rarr;
                </button>
            </div>

        </div>
    </div>
</div>
