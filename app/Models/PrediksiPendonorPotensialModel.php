<?php

namespace App\Models;

use CodeIgniter\Model;

class PrediksiPendonorPotensialModel extends Model
{
    protected $table            = 'prediksi_pendonor_potensial';
    protected $primaryKey       = 'id_prediksi';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_prediksi',
        'id_histori_prediksi',
        'id_pendonor',
        'probabilitas_donor',
        'label_prediksi',
        'peringkat',
        'created_at',
    ];

    protected $useTimestamps = false;
    protected $beforeInsert  = ['generateId'];

    protected function generateId(array $data): array
    {
        if (! isset($data['data']['id_prediksi'])) {
            $data['data']['id_prediksi'] = 'PRD-' . strtoupper(bin2hex(random_bytes(4)));
        }
        return $data;
    }

    public function getHasilWithPendonor(string $idHistoriPrediksi): array
    {
        return $this->db->table('prediksi_pendonor_potensial ppp')
            ->select('ppp.*, dp.id_pendonor_pusat, dp.nama_pendonor, dp.alamat,
                      dp.no_hp, dp.umur, dp.jenis_kelamin, dp.golongan_darah, dp.kecamatan')
            ->join('data_pendonor dp', 'dp.id_pendonor = ppp.id_pendonor', 'left')
            ->where('ppp.id_histori_prediksi', $idHistoriPrediksi)
            ->orderBy('ppp.peringkat', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Simpan batch hasil prediksi (dipindah dari controller)
     */
    public function simpanBatch(string $idHistori, array $topHasil): void
    {
        foreach ($topHasil as $rank => $row) {
            $this->db->table('prediksi_pendonor_potensial')->insert([
                'id_prediksi'         => 'PRD-' . strtoupper(bin2hex(random_bytes(4))),
                'id_histori_prediksi' => $idHistori,
                'id_pendonor'         => $row['id_pendonor'],
                'probabilitas_donor'  => round((float) ($row['skor'] ?? 0), 4),
                'label_prediksi'      => ($row['skor'] ?? 0) >= 0.3 ? 'potensial' : 'tidak_potensial',
                'peringkat'           => $rank + 1,
                'created_at'          => date('Y-m-d H:i:s'),
            ]);
        }
    }
}