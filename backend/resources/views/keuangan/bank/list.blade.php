<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr
                class="border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30 text-[11px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                <th class="py-3.5 px-4">Bank & Kantor Cabang</th>
                <th class="py-3.5 px-4">Nomor Rekening</th>
                <th class="py-3.5 px-4">Atas Nama</th>
                <th class="py-3.5 px-4 text-right">Saldo Kas Bank</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
            @forelse ($banks as $bank)
                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40 transition-colors group">
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-lg flex-shrink-0 border border-blue-500/20 shadow-2xs">
                                <i class="bi bi-bank"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-zinc-900 dark:text-white truncate">
                                        {{ $bank->nama_bank }}
                                    </span>
                                    @if ($bank->is_default)
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 shadow-2xs">
                                            Rekening Utama
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 truncate mt-0.5">
                                    {{ $bank->cabang ? 'Cabang: ' . $bank->cabang : 'Kantor Pusat' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-1.5">
                            <span
                                class="font-mono font-bold text-zinc-900 dark:text-white text-xs bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded-lg">
                                {{ $bank->nomor_rekening }}
                            </span>
                            <button type="button" onclick="copyToClipboard('{{ $bank->nomor_rekening }}')"
                                class="w-6 h-6 rounded-md hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 flex items-center justify-center transition-colors"
                                title="Salin No Rekening">
                                <i class="bi bi-clipboard text-xs"></i>
                            </button>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 font-bold text-zinc-800 dark:text-zinc-200">
                        {{ $bank->atas_nama }}
                    </td>
                    <td
                        class="py-3.5 px-4 text-right font-black text-sm font-mono {{ $bank->saldo >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-rose-600 dark:text-rose-400' }}">
                        Rp {{ number_format($bank->saldo, 0, ',', '.') }}
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <x-toggle :checked="$bank->is_active" :url="route('keuangan.bank.toggle-status', $bank->id)" name="is_active" ajax="true" />
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('keuangan.bank.edit', $bank->id) }}"
                                class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 flex items-center justify-center transition-all hover:scale-105 active:scale-90 border border-blue-500/20 shadow-2xs outline-none action-modal"
                                title="Edit Rekening">
                                <i class="bi bi-pencil-fill text-xs"></i>
                            </a>
                            <form action="{{ route('keuangan.bank.destroy', $bank->id) }}" method="POST"
                                class="delete-ajax inline m-0 p-0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 flex items-center justify-center transition-all hover:scale-105 active:scale-90 border border-rose-500/20 shadow-2xs outline-none"
                                    title="Hapus Rekening">
                                    <i class="bi bi-trash-fill text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-zinc-400 dark:text-zinc-500">
                        <i class="bi bi-bank text-3xl mb-2 block"></i>
                        <p class="font-bold">Belum ada data rekening bank terdaftar.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
