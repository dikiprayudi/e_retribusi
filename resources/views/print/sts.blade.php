<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak STS - {{ htmlspecialchars($noSts) }}</title>
    {{-- CSS Anda --}}
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; }
        .container { width: 18cm; margin: 0 auto; }
        .header-table { width: 100%; border: none; margin-bottom: 5px; }
        .header-table td { vertical-align: middle; }
        .logo { width: 90px; margin-top: 10px;}
        .header-text { text-align: center; }
        .header-text h4, .header-text h5 { margin: 0; line-height: 1.4; }
        .header-text p { margin: 0; font-size: 11pt; }
        .content-table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px;}
        .content-table th, .content-table td { border: 1px solid black; padding: 8px; }
        .content-table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .terbilang { font-style: italic; font-weight: bold; }
        .signatures { margin-top: 20px; overflow: hidden; }
        .signature-box { width: 45%; float: left; text-align: center; }
        .signature-box.right { float: right; }
        .no-print { margin-bottom: 20px; }
        hr.main-line { border: 1px solid black; }
        
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

<div class="container">
    <table class="header-table">
        <tr>
            <td style="width: 20%; text-align: center;">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
            </td>
            <td style="width: 60%;" class="header-text">
                <h4 style="font-size: 14pt;">PEMERINTAH KABUPATEN GARUT</h4>
                <h4 style="font-size: 16pt;">DINAS KESEHATAN</h4>
                <h4 style="font-size: 18pt;">UPT PUSKESMAS CIKELET</h4>
                <p style="font-size: 9pt;">Jalan Raya Cikelet No. Telp (0262) 521508 Kode Pos 44177 Garut – Jawa Barat</p>
                <p style="font-size: 9pt;">e-mail : puskesmascikelet19@gmail.com web : http://pkm-cikelet.garutkab.go.id/</p>
            </td>
            <td style="width: 20%;"></td>
        </tr>
    </table>
    <hr class="main-line">
    <h4 style="text-align: center; margin: 10px 0;">SURAT TANDA SETORAN (STS)</h4>
    
    <table style="margin-top: 20px; width: 70%;">
        <tr><td width="35%">No. STS</td><td>: {{ htmlspecialchars($noSts) }}</td></tr>
        <tr><td>Tanggal</td><td>: {{ $tanggalSetorFormatted }}</td></tr>
        <tr><td>Bank</td><td>: {{ htmlspecialchars($bank) }}</td></tr>
        <tr><td>No. Rekening</td><td>: {{ htmlspecialchars($noRekening) }}</td></tr>
        <tr><td>Penerimaan Tanggal</td><td>: {{ $startDateFormatted . ' s/d ' . $endDateFormatted }}</td></tr>
    </table>

    <p style="margin-top: 20px;">
        Harap diterima uang sebesar <strong>Rp. {{ number_format($grandTotal, 0, ',', '.') }}</strong>,-
        {{-- Pastikan Anda punya helper terbilang() --}}
        <span class="terbilang">(terbilang: {{ terbilang($grandTotal) }} Rupiah)</span>
    </p>

    <p>Dengan Rincian Penerimaan sebagai berikut :</p>

    <table class="content-table">
        <thead style="font-size: 12pt;">
            <tr>
                <th width="5%">No.</th>
                <th>Kode Rekening</th>
                <th>Uraian</th>
                <th width="20%">Jumlah (Rp.)</th>
            </tr>
        </thead>
        <tbody style="font-size: 11pt;">
            @php $no = 1; @endphp
            {{-- Kita loop dari $rincianData (data JSON) --}}
            @forelse ($rincianData as $item)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ htmlspecialchars($item['kode_rekening']) }}</td>
                <td>{{ htmlspecialchars($item['uraian']) }}</td>
                <td class="text-right">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Tidak ada data rincian.</td>
            </tr>
            @endforelse
            
            @for ($i = count($rincianData); $i < 5; $i++)
            <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">JUMLAH TOTAL (Rp.)</th>
                <th class="text-right">{{ number_format($grandTotal, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div class="signature-box left">
            <p>Mengetahui,<br>Kuasa Pengguna Anggaran</p>
            <br><br><br>
            <p>
                <strong><u>{{ htmlspecialchars($paNama) }}</u></strong><br>
                {{ htmlspecialchars($paPangkat) }}<br>
                NIP: {{ htmlspecialchars($paNip) }}
            </p>
        </div>
        <div class="signature-box right">
            <p>Disiapkan oleh,<br>Bendahara Penerimaan Pembantu</p>
            <br><br><br>
            <p>
                <strong><u>{{ htmlspecialchars($bpNama) }}</u></strong><br>
                {{ htmlspecialchars($bpPangkat) }}<br>
                NIP: {{ htmlspecialchars($bpNip) }}
            </p>
        </div>
    </div>
</div>


{{-- TAMBAHKAN SKRIP INI --}}
    <script>
        // Panggil dialog print segera setelah halaman dimuat
        window.onload = function() {
            window.print();
        }

        // Opsional: Otomatis tutup tab ini setelah user selesai print atau cancel
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>
</html>