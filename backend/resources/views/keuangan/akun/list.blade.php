<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr
                class="border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30 text-[11px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                <th class="py-3.5 px-4">Kode & Nama Pos Akun</th>
                <th class="py-3.5 px-4">Tipe Akun</th>
                <th class="py-3.5 px-4 text-right">Saldo Awal</th>
                <th class="py-3.5 px-4 text-right">Saldo Berjalan</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
            @forelse ($akuns as $akun)
                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40 transition-colors group">
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base flex-shrink-0 border border-emerald-500/20 shadow-2xs">
                                <i class="bi bi-wallet-fill"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-zinc-900 dark:text-white truncate">
                                        {{ $akun->nama_akun }}
                                    </span>
                                    <span
                                        class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-mono">
                                        {{ $akun->kode_akun }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 truncate mt-0.5">
                                    {{ $akun->deskripsi ?? 'Tidak ada deskripsi' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 px-4">
                        @php
                            $badgeClasses = match ($akun->tipe_akun) {
                                'kas' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                'bank' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                                'operasional'
                                    => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                'investasi'
                                    => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                default => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20',
                            };
                        @endphp
                        <span
                            class="px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider border {{ $badgeClasses }}">
                            {{ strtoupper($akun->tipe_akun) }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-right font-medium text-zinc-600 dark:text-zinc-300 font-mono">
                        Rp {{ number_format($akun->saldo_awal, 0, ',', '.') }}
                    </td>
                    <td
                        class="py-3.5 px-4 text-right font-black text-sm font-mono {{ $akun->saldo_berjalan >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        Rp {{ number_format($akun->saldo_berjalan, 0, ',', '.') }}
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <x-toggle :checked="$akun->is_active" :url="route('keuangan.akun.toggle-status', $akun->id)" name="is_active" ajax="true" />
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('keuangan.akun.edit', $akun->id) }}"
                                class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 flex items-center justify-center transition-all hover:scale-105 active:scale-90 border border-blue-500/20 shadow-2xs outline-none action-modal"
                                title="Edit Pos Akun">
                                <i class="bi bi-pencil-fill text-xs"></i>
                            </a>
                            <form action="{{ route('keuangan.akun.destroy', $akun->id) }}" method="POST"
                                class="delete-ajax inline m-0 p-0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 flex items-center justify-center transition-all hover:scale-105 active:scale-90 border border-rose-500/20 shadow-2xs outline-none"
                                    title="Hapus Pos Akun">
                                    <i class="bi bi-trash-fill text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-zinc-400 dark:text-zinc-500">
                        <i class="bi bi-wallet2 text-3xl mb-2 block"></i>
                        <p class="font-bold">Belum ada pos akun keuangan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
