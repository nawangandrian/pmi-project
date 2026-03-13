<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PendonorModel;
use App\Models\HistorisDonorModel;
use App\Models\ModelPrediksiModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $pendonorModel      = new PendonorModel();
        $historisDonorModel = new HistorisDonorModel();
        $modelPrediksiModel = new ModelPrediksiModel();

        $db = \Config\Database::connect();

        // ── Statistik utama ──────────────────────────────────────
        $totalPendonor = $pendonorModel->countAll();
        $totalHistoris = $historisDonorModel->countAll();
        $totalPrediksi = $db->table('histori_prediksi')->countAll();
        $modelAktif    = $modelPrediksiModel->getAktif();

        // ── Pendonor baru bulan ini ──────────────────────────────
        $bulanIni = date('Y-m');
        $pendonorBulanIni = $db->table('data_historis_donor')
            ->where("DATE_FORMAT(tanggal_donor, '%Y-%m')", $bulanIni)
            ->where('baru_ulang', 'baru')
            ->countAllResults();

        $donorBulanIni = $db->table('data_historis_donor')
            ->where("DATE_FORMAT(tanggal_donor, '%Y-%m')", $bulanIni)
            ->countAllResults();

        // ── Donor Baru vs Ulang ────────────────────────────────
        $donorBaru = $db->table('data_historis_donor')
            ->where('baru_ulang', 'baru')
            ->countAllResults();

        $donorUlang = $db->table('data_historis_donor')
            ->where('baru_ulang', 'ulang')
            ->countAllResults();

        $baruPct = $totalHistoris > 0 ? round($donorBaru / $totalHistoris * 100, 1) : 0;

        // ── Distribusi golongan darah ────────────────────────────
        $golonganDarah = $db->query("
            SELECT golongan_darah, COUNT(*) as jumlah
            FROM data_pendonor
            WHERE golongan_darah IS NOT NULL AND golongan_darah != ''
            GROUP BY golongan_darah ORDER BY jumlah DESC
        ")->getResultArray();

        // ── Ambil tanggal donor terbaru ──────────────────────────
        $lastDate = $db->table('data_historis_donor')
            ->selectMax('tanggal_donor')
            ->get()
            ->getRow()
            ->tanggal_donor;

        // Jika tidak ada data sama sekali
        if (!$lastDate) {
            $trenDonor = [];
        } else {

            // ── Tren donor 6 bulan terakhir berdasarkan data ──────
            $trenDonor = [];

            for ($i = 5; $i >= 0; $i--) {
                $bulanKey = date('Y-m', strtotime($lastDate . " -$i months"));
                $bulanLbl = date('M Y', strtotime($lastDate . " -$i months"));

                $jumlah = $db->table('data_historis_donor')
                    ->where("DATE_FORMAT(tanggal_donor,'%Y-%m')", $bulanKey)
                    ->countAllResults();

                $trenDonor[] = [
                    'bulan' => $bulanKey,
                    'label' => $bulanLbl,
                    'total' => (int)$jumlah
                ];
            }
        }

        // ── Top kecamatan ────────────────────────────────────────
        $topKecamatan = $db->query("
            SELECT kecamatan, COUNT(*) as jumlah
            FROM data_pendonor
            WHERE kecamatan IS NOT NULL AND kecamatan != ''
            GROUP BY kecamatan
            ORDER BY kecamatan ASC
        ")->getResultArray();

        // ── Baru vs Ulang ────────────────────────────────────────
        $baruUlang = $db->query("
            SELECT baru_ulang, COUNT(*) as jumlah
            FROM data_historis_donor GROUP BY baru_ulang
        ")->getResultArray();

        // ── Pengesahan ───────────────────────────────────────────
        $pengesahan = $db->query("
            SELECT status_pengesahan, COUNT(*) as jumlah
            FROM data_historis_donor GROUP BY status_pengesahan
        ")->getResultArray();

        // ── Prediksi terbaru ─────────────────────────────────────
        $prediksiTerbaru = $db->query("
            SELECT hp.tanggal_prediksi, hp.jumlah_hasil, mp.nama_model,
                   u.username, hp.id_histori_prediksi
            FROM histori_prediksi hp
            LEFT JOIN model_prediksi mp ON mp.id_model = hp.id_model
            LEFT JOIN users u ON u.id_user = hp.id_user
            ORDER BY hp.tanggal_prediksi DESC LIMIT 5
        ")->getResultArray();

        // ── Distribusi usia ──────────────────────────────────────
        $distribusiUsia = $db->query("
            SELECT
                CASE
                    WHEN umur BETWEEN 17 AND 25 THEN '17–25'
                    WHEN umur BETWEEN 26 AND 35 THEN '26–35'
                    WHEN umur BETWEEN 36 AND 45 THEN '36–45'
                    WHEN umur BETWEEN 46 AND 55 THEN '46–55'
                    ELSE '56+'
                END as kelompok,
                COUNT(*) as jumlah
            FROM data_pendonor
            WHERE umur IS NOT NULL AND umur > 0
            GROUP BY kelompok ORDER BY MIN(umur)
        ")->getResultArray();

        // ── Jenis kelamin ────────────────────────────────────────
        $jenisKelamin = $db->query("
            SELECT jenis_kelamin, COUNT(*) as jumlah
            FROM data_pendonor GROUP BY jenis_kelamin
        ")->getResultArray();

        return view('pages/dashboard/index', [
            'title'            => 'Dashboard | SP3 (Sistem Prediksi Pendonor Potensial)',
            'totalPendonor'    => $totalPendonor,
            'totalHistoris'    => $totalHistoris,
            'totalPrediksi'    => $totalPrediksi,
            'modelAktif'       => $modelAktif,
            'pendonorBulanIni' => $pendonorBulanIni,
            'donorBulanIni'    => $donorBulanIni,
            'donorBaru'        => $donorBaru,
            'donorUlang'       => $donorUlang,
            'baruPct'          => $baruPct,
            'golonganDarah'    => $golonganDarah,
            'trenDonor'        => $trenDonor,
            'topKecamatan'     => $topKecamatan,
            'baruUlang'        => $baruUlang,
            'pengesahan'       => $pengesahan,
            'prediksiTerbaru'  => $prediksiTerbaru,
            'distribusiUsia'   => $distribusiUsia,
            'jenisKelamin'     => $jenisKelamin,
        ]);
    }
}
