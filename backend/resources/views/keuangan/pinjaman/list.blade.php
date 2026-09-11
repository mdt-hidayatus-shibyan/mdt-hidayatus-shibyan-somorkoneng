<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr
                class="border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30 text-[11px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                <th class="py-3.5 px-4">Kode & Tanggal</th>
                <th class="py-3.5 px-4">Peminjam / Nasabah</th>
                <th class="py-3.5 px-4">Keperluan & Tenor</th>
                <th class="py-3.5 px-4 text-right">Nominal Pinjaman</th>
                <th class="py-3.5 px-4 text-right">Sisa Piutang</th>
                <th class="py-3.5 px-4">Agunan / Jaminan</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
            @forelse ($pinjamans as $pinjaman)
                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40 transition-colors group">
                    <td class="py-3.5 px-4">
                        <span class="font-mono font-bold text-zinc-900 dark:text-white block">
                            {{ $pinjaman->kode_pinjaman }}
                        </span>
                        <span class="text-[11px] text-zinc-400">
                            {{ $pinjaman->tanggal_pengajuan->format('d/m/Y') }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-2.5">
                            <x-avatar :src="$pinjaman->nasabah->foto_nasabah
                                ? asset('storage/' . $pinjaman->nasabah->foto_nasabah)
                                : null" :name="$pinjaman->nasabah->nama_lengkap" size="sm" />
                            <div class="min-w-0">
                                <span class="font-bold text-zinc-900 dark:text-white block truncate">
                                    {{ $pinjaman->nasabah->nama_lengkap }}
                                </span>
                                <span class="text-[10px] uppercase font-bold text-zinc-400 block">
                                    {{ str_replace('_', ' ', $pinjaman->nasabah->tipe_nasabah) }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 max-w-xs">
                        <p class="text-zinc-700 dark:text-zinc-300 truncate"
                            title="{{ $pinjaman->keperluan_pinjaman }}">
                            {{ $pinjaman->keperluan_pinjaman }}
                        </p>
                        <div class="text-[11px] text-zinc-400 flex items-center gap-2 mt-0.5 font-mono">
                            <span>{{ $pinjaman->tenor_bulan }} Bln</span>
                            <span>•</span>
                            <span>Rp {{ number_format($pinjaman->nominal_angsuran_total, 0, ',', '.') }}/bln</span>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 text-right font-black text-sm font-mono text-zinc-900 dark:text-white">
                        Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}
                    </td>
                    <td
                        class="py-3.5 px-4 text-right font-black text-sm font-mono {{ $pinjaman->sisa_pinjaman > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                        Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                    </td>
                    <td class="py-3.5 px-4">
                        @if ($pinjaman->jaminans->count() > 0)
                            <div class="flex items-center gap-1.5">
                                <span
                                    class="w-6 h-6 rounded-md bg-amber-500/10 text-amber-600 flex items-center justify-center text-xs">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </span>
                                <span class="font-bold text-zinc-800 dark:text-zinc-200 truncate max-w-[120px]"
                                    title="{{ $pinjaman->jaminans->first()->nama_barang_jaminan }}">
                                    {{ $pinjaman->jaminans->first()->nama_barang_jaminan }}
                                </span>
                            </div>
                        @else
                            <span class="text-zinc-400 italic text-[11px]">Tanpa Agunan</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        @php
                            $statusClasses = match ($pinjaman->status) {
                                'pengajuan' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                'disetujui' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                'dicairkan'
                                    => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                                'lunas'
                                    => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                'macet'
                                    => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20 animate-pulse',
                                'ditolak' => 'bg-zinc-500/10 text-zinc-500 dark:text-zinc-400 border-zinc-500/20',
                                default => 'bg-zinc-500/10 text-zinc-600 border-zinc-500/20',
                            };
                        @endphp
                        <span
                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $statusClasses }}">
                            {{ $pinjaman->status }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('keuangan.pinjaman.show', $pinjaman->id) }}"
                                class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 flex items-center justify-center transition-colors"
                                title="Kelola & Detail Pinjaman">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <a href="{{ route('keuangan.pinjaman.cetak-perjanjian', $pinjaman->id) }}" target="_blank"
                                class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 hover:bg-purple-500/20 flex items-center justify-center transition-colors"
                                title="Cetak Surat Akad Pinjaman">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-8">
                        <x-empty-state icon="bi bi-shield-lock" title="Belum Ada Pinjaman"
                            message="Belum ada data pinjaman yang tercatat dalam sistem." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800">
    {{ $pinjamans->links('vendor.pagination.custom') }}
</div>
