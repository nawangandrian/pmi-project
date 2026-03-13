<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPrediksiModel extends Model
{
    protected $table            = 'model_prediksi';
    protected $primaryKey       = 'id_model';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_model',
        'nama_model',
        'parameter_model',
        'akurasi_model',
        'f1_score',
        'roc_auc',
        'cv_roc_auc',
        'tanggal_training',
        'file_model',
        'status',
        'id_user',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $beforeInsert  = ['generateId'];

    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    protected function generateId(array $data): array
    {
        if (! isset($data['data']['id_model'])) {
            $data['data']['id_model'] = 'MDL-' . strtoupper(bin2hex(random_bytes(4)));
        }
        return $data;
    }

    public function getAktif(): ?array
    {
        return $this->where('status', 'aktif')->first();
    }

    public function setAktif(string $idModel): void
    {
        $this->db->table('model_prediksi')->set('status', 'nonaktif')->update();
        $this->update($idModel, ['status' => 'aktif']);
    }

    public function getAllWithUser(): array
    {
        return $this->db->table('model_prediksi mp')
            ->select('mp.*, u.username')
            ->join('users u', 'u.id_user = mp.id_user', 'left')
            ->orderBy('mp.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil data historis JOIN pendonor untuk training.
     * Mendukung filter opsional:
     *   - golongan_darah  : string|array  (e.g. 'A+' atau ['A+','B+'])
     *   - kecamatan       : string        (exact match)
     *   - tanggal_dari    : string Y-m-d
     *   - tanggal_sampai  : string Y-m-d
     *   - min_jumlah_donor: int           (jumlah_donor >= nilai ini)
     *   - jenis_kelamin   : 'L'|'P'       (opsional)
     */
    public function getTrainingData(array $filters = []): array
    {
        $builder = $this->db->table('data_historis_donor h')
            ->select('h.id_pendonor, h.tanggal_donor, h.jumlah_donor, h.baru_ulang,
                      h.status_donor, h.status_pengesahan,
                      p.nama_pendonor, p.umur, p.jenis_kelamin, p.golongan_darah, p.kecamatan')
            ->join('data_pendonor p', 'p.id_pendonor = h.id_pendonor')
            ->where('h.status_pengesahan', 'sudah');

        // ── Filter golongan darah ──────────────────────────────
        if (! empty($filters['golongan_darah'])) {
            $gol = (array) $filters['golongan_darah'];
            $builder->whereIn('p.golongan_darah', $gol);
        }

        // ── Filter kecamatan ───────────────────────────────────
        if (! empty($filters['kecamatan'])) {
            $builder->where('p.kecamatan', $filters['kecamatan']);
        }

        // ── Filter rentang tanggal donor ───────────────────────
        if (! empty($filters['tanggal_dari'])) {
            $builder->where('DATE(h.tanggal_donor) >=', $filters['tanggal_dari']);
        }
        if (! empty($filters['tanggal_sampai'])) {
            $builder->where('DATE(h.tanggal_donor) <=', $filters['tanggal_sampai']);
        }

        // ── Filter minimum jumlah donor (frekuensi) ────────────
        if (! empty($filters['min_jumlah_donor']) && (int) $filters['min_jumlah_donor'] > 1) {
            $builder->where('h.jumlah_donor >=', (int) $filters['min_jumlah_donor']);
        }

        // ── Filter jenis kelamin ───────────────────────────────
        if (! empty($filters['jenis_kelamin']) && in_array($filters['jenis_kelamin'], ['L', 'P'])) {
            $builder->where('p.jenis_kelamin', $filters['jenis_kelamin']);
        }

        return $builder->get()->getResultArray();
    }
}