<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Penerimaan Retribusi {{ $poli->nama_poli ?? 'UPT' }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 7pt; }
        .header { text-align: center; line-height: 1.2; font-weight: bold; }
        .header h3, .header h4 { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid black; padding: 4px; text-align: center; }
        th { background-color: #f2f2f2; }
        .ttd { margin-top: 20px; width: 100%; border: none; }
        .ttd td { border: none; width: 50%; text-align: center; }
        .ttd-name { text-decoration: underline; font-weight: bold; margin-top: 50px; }
        .col-no { width: 3%; }
        .col-tanggal { width: 10%; }
        .col-jenis { width: 17%; } 
        .col-total { width: 10%; }
        .text-left { text-align: left; padding-left: 5px; }
        .text-right { text-align: right; padding-right: 5px; }
        .total-row { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h3>REKAPITULASI PENERIMAAN RETRIBUSI PASIEN UMUM</h3>
        <h4>{{ $poli->nama_poli ?? 'N/A' }}</h4>
        <h4>Bulan : {{ $bulanFormatted }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="col-no">NO</th>
                <th rowspan="2" class="col-tanggal">TANGGAL BULAN PELAYANAN</th>
                <th colspan="{{ $kolomPoli->count() }}">POLI PELAYANAN</th>
                <th rowspan="2" class="col-total">TOTAL</th>
            </tr>
            <tr>
                @foreach($kolomPoli as $poli)
                <th class="col-jenis">{{ $poli->nama_poli }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rekapData as $hari => $data)
            <tr>
                <td>{{ $hari }}</td>
                <td class="text-center">{{ $data['tanggal']->format('d/m/Y') }}</td>
                
                @foreach($kolomPoli as $poli)
                <td class="text-right">
                    {{ $data['poli'][$poli->id] > 0 ? number_format($data['poli'][$poli->id], 0, ',', '.') : '' }}
                </td>
                @endforeach
                
                <td class="text-right total-row">{{ $data['total_harian'] > 0 ? number_format($data['total_harian'], 0, ',', '.') : '' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2">JUMLAH</td>
                @foreach($kolomPoli as $poli)
                <td class="text-right">
                    {{ number_format($totalPerKolom[$poli->id], 0, ',', '.') }}
                </td>
                @endforeach
                <td class="text-right">{{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
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