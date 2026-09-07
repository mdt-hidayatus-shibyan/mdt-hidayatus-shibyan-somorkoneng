<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembagian Tabungan Murid - {{ $simulasi['periode']->nama_periode }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #ffffff;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 15px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f766e;
        }

        .header h2 {
            font-size: 13px;
            margin: 3px 0 0 0;
            font-weight: 700;
        }

        .header p {
            font-size: 10px;
            margin: 2px 0 0 0;
            color: #6b7280;
        }

        .info-grid {
            margin-bottom: 15px;
            font-size: 11px;
        }

        .info-grid table td {
            padding: 2px 4px;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 20px;
        }

        table.report th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            border: 1px solid #d1d5db;
            padding: 6px 4px;
            text-align: center;
        }

        table.report td {
            border: 1px solid #e5e7eb;
            padding: 5px 4px;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-mono {
            font-family: monospace;
        }

        .font-bold {
            font-weight: 700;
        }

        .footer-sign {
            margin-top: 30px;
            display: table;
            width: 100%;
            page-break-inside: avoid;
        }

        .sign-col {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .sign-space {
            height: 55px;
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

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()"
            style="background: #0f766e; color: white; border: none; padding: 7px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 11px;">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <!-- KOP MADRASAH -->
    <div class="header">
        <h1>Madrasah Diniyah Takmiliyah Hidayatus Shibyan</h1>
        <h2>BERITA ACARA & REKAP LAPORAN PEMBAGIAN TABUNGAN MURID</h2>
        <p>Somorkoneng, Blega, Bangkalan, Jawa Timur &bull; Dicetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="info-grid">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 130px; font-weight: 600; color: #4b5563;">Periode Program</td>
                <td style="font-weight: 700;">: {{ $simulasi['periode']->nama_periode }}</td>
                <td style="width: 130px; font-weight: 600; color: #4b5563;">Kelas / Ruangan</td>
                <td style="font-weight: 700;">:
                    {{ $ruangan ? $ruangan->nama_ruangan : 'Semua Ruangan / Seluruh Murid' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color: #4b5563;">Tanggal Pembagian</td>
                <td style="font-weight: 700;">:
                    {{ \Carbon\Carbon::parse($simulasi['periode']->tanggal_pembagian)->format('d F Y') }}</td>
                <td style="font-weight: 600; color: #4b5563;">Ketentuan Potongan</td>
                <td style="font-weight: 700; color: #dc2626;">: {{ $simulasi['persentase_potongan'] }}% (Infaq Madrasah)
                </td>
            </tr>
        </table>
    </div>

    <!-- TABEL REKAP -->
    <table class="report">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">NISM</th>
                <th>Nama Murid</th>
                <th style="width: 70px;">Kelas</th>
                <th style="width: 80px;">No. Rekening</th>
                <th style="width: 80px;">Tabungan Kotor</th>
                <th style="width: 75px;">Potongan ({{ $simulasi['persentase_potongan'] }}%)</th>
                <th style="width: 85px;">Bersih Diterima</th>
                <th style="width: 90px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($simulasi['rincian'] as $r)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center font-mono">{{ $r['murid']->nism }}</td>
                    <td class="font-bold">{{ $r['murid']->nama_lengkap }}</td>
                    <td class="text-center">{{ $r['ruangan']?->nama_ruangan ?? '-' }}</td>
                    <td class="text-center font-mono">{{ $r['tabungan']->nomor_rekening }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($r['saldo_kotor'], 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #dc2626;">Rp
                        {{ number_format($r['potongan'], 0, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold" style="background-color: #f0fdf4;">Rp
                        {{ number_format($r['saldo_bersih'], 0, ',', '.') }}</td>
                    <td style="font-size: 8px; color: #9ca3af; padding-left: 6px;">{{ $loop->iteration }}.
                        .................</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px; color: #9ca3af;">Tidak ada data murid
                        yang memiliki saldo pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f3f4f6; font-weight: bold;">
                <td colspan="5" class="text-right" style="padding: 6px;">TOTAL KESELURUHAN
                    ({{ $simulasi['total_rekening'] }} Murid):</td>
                <td class="text-right font-mono" style="padding: 6px;">Rp
                    {{ number_format($simulasi['total_saldo_kotor'], 0, ',', '.') }}</td>
                <td class="text-right font-mono" style="padding: 6px; color: #dc2626;">Rp
                    {{ number_format($simulasi['total_potongan'], 0, ',', '.') }}</td>
                <td class="text-right font-mono"
                    style="padding: 6px; font-size: 11px; color: #0f766e; background-color: #dcfce7;">Rp
                    {{ number_format($simulasi['total_saldo_bersih'], 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- TANDA TANGAN -->
    <div class="footer-sign">
        <div class="sign-col">
            <div>Mengetahui,<br>Kepala Madrasah Diniyah,</div>
            <div class="sign-space"></div>
            <div class="font-bold"><u>Pengurus MDT Hidayatus Shibyan</u></div>
        </div>
        <div class="sign-col">
            <div>Somorkoneng, {{ date('d F Y') }}<br>Bendahara Madrasah,</div>
            <div class="sign-space"></div>
            <div class="font-bold"><u>{{ Auth::user()->name ?? 'Bendahara Tabungan' }}</u></div>
        </div>
    </div>

</body>

</html>
