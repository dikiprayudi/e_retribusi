<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sts; 
use App\Models\Pegawai;
use App\Models\Penerimaan; 
use App\Models\Pelayanan; // <-- 1. PASTIKAN MODEL INI DI-IMPORT
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StsPrintController extends Controller
{
    public function print(Sts $sts) 
    {
        // 1. Ambil data pegawai (Sama seperti sebelumnya)
        $pegawaiPA = Pegawai::find($sts->pengguna_anggaran_id);
        $pegawaiBP = Pegawai::find($sts->bendahara_id);

        // --- 2. LOGIKA BARU: MENGHITUNG ULANG RINCIAN BERDASARKAN JENIS PELAYANAN ---
        
        // Ambil semua record penerimaan dalam periode STS
        $penerimaans = Penerimaan::whereBetween('tanggal', [$sts->periode_awal, $sts->periode_akhir])->get();
        
        // Kumpulkan semua ID tindakan (pelayanan_id) dari JSON
        $allTindakanIds = [];
        foreach ($penerimaans as $penerimaan) {
            if (is_array($penerimaan->tindakan)) {
                foreach ($penerimaan->tindakan as $tindakan) {
                    if (!empty($tindakan['tindakan_id'])) {
                        $allTindakanIds[] = $tindakan['tindakan_id'];
                    }
                }
            }
        }
        
        // Ambil semua data master pelayanan dalam 1 query (ini efisien)
        $pelayananMap = Pelayanan::whereIn('id', array_unique($allTindakanIds))
                        ->get()
                        ->keyBy('id'); // Buat Peta [id => data_pelayanan]

        // Sekarang, proses dan kelompokkan data
        $rincianData = [];
        foreach ($penerimaans as $penerimaan) {
            if (is_array($penerimaan->tindakan)) {
                foreach ($penerimaan->tindakan as $tindakan) {
                    $tindakanId = $tindakan['tindakan_id'] ?? null;
                    $jumlah = (float) ($tindakan['biaya'] ?? 0); // Ambil total biaya (qty * tarif)

                    // Cari data master pelayanan dari Peta
                    $pelayanan = $pelayananMap->get($tindakanId);

                    if ($pelayanan) {
                        // --- INI PERUBAHAN UTAMA ---
                        // Kita gunakan 'jenis_pelayanan' sebagai Uraian
                        $uraian = $pelayanan->jenis_pelayanan; 
                        // Kita gunakan 'kode_rekening' dari master sebagai Kunci Grup
                        $kode = $pelayanan->kode_rekening; 
                        // --- AKHIR PERUBAHAN ---
                    
                        // Buat grup jika belum ada
                        if (!isset($rincianData[$kode])) {
                            $rincianData[$kode] = [
                                'kode_rekening' => $kode,
                                'uraian' => $uraian,
                                'jumlah' => 0,
                            ];
                        }
                        
                        // Tambahkan jumlah ke grup yang sesuai
                        $rincianData[$kode]['jumlah'] += $jumlah;
                    }
                }
            }
        }
        // --- BATAS LOGIKA BARU ---

        // 3. Siapkan variabel untuk dikirim ke view (Sama seperti sebelumnya)
        $data = [
            'noSts' => $sts->no_sts,
            'bank' => $sts->bank,
            'noRekening' => $sts->no_rekening,
            'rincianData' => $rincianData, // <-- Gunakan rincian baru yang sudah dikelompokkan
            'grandTotal' => $sts->total_disetor, 

            // Data PA
            'paNama' => $pegawaiPA ? $pegawaiPA->nama_pegawai : 'N/A',
            'paNip' => $sts->pengguna_anggaran_nip,
            'paPangkat' => $pegawaiPA ? $pegawaiPA->pangkat_golongan : '',

            // Data BP
            'bpNama' => $pegawaiBP ? $pegawaiBP->nama_pegawai : 'N/A',
            'bpNip' => $sts->bendahara_nip,
            'bpPangkat' => $pegawaiBP ? $pegawaiBP->pangkat_golongan : '',
            
            // Format Tanggal
            'tanggalSetorFormatted' => Carbon::parse($sts->tanggal_setor)->translatedFormat('d F Y'),
            'startDateFormatted' => Carbon::parse($sts->periode_awal)->translatedFormat('d F Y'),
            'endDateFormatted' => Carbon::parse($sts->periode_akhir)->translatedFormat('d F Y'),
        ];
        
        // 4. Kirim ke View (File print.sts.blade.php Anda TIDAK PERLU diubah)
        return view('print.sts', $data);
    }
}