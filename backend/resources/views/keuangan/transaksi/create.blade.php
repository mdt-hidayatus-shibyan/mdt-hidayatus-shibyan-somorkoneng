@section('title', 'Catat Transaksi Keuangan')

<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('keuangan.transaksi.index') }}"
                    class="text-xs font-bold text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 inline-flex items-center gap-1.5 mb-1.5 transition-colors">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Buku Kas</span>
                </a>
                <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                    Catat Transaksi Keuangan
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Pencatatan penerimaan, pengeluaran kas operasional, dan mutasi antar kas/bank.
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="m3-glass-card p-6 md:p-8 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-xl">
            @include('keuangan.transaksi.form-modal', [
                'akuns' => $akuns,
                'banks' => $banks,
                'kategoris' => $kategoris,
                'defaultJenis' => $defaultJenis,
            ])
        </div>
    </div>

    @push('scripts')
        <script>
            function closeTransaksiModal() {
                window.location.href = "{{ route('keuangan.transaksi.index') }}";
            }

            function submitTransaksiForm(e) {
                e.preventDefault();
                const form = e.target;
                const btn = document.getElementById('btnSubmitTransaksi');
                btn.disabled = true;
                btn.innerHTML = `<i class="bi bi-arrow-repeat animate-spin text-sm"></i> Menyimpan...`;

                const formData = new FormData(form);

                fetch("{{ route('keuangan.transaksi.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect || "{{ route('keuangan.transaksi.index') }}";
                        });
                    })
                    .catch(err => {
                        let msg = err.message || 'Terjadi kesalahan validasi.';
                        if (err.errors) {
                            msg = Object.values(err.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mencatat Transaksi',
                            html: msg
                        });
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = `<i class="bi bi-check2-circle text-sm"></i> <span>Simpan Transaksi</span>`;
                    });
            }
        </script>
    @endpush
</x-app-layout>
