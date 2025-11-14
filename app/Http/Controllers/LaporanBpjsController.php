<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bpjs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Pegawai; // Pastikan ini ada

class LaporanBpjsController extends Controller
{
    public function cetak(Request $request)
    {
        // --- PERBAIKAN DI BLOK VALIDASI INI ---
        $request->validate([
            'jenis_laporan' => 'required|in:bulanan,tahunan,harian',
            'bulan' => 'required_if:jenis_laporan,bulanan|date',
            'tahun' => 'required_if:jenis_laporan,tahunan|date',
            'tanggal_mulai' => 'required_if:jenis_laporan,harian|date',
            'tanggal_selesai' => 'required_if:jenis_laporan,harian|date|after_or_equal:tanggal_mulai',
            
            // Cukup validasi 'required'. Kita hapus 'exists:...'
            'ttd_kiri_id' => 'required', 
            'ttd_kanan_id' => 'required',
        ]);
        // --- BATAS PERBAIKAN ---

        $jenis = $request->jenis_laporan;
        $periodeString = '';

        // Logika IF/ELSEIF $jenis (Ini sudah benar)
        if ($jenis === 'bulanan') {
            $tanggalMulai = Carbon::parse($request->bulan)->startOfMonth();
            $tanggalSelesai = Carbon::parse($request->bulan)->endOfMonth();
            $periodeString = $tanggalMulai->format('F Y');
        } 
        elseif ($jenis === 'tahunan') {
            $tanggalMulai = Carbon::parse($request->tahun)->startOfYear();
            $tanggalSelesai = Carbon::parse($request->tahun)->endOfYear();
            $periodeString = 'Tahun ' . $tanggalMulai->format('Y');
        } 
        elseif ($jenis === 'harian') {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
            $periodeString = $tanggalMulai->format('d F Y') . ' s/d ' . $tanggalSelesai->format('d F Y');
        }

        // Logika pengambilan data (Ini sudah benar)
        $dataBpjs = Bpjs::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
                             ->orderBy('tanggal', 'asc')
                             ->get();
        $totalBpjs = $dataBpjs->sum('jumlah');

        // Logika TTD (Ini sudah benar)
        // Kita gunakan $request->ttd_kiri_id dan $request->ttd_kanan_id
        $ttdKiri = Pegawai::find($request->ttd_kiri_id);
        $ttdKanan = Pegawai::find($request->ttd_kanan_id);
        $tanggalSelesaiFormatted = $tanggalSelesai->translatedFormat('d F Y');


        // Kirim ke view (Ini sudah benar)
        return view('laporan.cetak-bpjs', compact(
            'dataBpjs',
            'totalBpjs',
            'periodeString',
            'ttdKiri',
            'ttdKanan',
            'tanggalSelesaiFormatted'
        ));
    }
}