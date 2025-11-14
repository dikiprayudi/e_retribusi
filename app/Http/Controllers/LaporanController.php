<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penerimaan;
use App\Models\Poli;
use App\Models\Pegawai;
use App\Models\Pelayanan;
use App\Models\Sts; 
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LaporanController extends Controller
{
    /**
     * Method 1: Membuat Laporan Rekapitulasi Harian (Pivot)
     * (LOGIKA BARU - GROUP BY POLI)
     */
    public function rekapHarianPenerimaan(Request $request)
    {
        // 1. Ambil data dari URL
        // --- PERUBAHAN: Menghapus fallback now() ---
        // (Diasumsikan 'bulan' selalu dikirim dari form Filament)
        $tanggalPilihan = Carbon::parse($request->input('bulan'));
        $ttdKiriId = $request->input('ttd_kiri');
        $ttdKananId = $request->input('ttd_kanan');

        // 2. Ambil data mentah
        $ttdKiri = Pegawai::find($ttdKiriId);
        $ttdKanan = Pegawai::find($ttdKananId);
        
        // 3. Tentukan Kolom (DINAMIS DARI TABEL POLI)
        $kolomPoli = Poli::orderBy('nama_poli')->get();
        $kolomPoliIds = $kolomPoli->pluck('id')->all();

        // 4. Ambil semua penerimaan di bulan ini
        $penerimaans = Penerimaan::whereYear('tanggal', $tanggalPilihan->year)
            ->whereMonth('tanggal', $tanggalPilihan->month)
            ->orderBy('tanggal', 'asc')
            ->get();

        // 5. Proses Pivot Data (Logika Baru)
        $rekapData = [];
        $totalPerKolom = array_fill_keys($kolomPoliIds, 0); 
        $totalKeseluruhan = 0;
        $hariDalamBulan = $tanggalPilihan->daysInMonth;

        for ($hari = 1; $hari <= $hariDalamBulan; $hari++) {
            $tanggalSekarang = $tanggalPilihan->copy()->day($hari);
            
            // Filter penerimaan untuk hari ini
            $penerimaanHariIni = $penerimaans->where(function ($item) use ($tanggalSekarang) {
                return Carbon::parse($item->tanggal)->isSameDay($tanggalSekarang);
            });
            
            $dataHari = [
                'tanggal' => $tanggalSekarang,
                'total_harian' => 0,
                'poli' => array_fill_keys($kolomPoliIds, 0) // Gunakan 'poli' sebagai key
            ];

            foreach ($penerimaanHariIni as $penerimaan) {
                $poliId = $penerimaan->poli_id;
                $totalPenerimaan = (float)($penerimaan->total_tarif ?? 0); 

                if ($poliId && isset($dataHari['poli'][$poliId])) {
                    $dataHari['poli'][$poliId] += $totalPenerimaan;
                    $totalPerKolom[$poliId] += $totalPenerimaan;
                }
            }
            
            $dataHari['total_harian'] = array_sum($dataHari['poli']);
            $totalKeseluruhan += $dataHari['total_harian'];
            $rekapData[$hari] = $dataHari;
        }

        // --- PERUBAHAN: Tambahkan format tanggal untuk view ---
        $bulanFormatted = $tanggalPilihan->translatedFormat('F Y'); // Cth: "November 2025"
        // Buat tanggal cetak TTD (akhir bulan)
        $tanggalCetakFormatted = $tanggalPilihan->copy()->endOfMonth()->translatedFormat('d F Y');
        // --- BATAS PERUBAHAN ---

        // 6. Kirim data ke View
        $dataView = [
            'rekapData' => $rekapData, 
            'kolomPoli' => $kolomPoli, 
            'totalPerKolom' => $totalPerKolom, 
            'totalKeseluruhan' => $totalKeseluruhan, 
            'poli' => (object)['nama_poli' => 'PUSKESMAS CIKELET'], // Judul UPT
            'bulan' => $tanggalPilihan, // Objek Carbon asli
            'bulanFormatted' => $bulanFormatted, // String "November 2025"
            'ttdKiri' => $ttdKiri, 
            'ttdKanan' => $ttdKanan, 
            'tanggalCetakFormatted' => $tanggalCetakFormatted, // Tanggal TTD
        ];
        
        // 7. Buat PDF (F4 Landscape)
        $ukuranKertasF4 = [0, 0, 595.27, 935.43];
        $pdf = Pdf::loadView('print.rekap-harian-penerimaan', $dataView)
                    ->setPaper($ukuranKertasF4, 'landscape'); 

        return $pdf->stream('rekap-harian-' . $tanggalPilihan->format('F-Y') . '.pdf');
    }


    public function laporanTransaksi(Request $request)
    {
        // 1. Ambil data dari URL
        $tanggalMulai = Carbon::parse($request->input('tanggal_mulai'));
        $tanggalSelesai = Carbon::parse($request->input('tanggal_selesai'));
        $poliId = $request->input('poli_id');
        $ttdKiriId = $request->input('ttd_kiri');
        $ttdKananId = $request->input('ttd_kanan');
        
        // --- PERUBAHAN: Hapus $tanggalCetak, gunakan $tanggalSelesai ---
        $tanggalCetak = $tanggalSelesai; // Tanggal TTD = Tanggal Akhir Periode

        // 2. Ambil data mentah
        $ttdKiri = Pegawai::find($ttdKiriId);
        $ttdKanan = Pegawai::find($ttdKananId);

        // 3. Buat query dasar
        $penerimaanQuery = Penerimaan::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
        $poli = null;

        if ($poliId == 'semua') {
            $poli = (object)['nama_poli' => 'Puskesmas'];
        } else {
            $penerimaanQuery->where('poli_id', $poliId);
            $poli = Poli::find($poliId);
        }

        // 4. Ambil data (termasuk relasi 'poli' untuk ditampilkan)
        $penerimaans = $penerimaanQuery->with('poli')->orderBy('tanggal', 'asc')->get();

        // 5. Hitung Grand Total
        $grandTotal = $penerimaans->sum('total_tarif');

        // --- PERUBAHAN: Tambahkan format tanggal untuk view ---
        $tanggalMulaiFormatted = $tanggalMulai->translatedFormat('d F Y');
        $tanggalSelesaiFormatted = $tanggalSelesai->translatedFormat('d F Y');
        $tanggalCetakFormatted = $tanggalCetak->translatedFormat('d F Y');
        // --- BATAS PERUBAHAN ---

        // 6. Kirim data ke View
        $dataView = [
            'penerimaans' => $penerimaans, 
            'poli' => $poli,
            'tanggalMulai' => $tanggalMulai, // Objek Carbon asli
            'tanggalSelesai' => $tanggalSelesai, // Objek Carbon asli
            'grandTotal' => $grandTotal,
            'ttdKiri' => $ttdKiri,
            'ttdKanan' => $ttdKanan,
            // Variabel baru yang sudah diformat
            'tanggalMulaiFormatted' => $tanggalMulaiFormatted,
            'tanggalSelesaiFormatted' => $tanggalSelesaiFormatted,
            'tanggalCetakFormatted' => $tanggalCetakFormatted, // Tanggal TTD
        ];

        // 7. Buat PDF (Gunakan F4 Landscape)
        $ukuranKertasF4 = [0, 0, 595.27, 935.43];
        $pdf = Pdf::loadView('print.laporan-transaksi', $dataView)
                    ->setPaper($ukuranKertasF4, 'landscape'); 

        return $pdf->stream('laporan-transaksi.pdf');
    }

    public function printRegisterSts(Request $request)
    {
        // 1. Ambil data dari URL
        $tanggalMulai = Carbon::parse($request->input('tanggal_mulai'));
        $tanggalSelesai = Carbon::parse($request->input('tanggal_selesai'));
        $ttdKiriId = $request->input('ttd_kiri');
        $ttdKananId = $request->input('ttd_kanan');
        
        // --- PERUBAHAN: Tambahkan tanggal TTD ---
        $tanggalCetak = $tanggalSelesai; // Tanggal TTD = Tanggal Akhir Periode

        // 2. Ambil data Penandatangan
        $ttdKiri = Pegawai::find($ttdKiriId);
        $ttdKanan = Pegawai::find($ttdKananId);

        // 3. Ambil data STS dalam periode yang dipilih
        $stsRecords = Sts::whereBetween('tanggal_setor', [$tanggalMulai, $tanggalSelesai])
                            ->orderBy('tanggal_setor', 'asc')
                            ->get();
        
        // 4. Hitung Saldo (Running Total)
        $saldo = 0;
        $stsRecordsWithSaldo = $stsRecords->map(function($sts) use (&$saldo) {
            $saldo += $sts->total_disetor;
            $sts->saldo = $saldo; 
            return $sts;
        });

        // --- PERUBAHAN: Tambahkan format tanggal untuk view ---
        $tanggalMulaiFormatted = $tanggalMulai->translatedFormat('d F Y');
        $tanggalSelesaiFormatted = $tanggalSelesai->translatedFormat('d F Y');
        $tanggalCetakFormatted = $tanggalCetak->translatedFormat('d F Y');
        // --- BATAS PERUBAHAN ---

        // 5. Kirim data ke View
        $dataView = [
            'stsRecords' => $stsRecordsWithSaldo, 
            'tanggalMulai' => $tanggalMulai, // Objek Carbon asli
            'tanggalSelesai' => $tanggalSelesai, // Objek Carbon asli
            'ttdKiri' => $ttdKiri,
            'ttdKanan' => $ttdKanan,
            // Variabel baru yang sudah diformat
            'tanggalMulaiFormatted' => $tanggalMulaiFormatted,
            'tanggalSelesaiFormatted' => $tanggalSelesaiFormatted,
            'tanggalCetakFormatted' => $tanggalCetakFormatted, // Tanggal TTD
        ];

        // 6. Buat PDF (F4 Portrait)
        $ukuranKertasF4 = [0, 0, 595.27, 935.43];
        $pdf = Pdf::loadView('print.register-sts', $dataView)
                    ->setPaper($ukuranKertasF4, 'portrait'); 

        return $pdf->stream('register-sts-'. $tanggalMulai->format('Y') .'.pdf');
    }
}