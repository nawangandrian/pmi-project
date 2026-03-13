<?php

namespace App\Controllers;

use App\Models\ModelPrediksiModel;
use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        $db = \Config\Database::connect();

        // ── Tren donor 6 bulan — sama persis dengan DashboardController ─────
        // Cari tanggal donor terbaru sebagai anchor, bukan NOW()
        $lastDate = $db->table('data_historis_donor')
            ->selectMax('tanggal_donor')
            ->get()->getRow()->tanggal_donor;

        $trenDonor = [];
        if ($lastDate) {
            for ($i = 5; $i >= 0; $i--) {
                $bulanKey = date('Y-m', strtotime($lastDate . " -$i months"));
                $bulanLbl = date('M Y', strtotime($lastDate . " -$i months"));

                $jumlah = $db->table('data_historis_donor')
                    ->where("DATE_FORMAT(tanggal_donor,'%Y-%m')", $bulanKey)
                    ->countAllResults();

                $trenDonor[] = [
                    'bulan' => $bulanKey,
                    'label' => $bulanLbl,
                    'total' => (int) $jumlah,
                ];
            }
        }

        // ── Distribusi golongan darah ─────────────────────────────────────────
        $golonganDarah = $db->query("
            SELECT golongan_darah, COUNT(*) AS jumlah
            FROM data_pendonor
            WHERE golongan_darah IS NOT NULL AND golongan_darah != ''
            GROUP BY golongan_darah
            ORDER BY jumlah DESC
        ")->getResultArray();

        // ── Statistik global ──────────────────────────────────────────────────
        $totalPendonor = $db->table('data_pendonor')->countAll();
        $totalHistoris = $db->table('data_historis_donor')->countAll();
        $totalPrediksi = $db->table('histori_prediksi')->countAll();

        // ── Kandidat teratas dari sesi prediksi terbaru ───────────────────────
        $kandidatTeratas = $db->query("
            SELECT
                ppp.probabilitas_donor,
                ppp.peringkat,
                dp.nama_pendonor,
                dp.golongan_darah,
                dp.kecamatan,
                LEFT(dp.nama_pendonor, 1) AS huruf1,
                SUBSTRING_INDEX(
                    TRIM(SUBSTRING(dp.nama_pendonor,
                        LOCATE(' ', dp.nama_pendonor) + 1
                    )), ' ', 1
                ) AS huruf2
            FROM prediksi_pendonor_potensial ppp
            JOIN data_pendonor dp ON dp.id_pendonor = ppp.id_pendonor
            WHERE ppp.id_histori_prediksi = (
                SELECT id_histori_prediksi
                FROM histori_prediksi
                ORDER BY tanggal_prediksi DESC
                LIMIT 1
            )
            ORDER BY ppp.peringkat ASC
            LIMIT 3
        ")->getResultArray();

        // ── Model aktif ───────────────────────────────────────────────────────
        $modelAktif = (new ModelPrediksiModel())->getAktif();

        return view('welcome_message', [
            'trenDonor'       => $trenDonor,
            'golonganDarah'   => $golonganDarah,
            'totalPendonor'   => $totalPendonor,
            'totalHistoris'   => $totalHistoris,
            'totalPrediksi'   => $totalPrediksi,
            'kandidatTeratas' => $kandidatTeratas,
            'modelAktif'      => $modelAktif,
        ]);
    }
}