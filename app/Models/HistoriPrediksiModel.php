<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriPrediksiModel extends Model
{
    protected $table            = 'histori_prediksi';
    protected $primaryKey       = 'id_histori_prediksi';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_histori_prediksi',
        'id_model',
        'tanggal_prediksi',
        'id_user',
        'parameter_filter',
        'jumlah_hasil',
        'keterangan',
        'created_at',
    ];

    protected $useTimestamps = false;
    protected $beforeInsert  = ['generateId'];

    protected function generateId(array $data): array
    {
        if (! isset($data['data']['id_histori_prediksi'])) {
            $data['data']['id_histori_prediksi'] = 'HPR-' . strtoupper(bin2hex(random_bytes(4)));
        }
        return $data;
    }

    public function getAllWithDetail(): array
    {
        // FIX: ganti 'u.id' → 'u.id_user'
        return $this->db->table('histori_prediksi hp')
            ->select('hp.*, mp.nama_model, mp.akurasi_model, u.username')
            ->join('model_prediksi mp', 'mp.id_model = hp.id_model', 'left')
            ->join('users u', 'u.id_user = hp.id_user', 'left')
            ->orderBy('hp.tanggal_prediksi', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil satu histori beserta info model dan user (dipindah dari controller)
     */
    public function getDetailWithModel(string $id): ?array
    {
        return $this->db->table('histori_prediksi hp')
            ->select('hp.*, mp.nama_model, mp.akurasi_model, mp.f1_score, mp.roc_auc, u.username')
            ->join('model_prediksi mp', 'mp.id_model = hp.id_model', 'left')
            ->join('users u', 'u.id_user = hp.id_user', 'left')
            ->where('hp.id_histori_prediksi', $id)
            ->get()
            ->getRowArray() ?: null;
    }

    /**
     * Ambil histori untuk export (tanpa info user, hanya info model)
     */
    public function getForExport(string $id): ?array
    {
        return $this->db->table('histori_prediksi hp')
            ->select('hp.*, mp.nama_model, mp.akurasi_model')
            ->join('model_prediksi mp', 'mp.id_model = hp.id_model', 'left')
            ->where('hp.id_histori_prediksi', $id)
            ->get()
            ->getRowArray() ?: null;
    }

    /**
     * Simpan histori prediksi baru (dipindah dari controller)
     */
    public function simpanHistori(array $data): string
    {
        $idHistori = 'HPR-' . strtoupper(bin2hex(random_bytes(4)));
        $this->db->table('histori_prediksi')->insert(array_merge(
            ['id_histori_prediksi' => $idHistori],
            $data
        ));
        return $idHistori;
    }

    public function hapusHistori(string $id): void
    {
        $this->db->table('prediksi_pendonor_potensial')
            ->where('id_histori_prediksi', $id)
            ->delete();
        $this->delete($id);
    }
}