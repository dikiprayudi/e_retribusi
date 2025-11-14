<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Penerimaan; // Model Tunai
use App\Models\Bpjs;       // Model Non-Tunai
use App\Models\Pegawai;
use Carbon\Carbon;
use App\Enums\KategoriBpjs;
use App\Models\Sts;

class LaporanBkuController extends Controller
{
    public function cetak(Request $request)
    {
        // 1. Validasi (Ini sudah benar)
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'ttd_kiri_id' => 'required',
            'ttd_kanan_id' => 'required',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);

        // --- LOGIKA BARU BERDASARKAN PDF ---

        // 2. Ambil data Tunai (Penerimaan)
        // Ini HANYA masuk ke kolom Penerimaan (Debet)
        $penerimaanTunai = Penerimaan::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->tanggal,
                    'no_bukti' => 'TUNAI-' . $item->id, // Buat No Bukti placeholder
                    // --- PERUBAHAN ---
                    'cara_pembayaran' => 'Penerimaan Tunai-Tanpa Penetapan',
                    'kode_rekening' => '4.1.02.01.01.0001', // Asumsi dari PDF
                    'uraian' => $item->keterangan ?? 'Retribusi Pelayanan Kesehatan di Puskesmas',
                    'penerimaan' => $item->total_tarif,
                    'penyetoran' => 0, // Tunai BELUM disetor di baris ini
                ];
            });

        // 3. Ambil data Non-Tunai (Bpjs)
        // Ini masuk ke Penerimaan (Debet) DAN Penyetoran (Kredit)
        // karena ini adalah transaksi non-tunai (Rek. BUD)
        $penerimaanNonTunai = Bpjs::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->get()
            ->map(function ($item) {
                // --- LOGIKA BARU KODE REKENING ---
                $kodeRekening = match ($item->kategori) {
                    KategoriBpjs::BPJS_KAPITASI => '4.1.04.16.01.0002',
                    KategoriBpjs::BPJS_NON_KAPITASI => '4.1.04.16.01.0003',
                    KategoriBpjs::JASA_GIRO => '4.1.04.16.01.0012',
                    // Ganti '...' dengan kode rekening yang benar jika ada
                    KategoriBpjs::LAPAD_RUHAMA => '4.1.04.16.01.0003', 
                    KategoriBpjs::LAIN_LAIN => '4.1.04.16.01.0003',
                };
                return [
                    'tanggal' => $item->tanggal,
                    'no_bukti' => $item->no_sts ?? 'NON-TUNAI',
                    'cara_pembayaran' => 'Penerimaan Rek. BLUD',
                    'kode_rekening' => $kodeRekening,
                    'uraian' => $item->uraian ?? $item->kategori->getLabel(),
                    'penerimaan' => $item->jumlah, // Masuk ke Debet
                    'penyetoran' => $item->jumlah, // JUGA Masuk ke Kredit
                ];
            });
        
        // --- 4. LOGIKA BARU: DISESUAIKAN DENGAN MODEL STS ANDA ---
        $penyetoranTunai = Sts::whereBetween('tanggal_setor', [$tanggalMulai, $tanggalSelesai]) // <-- DIUBAH
            ->get()
            ->map(function ($item) {
                // $item adalah model Sts
                return [
                    'tanggal' => $item->tanggal_setor, // <-- DIUBAH
                    'no_bukti' => $item->no_sts,
                    'cara_pembayaran' => '', 
                    'kode_rekening' => '', 
                    'uraian' => 'Penyetoran Tunai',
                    'penerimaan' => 0, 
                    'penyetoran' => $item->total_disetor, // <-- DIUBAH
                ];
            });
        
        // 5. Gabungkan TIGA sumber data dan Urutkan
        $dataBku = $penerimaanTunai
            ->merge($penerimaanNonTunai)
            ->merge($penyetoranTunai) // <-- Gabungkan data Sts
            ->sortBy('tanggal');

        // 5. Ambil data TTD (Ini sudah benar)
        $ttdKiri = Pegawai::find($request->ttd_kiri_id);
        $ttdKanan = Pegawai::find($request->ttd_kanan_id);
        $tanggalSelesaiFormatted = $tanggalSelesai->translatedFormat('d F Y');

        // 6. Kirim ke View (Ini sudah benar)
        return view('laporan.cetak-bku', compact(
            'dataBku',
            'tanggalMulai',
            'tanggalSelesai',
            'ttdKiri',
            'ttdKanan',
            'tanggalSelesaiFormatted'
        ));
    }
}