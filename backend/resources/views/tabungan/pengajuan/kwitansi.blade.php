<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Penarikan Tabungan - {{ $pengajuan->kode_pengajuan }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 10mm 15mm 10mm 15mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #ffffff;
            margin: 0;
            padding: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #0f766e;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 14px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f766e;
        }

        .header h2 {
            font-size: 12px;
            margin: 2px 0 0 0;
            font-weight: 700;
        }

        .header p {
            font-size: 9px;
            margin: 1px 0 0 0;
            color: #6b7280;
        }

        .kwitansi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 15px;
        }

        .kwitansi-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .kwitansi-table td.label {
            width: 140px;
            color: #4b5563;
            font-weight: 600;
        }

        .kwitansi-table td.val {
            font-weight: 700;
            color: #111827;
        }

        .highlight-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 8px 12px;
            border-radius: 6px;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .highlight-box .nominal {
            font-size: 15px;
            font-weight: 800;
            color: #15803d;
            font-family: monospace;
        }

        .footer-sign {
            margin-top: 15px;
            display: table;
            width: 100%;
            page-break-inside: avoid;
        }

        .sign-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            font-size: 10px;
        }

        .sign-space {
            height: 45px;
        }

        .font-bold {
            font-weight: 700;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="margin-bottom: 10px; text-align: right;">
        <button onclick="window.print()"
            style="background: #0f766e; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 10px;">
            🖨️ Cetak Kwitansi
        </button>
    </div>

    <!-- HEADER -->
    <div class="header">
        <h1>Madrasah Diniyah Takmiliyah Hidayatus Shibyan</h1>
        <h2>KWITANSI BUKTI PENARIKAN DANA TABUNGAN</h2>
        <p>Somorkoneng, Blega, Bangkalan, Jawa Timur &bull; No: <strong>{{ $pengajuan->kode_pengajuan }}</strong></p>
    </div>

    @php
        $tab = $pengajuan->tabungan;
    @endphp

    <table class="kwitansi-table">
        <tr>
            <td class="label">Telah Diserahkan Kepada</td>
            <td class="val">: {{ $tab->nama_nasabah }} (No. Rek: {{ $tab->nomor_rekening }})</td>
        </tr>
        <tr>
            <td class="label">Kategori Nasabah</td>
            <td class="val">: {{ $tab->jenis_nasabah }} &bull; {{ $tab->nama_rekening }}</td>
        </tr>
        <tr>
            <td class="label">Nominal Penarikan Kotor</td>
            <td class="val">: Rp {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Potongan Tabungan ({{ $pengajuan->persentase_potongan }}%)</td>
            <td class="val" style="color: #dc2626;">: Rp
                {{ number_format($pengajuan->nominal_potongan, 0, ',', '.') }} (Infaq/Operasional)</td>
        </tr>
        <tr>
            <td class="label">Uang Bersih Diserahkan</td>
            <td class="val">
                <div class="highlight-box">
                    <span class="nominal">Rp
                        {{ number_format($pengajuan->nominal_bersih_diterima, 0, ',', '.') }}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td class="label">Untuk Keperluan / Alasan</td>
            <td class="val">: {{ $pengajuan->alasan_penarikan ?? 'Penarikan Tabungan Madrasah' }}</td>
        </tr>
        <tr>
            <td class="label">Status & Tanggal Cair</td>
            <td class="val">: <strong>{{ $pengajuan->status }}</strong>
                ({{ $pengajuan->updated_at->format('d/m/Y H:i') }})</td>
        </tr>
    </table>

    <!-- TANDA TANGAN -->
    <div class="footer-sign">
        <div class="sign-col">
            <div>Penerima / Nasabah,</div>
            <div class="sign-space"></div>
            <div class="font-bold"><u>{{ $tab->nama_nasabah }}</u></div>
        </div>
        <div class="sign-col">
            <div>Somorkoneng, {{ date('d F Y') }}<br>Bendahara Madrasah,</div>
            <div class="sign-space"></div>
            <div class="font-bold">
                <u>{{ $pengajuan->verifikator?->name ?? (Auth::user()->name ?? 'Pengurus Tabungan') }}</u></div>
        </div>
    </div>

</body>

</html>
