<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-300 animate-in fade-in" id="userResetPasswordModal">
    <div class="m3-glass-card !bg-white dark:!bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-3xl w-full max-w-md flex flex-col shadow-2xl overflow-hidden transform transition-all animate-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black text-lg border border-amber-500/20">
                    <i class="bi bi-key-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight">
                        Reset Password Pengguna
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium" id="resetUserTargetName">
                        Set password baru untuk akun pengguna
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeResetModal()" class="w-8 h-8 rounded-xl flex items-center justify-center text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <i class="bi bi-x-lg text-xs font-bold"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="resetPasswordForm" method="POST" class="p-6 space-y-4">
            @csrf

            <!-- Alert Error Container -->
            <div id="resetErrorAlert" class="hidden p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold"></div>

            <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-medium leading-relaxed">
                <i class="bi bi-exclamation-circle-fill mr-1"></i>
                Setelah password direset, seluruh sesi aktif user ini akan diputus dan wajib login menggunakan password baru.
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                        Password Baru <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" onclick="generateResetPassword()"
                        class="text-[11px] font-black text-primary dark:text-primary-dark hover:underline flex items-center gap-1">
                        <i class="bi bi-shuffle"></i> Buat Acak
                    </button>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-lock-fill text-xs"></i>
                    </div>
                    <input type="text" name="password" id="resetNewPassword" required placeholder="Minimal 6 karakter"
                        class="m3-input-glass w-full !pl-10 text-xs font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                    Konfirmasi Password Baru <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-shield-check text-xs"></i>
                    </div>
                    <input type="text" name="password_confirmation" id="resetNewPasswordConfirm" required placeholder="Ketik ulang password baru"
                        class="m3-input-glass w-full !pl-10 text-xs font-mono font-bold">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-4 border-t border-zinc-200/80 dark:border-zinc-800 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeResetModal()"
                    class="h-10 px-5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-black transition-all">
                    Batal
                </button>
                <button type="submit" id="btnSubmitReset"
                    class="h-10 px-6 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-black shadow-md flex items-center gap-1.5 transition-all active:scale-95">
                    <i class="bi bi-key-fill"></i>
                    <span>Reset Password</span>
                </button>
            </div>
        </form>
    </div>
</div>