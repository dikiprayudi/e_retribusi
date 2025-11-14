<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Penerimaan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 8pt;
        }
        .header { text-align: center; line-height: 1.2; font-weight: bold; margin-bottom: 20px;}
        .header h3, .header h4, .header h5 { margin: 0; }
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
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; }
        .col-no { width: 3%; }
        .col-tanggal { width: 7%; }
        .col-rm { width: 6%; }
        .col-pasien { width: 9%; }
        .col-alamat { width: 7%; }
        .col-jk { width: 5%; }
        .col-status { width: 5%; }
        .col-poli { width: 8%; }
        .col-pelayanan { width: 33%; }
        .col-total { width: 10%; }
    </style>
</head>
<body>
    <div class="header">
        <h3>LAPORAN TRANSAKSI PENERIMAAN</h3>
        <h4>Poli Layanan {{ $poli->nama_poli }}</h4>
        <h5>Periode : {{ $tanggalMulaiFormatted }} s/d {{ $tanggalSelesaiFormatted }}</h5>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-tanggal">Tanggal</th>
                <th class="col-rm">No. RM</th>
                <th class="col-pasien">Nama Pasien</th>
                <th class="col-alamat">Alamat</th>
                <th class="col-jk">Jenis Kelamin</th>
                <th class="col-status">Status Pasien</th>
                <th class="col-poli">Poli Tujuan</th>
                <th class="col-pelayanan">Jenis Pelayanan</th>
                <th class="col-total">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penerimaans as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $p->tanggal->format('d/m/Y') }}</td>
                <td class="text-center">{{ $p->no_rm }}</td>
                <td>{{ $p->nama_pasien }}</td>
                <td>{{ $p->alamat }}</td>
                <td class="text-center">{{ $p->jenis_kelamin }}</td>
                <td class="text-center">{{ $p->status_pasien ?? 'UMUM' }}</td>
                <td class="text-center">{{ $p->poli->nama_poli ?? 'N/A' }}</td>
                <td>
                    @if($p->tindakan)
                        <ul style="margin: 0; padding-left: 15px;">
                            @foreach($p->tindakan as $tindakan)
                                <li>{{ $tindakan['nama_pelayanan'] ?? 'N/A' }}</li>
                            @endforeach
                        </ul>
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">{{ number_format($p->total_tarif, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">Tidak ada data untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="9" class="text-center">GRAND TOTAL</td>
                <td class="text-right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="ttd">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala UPT Puskesmas Cikelet
                <div class="ttd-name">({{ $ttdKiri->nama_pegawai ?? '....................' }})</div>
                <div>NIP: {{ $ttdKiri->nip ?? '....................' }}</div>
            </td>
            <td>
                Cikelet, {{ $tanggalCetakFormatted }}<br>
                Bendahara Penerimaan Pembantu
                <div class="ttd-name">({{ $ttdKanan->nama_pegawai ?? '....................' }})</div>
                <div>NIP: {{ $ttdKanan->nip ?? '....................' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>