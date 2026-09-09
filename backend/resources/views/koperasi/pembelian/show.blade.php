@section('title', 'Detail Faktur Pembelian #' . $pembelian->nomor_faktur)
<x-app-layout>

    <div class="max-w-4xl mx-auto mb-8" x-data="{ openPelunasan: false, openFoto: false }">

        <!-- Top Navigation & Action Buttons -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-20">
            <a href="{{ route('koperasi.pembelian.index') }}"
                class="inline-flex items-center gap-2 text-xs font-bold text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                <i class="bi bi-arrow-left text-sm"></i>
                <span>Kembali ke Riwayat Pembelian</span>
            </a>

            <div class="flex items-center gap-2 flex-wrap">
                @if ($pembelian->status === 'Selesai' && $pembelian->status_pembayaran === 'Belum_Lunas')
                    <button type="button" @click="openPelunasan = true"
                        class="min-h-[40px] px-4 py-2 rounded-xl md:rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs inline-flex items-center gap-1.5 active:scale-95 shadow-md shadow-amber-500/20 transition-all">
                        <i class="bi bi-wallet2 text-sm"></i>
                        <span>Pelunasan Hutang Supplier</span>
                    </button>
                @endif

                <a href="{{ route('koperasi.pembelian.cetak', $pembelian->id) }}" target="_blank"
                    class="m3-btn-secondary min-h-[40px] px-4 py-2 text-xs font-bold inline-flex items-center gap-1.5 active:scale-95 shadow-2xs">
                    <i class="bi bi-printer text-sm"></i>
                    <span>Cetak Faktur Penerimaan</span>
                </a>

                @if ($pembelian->status === 'Selesai')
                    <button type="button" onclick="confirmBatalPembelian('{{ route('koperasi.pembelian.batal', $pembelian->id) }}', '{{ $pembelian->nomor_faktur }}')"
                        class="min-h-[40px] px-3.5 py-2 rounded-xl md:rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 border border-rose-500/20 text-xs font-bold transition-all inline-flex items-center gap-1.5 active:scale-95"
                        title="Batalkan Faktur & Kurangi Kembali Stok">
                        <i class="bi bi-x-circle-fill"></i>
                        <span>Batalkan Faktur</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- NOTIFIKASI BANNER STATUS HUTANG / PELUNASAN -->
        @if ($pembelian->status === 'Dibatalkan')
            <div class="mb-6 p-4 md:p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center text-lg font-black shrink-0 shadow-sm">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-rose-800 dark:text-rose-300">
                        Faktur Pembelian Telah Dibatalkan (VOID)
                    </h4>
                    <p class="text-xs text-rose-700/80 dark:text-rose-400/80 font-medium mt-0.5">
                        Alasan pembatalan: <em>{{ $pembelian->alasan_batal ?? 'Dibatalkan oleh Pengurus' }}</em> • Stok telah dikurangi kembali.
                    </p>
                </div>
            </div>
        @elseif ($pembelian->status_pembayaran === 'Belum_Lunas')
            <div class="mb-6 p-4 md:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg font-black shrink-0 shadow-sm">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-amber-800 dark:text-amber-300">
                            Faktur Pembelian Tempo (Hutang ke Supplier Belum Lunas)
                        </h4>
                        <p class="text-xs text-amber-700/80 dark:text-amber-400/80 font-medium mt-0.5">
                            Sisa hutang kepada <strong>{{ $pembelian->supplier }}</strong> sebesar <strong>Rp {{ number_format($pembelian->sisa_hutang, 0, ',', '.') }}</strong>
                            @if ($pembelian->tanggal_jatuh_tempo)
                                • Jatuh Tempo: <strong>{{ $pembelian->tanggal_jatuh_tempo->format('d/m/Y') }}</strong>
                            @endif
                        </p>
                    </div>
                </div>
                <button type="button" @click="openPelunasan = true"
                    class="min-h-[38px] px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold inline-flex items-center gap-1.5 shadow-sm active:scale-95 transition-all shrink-0">
                    <i class="bi bi-wallet2"></i>
                    <span>Lunasi Hutang</span>
                </button>
            </div>
        @else
            <div class="mb-6 p-4 md:p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg font-black shrink-0 shadow-sm">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-emerald-800 dark:text-emerald-300">
                        Faktur Pembelian LUNAS
                    </h4>
                    <p class="text-xs text-emerald-700/80 dark:text-emerald-400/80 font-medium mt-0.5">
                        Metode: <strong>{{ str_replace('_', ' ', $pembelian->metode_pembayaran) }}</strong>
                        @if ($pembelian->tanggal_pelunasan)
                            • Dilunasi pada: <strong>{{ $pembelian->tanggal_pelunasan->format('d/m/Y H:i') }}</strong>
                            via <strong>{{ str_replace('_', ' ', $pembelian->metode_pelunasan ?? 'Tunai') }}</strong>
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <!-- MAIN CARD FAKTUR -->
        <div class="m3-glass-card p-5 sm:p-7 md:p-8 shadow-sm dark:shadow-none space-y-6 relative z-10">

            <!-- HEADER FAKTUR -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-zinc-200/80 dark:border-zinc-800/80 gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">FAKTUR PENERIMAAN BARANG KOPERASI</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black font-mono text-zinc-900 dark:text-white mt-1">
                        {{ $pembelian->nomor_faktur }}
                    </h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-semibold mt-1">
                        Dicatat pada {{ $pembelian->tanggal->format('d F Y, H:i') }} WIB • Petugas: <strong>{{ $pembelian->petugas?->name ?? 'Administrator' }}</strong>
                    </p>
                </div>

                <div class="flex flex-col items-start sm:items-end">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Supplier / Toko Grosir</span>
                    <span class="text-base font-black text-zinc-900 dark:text-white mt-0.5">
                        {{ $pembelian->supplier }}
                    </span>
                    @if ($pembelian->nomor_faktur_supplier)
                        <span class="text-xs font-mono font-bold text-zinc-500 dark:text-zinc-400">
                            Nota Ref: #{{ $pembelian->nomor_faktur_supplier }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- TABEL RINCIAN ITEM -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left font-bold uppercase tracking-wider text-[10px] text-zinc-400 dark:text-zinc-500 border-b border-zinc-200/60 dark:border-zinc-800/60 pb-2.5">
                            <th class="pb-2.5 w-8">#</th>
                            <th class="pb-2.5">Nama Produk</th>
                            <th class="pb-2.5 text-center">Qty Masuk</th>
                            <th class="pb-2.5 text-right">Harga Beli (HPP)</th>
                            <th class="pb-2.5 text-right">Harga Jual Baru</th>
                            <th class="pb-2.5 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/40 dark:divide-zinc-800/40 font-medium">
                        @foreach ($pembelian->details as $idx => $d)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20">
                                <td class="py-3 text-zinc-400 font-bold">{{ $idx + 1 }}</td>
                                <td class="py-3">
                                    <div class="font-bold text-zinc-900 dark:text-white">
                                        {{ $d->nama_produk }}
                                    </div>
                                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono">
                                        {{ $d->kode_produk }} • Satuan: {{ $d->satuan }}
                                    </span>
                                </td>
                                <td class="py-3 text-center font-bold text-zinc-900 dark:text-white">
                                    {{ $d->jumlah }} {{ $d->satuan }}
                                </td>
                                <td class="py-3 text-right font-mono font-bold text-zinc-700 dark:text-zinc-300">
                                    Rp {{ number_format($d->harga_beli_satuan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    @if($d->harga_jual_satuan > 0)
                                        Rp {{ number_format($d->harga_jual_satuan, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 text-right font-mono font-black text-zinc-900 dark:text-white">
                                    Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- TOTAL & BREAKDOWN -->
            <div class="pt-6 border-t border-zinc-200/80 dark:border-zinc-800/80 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Info Tambahan / Catatan & Bukti -->
                <div class="space-y-4">
                    @if ($pembelian->catatan)
                        <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200/60 dark:border-zinc-800/60">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block mb-1">Catatan Pengadaan</span>
                            <p class="text-xs text-zinc-700 dark:text-zinc-300 font-medium">
                                {{ $pembelian->catatan }}
                            </p>
                        </div>
                    @endif

                    <!-- Foto Nota Fisik Preview -->
                    @if ($pembelian->foto_faktur)
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block mb-1.5">Lampiran Foto Faktur Fisik</span>
                            <div class="relative group inline-block cursor-pointer" @click="openFoto = true">
                                <img src="{{ asset('storage/' . $pembelian->foto_faktur) }}" alt="Nota Fisik"
                                    class="w-36 h-28 object-cover rounded-xl border border-zinc-200 dark:border-zinc-800 group-hover:opacity-90 transition-opacity shadow-sm">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/40 rounded-xl transition-opacity text-white text-xs font-bold gap-1">
                                    <i class="bi bi-zoom-in"></i> Perbesar
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Financial Breakdown -->
                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400 font-semibold">
                        <span>Total Nilai Barang (Subtotal)</span>
                        <span class="font-mono font-bold text-zinc-900 dark:text-white">
                            Rp {{ number_format($pembelian->total_nominal, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($pembelian->ongkir > 0)
                        <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400 font-semibold">
                            <span>Biaya Pengiriman (Ongkir)</span>
                            <span class="font-mono font-bold text-zinc-900 dark:text-white">
                                Rp {{ number_format($pembelian->ongkir, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    @if ($pembelian->diskon > 0)
                        <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400 font-semibold">
                            <span>Diskon / Potongan Grosir</span>
                            <span class="font-mono font-bold text-rose-600 dark:text-rose-400">
                                - Rp {{ number_format($pembelian->diskon, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    <div class="pt-2 border-t border-zinc-200/80 dark:border-zinc-800/80 flex justify-between items-center">
                        <span class="text-sm font-black text-zinc-900 dark:text-white">Grand Total Faktur</span>
                        <span class="text-lg font-mono font-black text-emerald-600 dark:text-emerald-400">
                            Rp {{ number_format($pembelian->total_nominal + $pembelian->ongkir - $pembelian->diskon, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400 font-semibold pt-1">
                        <span>Nominal Dibayar (DP / Kas)</span>
                        <span class="font-mono font-bold text-zinc-900 dark:text-white">
                            Rp {{ number_format($pembelian->nominal_bayar, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($pembelian->sisa_hutang > 0)
                        <div class="flex justify-between items-center pt-2 border-t border-dashed border-amber-500/30 text-amber-700 dark:text-amber-400 font-bold">
                            <span>Sisa Hutang Tempo</span>
                            <span class="font-mono font-black text-sm">
                                Rp {{ number_format($pembelian->sisa_hutang, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- MODAL PELUNASAN HUTANG SUPPLIER -->
        <template x-if="openPelunasan">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
                <div class="m3-glass-card max-w-md w-full p-6 space-y-5 bg-white dark:bg-zinc-900 shadow-2xl relative">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-200/80 dark:border-zinc-800/80">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <h3 class="text-base font-black text-zinc-900 dark:text-white">
                                Pelunasan Hutang Supplier
                            </h3>
                        </div>
                        <button type="button" @click="openPelunasan = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                            <i class="bi bi-x-lg text-lg"></i>
                        </button>
                    </div>

                    <form action="{{ route('koperasi.pembelian.lunasi', $pembelian->id) }}" method="POST" id="formPelunasanSupplier" class="space-y-4">
                        @csrf

                        <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs">
                            <div class="text-amber-800 dark:text-amber-400 font-semibold">Total Tagihan Hutang:</div>
                            <div class="text-lg font-mono font-black text-amber-700 dark:text-amber-300">
                                Rp {{ number_format($pembelian->sisa_hutang, 0, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                                Metode Pelunasan <span class="text-rose-500">*</span>
                            </label>
                            <select name="metode_pelunasan" required class="m3-input-glass w-full text-xs font-bold">
                                <option value="Tunai_Kas">Tunai Kas Koperasi</option>
                                <option value="Transfer_Bank">Transfer Bank Koperasi</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                                Nominal Pelunasan <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="nominal_bayar" value="{{ (float)$pembelian->sisa_hutang }}" required min="1" step="1000"
                                class="m3-input-glass w-full text-xs font-mono font-bold">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                                Catatan Pelunasan
                            </label>
                            <input type="text" name="catatan_pelunasan" placeholder="Contoh: Dilunasi via transfer BSI / Nota lunas toko"
                                class="m3-input-glass w-full text-xs font-medium">
                        </div>

                        <div class="pt-3 border-t border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-end gap-2.5">
                            <button type="button" @click="openPelunasan = false"
                                class="m3-btn-secondary min-h-[38px] px-4 text-xs font-bold">
                                Batal
                            </button>
                            <button type="submit"
                                class="m3-btn-primary min-h-[38px] px-5 text-xs font-black inline-flex items-center gap-1.5 shadow-md shadow-primary/20">
                                <i class="bi bi-check2-circle"></i>
                                <span>Konfirmasi Lunas</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- LIGHTBOX FOTO MODAL -->
        @if ($pembelian->foto_faktur)
            <template x-if="openFoto">
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click="openFoto = false">
                    <div class="relative max-w-3xl max-h-[90vh] overflow-auto p-2" @click.stop>
                        <img src="{{ asset('storage/' . $pembelian->foto_faktur) }}" alt="Nota Fisik"
                            class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl mx-auto object-contain">
                        <button type="button" @click="openFoto = false"
                            class="absolute top-4 right-4 w-10 h-10 rounded-full bg-black/60 text-white flex items-center justify-center hover:bg-black/90 transition-colors">
                            <i class="bi bi-x-lg text-lg"></i>
                        </button>
                    </div>
                </div>
            </template>
        @endif

    </div>

    <!-- Scripts for AJAX Handlers -->
    <script>
        $(document).on('submit', '#formPelunasanSupplier', function(e) {
            e.preventDefault();
            const form = this;
            const $btn = $(form).find('button[type="submit"]');
            const orig = $btn.html();

            $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...');

            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: $(form).serialize(),
                headers: { 'Accept': 'application/json' },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Hutang Berhasil Dilunasi!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                        $btn.prop('disabled', false).html(orig);
                    }
                },
                error: function(xhr) {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                    $btn.prop('disabled', false).html(orig);
                }
            });
        });

        function confirmBatalPembelian(url, noFaktur) {
            Swal.fire({
                title: 'Batalkan Faktur Ini?',
                html: `Apakah Anda yakin ingin membatalkan faktur <strong>${noFaktur}</strong>?<br><span class="text-xs text-rose-500 font-semibold">Penambahan stok produk dari faktur ini akan dikurangi kembali.</span>`,
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Tuliskan alasan pembatalan...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan pembatalan wajib diisi!';
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan & Rollback Stok',
                cancelButtonText: 'Tutup',
                confirmButtonColor: '#e11d48',
                showLoaderOnConfirm: true,
                preConfirm: (alasan) => {
                    return $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            alasan: alasan
                        },
                        headers: { 'Accept': 'application/json' }
                    }).then(response => {
                        if (!response.success) {
                            throw new Error(response.message || 'Gagal membatalkan faktur.');
                        }
                        return response;
                    }).catch(error => {
                        Swal.showValidationMessage(`Request gagal: ${error.statusText || error.message || error}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Faktur Dibatalkan!',
                        text: result.value.message,
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }
    </script>

</x-app-layout>
