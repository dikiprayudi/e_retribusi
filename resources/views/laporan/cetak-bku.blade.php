<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Penerimaan dan Penyetoran</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .info {
            font-size: 11px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8px; /* Font di tabel lebih kecil */
        }
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            vertical-align: middle;
        }
        table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
        }
        .ttd {
            width: 100%;
            margin-top: 30px;
        }
        .ttd td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
            border: none;
        }
        .ttd-name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()" onafterprint="window.close()">

    <div class="no-print" style="text-align: right; margin-bottom: 10px;">
        <button onclick="window.print()">Cetak Ulang</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
        <h1>BUKU PENERIMAAN DAN PENYETORAN</h1>
        <p>BENDAHARA PENERIMAAN PEMBANTU</p>
        <p>UPT PUSKESMAS CIKELET</p>
        <p>TAHUN ANGGARAN {{ $tanggalMulai->format('Y') }}</p>
    </div>

    <div class="info">
        Periode: {{ $tanggalMulai->format('d F Y') }} s/d {{ $tanggalSelesai->format('d F Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 2%;">No.</th>
                <th rowspan="2" style="width: 5%;">Tanggal</th>
                <th rowspan="2" style="width: 6%;">Nomor Bukti</th>
                <th rowspan="2" style="width: 13%;">Cara Pembayaran</th>
                <th rowspan="2" style="width: 6%;">Kode Rekening</th>
                <th rowspan="2">Uraian</th>
                <th colspan="2">Penerimaan</th>
                <th colspan="3">Penyetoran</th>
                <th rowspan="2" style="width: 9%;">Saldo</th>
            </tr>
            <tr>
                <th style="width: 9%;">Jumlah</th>
                <th style="width: 9%;">Jumlah Kumulatif</th>
                <th style="width: 7%;">Tanggal</th>
                <th style="width: 9%;">No STS</th>
                <th style="width: 9%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $saldo = 0;
                $totalPenerimaan = 0;
                $totalPenyetoran = 0;
                $kumulatifPenerimaan = 0;
            @endphp
            
            <tr>
                <td colspan="6" style="text-align: left; font-weight: bold;">SALDO AWAL KAS DI BENDAHARA</td>
                <td class="text-right">0,00</td>
                <td class="text-right">0,00</td>
                <td colspan="3"></td>
                <td class="text-right">0,00</td>
            </tr>

            @forelse ($dataBku as $index => $item)
                @php
                    // Lakukan perhitungan di sini
                    $isNonTunai = $item['penyetoran'] > 0;
                    $kumulatifPenerimaan += $item['penerimaan'];
                    $saldo = $saldo + $item['penerimaan'] - $item['penyetoran'];
                    
                    $totalPenerimaan += $item['penerimaan'];
                    $totalPenyetoran += $item['penyetoran'];
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</td>
                    <td>{{ $item['no_bukti'] }}</td>
                    <td>{{ $item['cara_pembayaran'] }}</td>
                    <td>{{ $item['kode_rekening'] }}</td>
                    <td>{{ $item['uraian'] }}</td>
                    <td class="text-right">{{ number_format($item['penerimaan'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($kumulatifPenerimaan, 0, ',', '.') }}</td>
                    
                    <td class="text-center">{{ $isNonTunai ? Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') : '-' }}</td>
                    <td>{{ $isNonTunai ? $item['no_bukti'] : '-' }}</td>
                    <td class="text-right">{{ $isNonTunai ? number_format($item['penyetoran'], 0, ',', '.') : '-' }}</td>

                    <td class="text-right">{{ number_format($saldo, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="no-data">
                        Tidak ada data untuk rentang tanggal yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        
        @if ($dataBku->count() > 0)
            <tr class="total-row">
                <td colspan="6" class="text-center">JUMLAH</td>
                <td class="text-right">{{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                <td></td> <td colspan="2"></td> <td class="text-right">{{ number_format($totalPenyetoran, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
            @endif
    </table>

    <div style="width: 40%; float: left; margin-top: 15px; font-size: 10px;">
        <p style="font-weight: bold; margin-bottom: 5px;">Kas di Bendahara Penerimaan</p>
        <table style="width: 100%; border: none; margin-bottom: 5px;">
            @php
                // Karena ini BKU BPJS (Non Tunai), asumsikan penerimaan adalah Bank
                $saldoAwal = 0;
                $totalTunai = $saldo;
                $totalBank = 0; // Total Penerimaan = Total Bank
            @endphp
            
            <tr style="border: none;">
                <td style="border: none; width: 35%;">Saldo Awal</td>
                <td style="border: none; width: 5%;">:</td>
                <td style="border: none; text-align: right;">{{ number_format($saldoAwal, 2, ',', '.') }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;">Jumlah Penerimaan</td>
                <td style="border: none;">:</td>
                <td style="border: none; text-align: right;">{{ number_format($totalPenerimaan, 2, ',', '.') }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;">Jumlah yang disetorkan</td>
                <td style="border: none;">:</td>
                <td style="border: none; text-align: right;">{{ number_format($totalPenyetoran, 2, ',', '.') }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; font-weight: bold;">Saldo Akhir</td>
                <td style="border: none; font-weight: bold;">:</td>
                <td style="border: none; text-align: right; font-weight: bold;">{{ number_format($saldo, 2, ',', '.') }}</td>
            </tr>
            <tr style="border: none;">
                <td colspan="3" style="border: none; padding-top: 10px;">terdiri atas</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;">Tunai</td>
                <td style="border: none;">:</td>
                <td style="border: none; text-align: right;">{{ number_format($totalTunai, 2, ',', '.') }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;">Bank</td>
                <td style="border: none;">:</td>
                <td style="border: none; text-align: right;">{{ number_format($totalBank, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <table class="ttd">
        <tr>
            <td>
                Mengetahui,<br>
                Kuasa Pengguna Anggaran
                <div class="ttd-name">{{ $ttdKiri->nama_pegawai ?? '....................' }}</div>
                <div>NIP: {{ $ttdKiri->nip ?? '....................' }}</div>
            </td>
            <td>
                Cikelet, {{ $tanggalSelesaiFormatted }}<br>
                Bendahara Penerimaan
                <div class="ttd-name">{{ $ttdKanan->nama_pegawai ?? '....................' }}</div>
                <div>NIP: {{ $ttdKanan->nip ?? '....................' }}</div>
            </td>
        </tr>
    </table>

</body>
</html>