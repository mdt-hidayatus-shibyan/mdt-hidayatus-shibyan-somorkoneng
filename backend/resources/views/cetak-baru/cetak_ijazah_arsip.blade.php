<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip_Ijazah_{{ Str::slug($data['nama_murid']) }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: white;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .ijazah-wrapper {
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                width: 21cm !important;
                height: 29.7cm !important;
                max-height: 29.7cm !important;
                overflow: hidden !important;
            }
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #e2e8f0;
            color: #111827;
        }

        /* WADAH UTAMA KERTAS */
        .ijazah-wrapper {
            background-color: white;
            width: 21cm;
            height: 29.7cm;
            max-height: 29.7cm;
            margin: 0 auto;
            position: relative;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            /* Mencegah elemen keluar halaman */

            background-image: url('{{ asset('storage/ijazah_tpq_wrapper.png') }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;

            /* Padding atas sedikit dirapatkan, padding bawah diperkecil agar footer naik */
            padding: 2.8cm 2.7cm 1.5cm 2.7cm;
        }

        /* HEADER & LOGO */
        .header-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .logo-box img {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        .arabic-image img {
            height: 55px;
            width: auto;
            object-fit: contain;
        }

        /* JUDUL */
        .title-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .title-ijazah {
            font-size: 18pt;
            font-weight: 800;
            letter-spacing: 4px;
            margin-bottom: 2px;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .title-tingkat {
            font-size: 11pt;
            font-weight: 700;
            color: #16a34a;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .title-madrasah {
            font-size: 13pt;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .title-alamat {
            font-size: 10.5pt;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .title-tahun {
            font-size: 9.5pt;
            font-weight: 600;
            color: #4b5563;
        }

        /* KONTEN */
        .content-section {
            font-size: 11pt;
            line-height: 1.5;
            text-align: justify;
        }

        .tabel-data {
            width: 95%;
            margin: 10px auto 15px auto;
        }

        .tabel-data td {
            padding-bottom: 4px;
            vertical-align: bottom;
        }

        .tabel-data .label-col {
            width: 170px;
            font-weight: 500;
        }

        .tabel-data .separator-col {
            width: 20px;
            text-align: center;
        }

        .tabel-data .value-col {
            font-weight: 700;
            font-size: 11.5pt;
            border-bottom: 1px dotted #9ca3af;
        }

        .lulus-text {
            text-align: center;
            font-size: 18pt;
            font-weight: 800;
            letter-spacing: 12px;
            margin: 15px 0;
            padding-left: 12px;
        }

        /* FOOTER (FOTO & TANDA TANGAN) */
        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 20px;
            padding: 0 1cm;
        }

        .pas-foto {
            width: 3cm !important;
            height: 4cm !important;
            min-width: 3cm;
            min-height: 4cm;
            border: 2px solid #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            font-weight: 600;
            color: #9ca3af;
            background: #f9fafb;
        }

        .ttd-box {
            text-align: center;
            width: 6.5cm;
        }

        .ttd-box p {
            margin: 0;
            font-size: 10.5pt;
            line-height: 1.3;
        }

        .qr-space {
            height: 2.1cm;
            /* Ukuran QR Code disesuaikan agar pas */
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 4px 0;
        }

        .ttd-nama {
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
            text-underline-offset: 3px;
            font-size: 11pt;
        }

        /* NOMOR IJAZAH (DI KIRI BAWAH) */
        .nomor-bawah {
            position: absolute;
            bottom: 0.2cm;
            left: 1cm;
            font-size: 9.5pt;
            font-weight: 700;
            color: #16a34a;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>

    <!-- TOMBOL AKSI -->
    <div class="no-print fixed top-5 right-5 flex gap-3 z-50">
        <button onclick="window.close()"
            class="px-5 py-2.5 bg-slate-600 text-white rounded-lg font-bold shadow-sm hover:bg-slate-700 transition-colors">Tutup</button>
        <button onclick="window.print()"
            class="px-5 py-2.5 bg-[#0F3D36] text-white rounded-lg font-bold shadow-sm hover:bg-[#0a2e28] transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak A4
        </button>
    </div>

    <!-- KERTAS IJAZAH -->
    <div class="ijazah-wrapper">

        <!-- HEADER (LOGO & LAFADZ) -->
        <div class="header-section">
            <div class="logo-box">
                <img src="{{ asset(getSetting('app_logo')) }}" alt="Logo Madrasah">
            </div>
            <div class="arabic-image">
                <img src="{{ asset('storage/lafadz.png') }}" alt="Kaligrafi Ayat">
            </div>
        </div>

        <!-- JUDUL & IDENTITAS INSTANSI -->
        <div class="title-section">
            <div class="title-ijazah">IJAZAH</div>
            <div class="title-tingkat">Tingkat {{ strtoupper($data['lulus_dari_tingkat'] ?? 'MADRASAH') }}</div>
            <div class="title-madrasah">MADRASAH DINIYAH TAKMILIYAH HIDAYATUS SHIBYAN</div>
            <div class="title-alamat">SOMORKONENG KWANYAR BANGKALAN</div>
            <div class="title-tahun">Tahun Pelajaran {{ $data['tahun_pelajaran'] ?? '-' }}</div>
        </div>

        <!-- KONTEN INTI -->
        <div class="content-section">
            <p>
                Yang bertanda tangan dibawah ini, Pengasuh Madrasah serta Kepala Bidang Tingkat
                <span class="font-semibold">{{ $data['lulus_dari_tingkat'] ?? 'Madrasah' }}</span> Madrasah Diniyah
                Hidayatus Shibyan Somorkoneng Kwanyar Bangkalan menerangkan bahwa:
            </p>

            <table class="tabel-data">
                <tr>
                    <td class="label-col">Nama</td>
                    <td class="separator-col">:</td>
                    <td class="value-col uppercase">{{ $data['nama_murid'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Tempat, Tanggal Lahir</td>
                    <td class="separator-col">:</td>
                    <td class="value-col">{{ $data['tempat_tgl_lahir'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Wali</td>
                    <td class="separator-col">:</td>
                    <td class="value-col">{{ $data['nama_wali'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Nomor Induk / NISM</td>
                    <td class="separator-col">:</td>
                    <td class="value-col tracking-widest">{{ $data['nism'] ?? '-' }}</td>
                </tr>
            </table>

            <div class="lulus-text">L U L U S</div>

            <p>
                dari tingkat <span class="font-semibold">{{ $data['lulus_dari_tingkat'] ?? 'Madrasah' }}</span>
                berdasarkan hasil Ujian Akhir yang
                diselenggarakan pada akhir Tahun Pelajaran {{ $data['tahun_pelajaran'] ?? '-' }}.
            </p>

            <p class="mt-3">
                Ijazah ini diberikan sebagai dorongan bagi yang bersangkutan agar mengamalkan ilmunya dan bertakwa
                kepada Allah <i>Subhanahu wa Ta'ala</i>.
            </p>

            <!-- TANGGAL CETAK -->
            <div class="text-right mt-6" style="padding-right: 1.5cm;">
                Bangkalan, {{ \Carbon\Carbon::parse($data['tanggal_disahkan'] ?? now())->translatedFormat('d F Y') }}
            </div>
        </div>

        <!-- AREA TANDA TANGAN & FOTO -->
        <div class="footer-section">

            <!-- KIRI: PAS FOTO -->
            <div class="pas-foto-wrapper">
                <div class="pas-foto">
                    Pas Foto<br>3x4
                </div>
            </div>

            <!-- KANAN: PENGASUH -->
            <div class="ttd-box">
                <p>Pengasuh</p>
                <p>Madrasah Diniyah</p>
                <p>Hidayatus Shibyan</p>

                <div class="qr-space">
                    @if (!empty($data['pengasuh_id']))
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate(
                            URL::signedRoute('profil.publik', ['tipe' => 'pengurus', 'id' => $data['pengasuh_id']]),
                        ) !!}
                    @endif
                </div>

                <p class="ttd-nama">{{ $data['pengasuh_nama'] ?? 'Pengasuh' }}</p>
            </div>

        </div>

        <!-- NOMOR IJAZAH (POJOK KIRI BAWAH) -->
        <div class="nomor-bawah">
            No Ijazah : {{ $data['nomor_dokumen'] ?? '-' }}
        </div>

    </div>

</body>

</html>
