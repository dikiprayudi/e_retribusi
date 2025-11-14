<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penerimaan Non Tunai (BPJS)</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 10px; /* Ukuran font lebih kecil untuk data padat */
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
        }
        .header h2 {
            margin: 0;
            font-size: 14px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid black;
            padding: 4px;
            text-align: left;
        }
        th { text-align: center; background-color: #f2f2f2; }
        .ttd {
            margin-top: 30px;
            width: 100%;
            border: none;
        }
        .ttd td {
            border: none;
            width: 50%;
            text-align: center;
        }
        .ttd-name { text-decoration: underline; font-weight: bold; margin-top: 60px; }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            padding: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN PENERIMAAN NON TUNAI</h1>
        <h2>UPT PUSKESMAS CIKELET</h2> 
        <p>Periode: {{ $periodeString }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No.</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 10%;">No. STS</th>
                <th style="width: 15%;">Kategori</th>
                <th>Uraian</th>
                <th style="width: 10%;">Bank</th>
                <th style="width: 10%;">No. Rekening</th>
                <th style="width: 12%;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dataBpjs as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->no_sts }}</td>
                    <td>{{ $item->kategori->getLabel() }}</td> <td>{{ $item->uraian }}</td>
                    <td>{{ $item->bank }}</td>
                    <td>{{ $item->no_rekening }}</td>
                    <td class="text-right">
                        {{ number_format($item->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="no-data">
                        Tidak ada data untuk rentang tanggal yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        
        @if ($dataBpjs->count() > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">
                    TOTAL PENERIMAAN
                </td>
                <td class="text-right">
                    {{ number_format($totalBpjs, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    <table class="ttd">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala UPT Puskesmas Cikelet
                <div class="ttd-name">{{ $ttdKiri->nama_pegawai ?? '....................' }}</div>
                <div>NIP: {{ $ttdKiri->nip ?? '....................' }}</div>
            </td>
            <td>
                Cikelet, {{ $tanggalSelesaiFormatted }}<br>
                Bendahara Penerimaan Pembantu
                <div class="ttd-name">{{ $ttdKanan->nama_pegawai ?? '....................' }}</div>
                <div>NIP: {{ $ttdKanan->nip ?? '....................' }}</div>
            </td>
        </tr>
    </table>


    <script>
        // Jalankan ini saat semua halaman sudah dimuat
        window.onload = function() {
            
            // 1. Siapkan event 'onafterprint'
            // Ini akan dijalankan SETELAH dialog print tertutup
            // (baik Anda klik "Print" atau "Cancel")
            window.onafterprint = function() {
                // Perintahkan tab ini untuk menutup dirinya sendiri
                window.close();
            };

            // 2. Buka dialog print
            window.print();
        };
    </script>
</body>
</html>