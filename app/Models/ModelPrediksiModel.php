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
        // parameter_model DIHAPUS — kini di tabel model_parameter
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

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    // =========================================================
    // INSERT — generate ID & kembalikan ke caller
    // =========================================================

    /**
     * Insert model baru, generate ID otomatis, dan return ID-nya.
     */
    public function insertAndGetId(array $data): string
    {
        $idModel        = 'MDL-' . strtoupper(bin2hex(random_bytes(4)));
        $data['id_model'] = $idModel;
        $this->insert($data);
        return $idModel;
    }

    // =========================================================
    // READ
    // =========================================================

    public function getAktif(): ?array
    {
        return $this->where('status', 'aktif')->first();
    }

    /**
     * Ambil semua model beserta username pembuat (untuk tabel daftar).
     */
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
     * Ambil satu model BESERTA seluruh parameter dari tabel model_parameter.
     *
     * Return: array flat (semua kolom mp + semua kolom mpar),
     *         filter_golongan_darah sudah di-decode ke PHP array.
     *         Return null jika model tidak ditemukan.
     */
    public function getWithParameter(string $idModel): ?array
    {
        $row = $this->db->table('model_prediksi mp')
            ->select('
                mp.id_model, mp.nama_model, mp.akurasi_model, mp.f1_score,
                mp.roc_auc, mp.cv_roc_auc, mp.tanggal_training, mp.file_model,
                mp.status, mp.id_user, mp.keterangan,
                mp.created_at, mp.updated_at,
                mpar.id_parameter,
                mpar.n_estimators, mpar.min_samples_leaf, mpar.class_weight,
                mpar.test_size, mpar.alpha_donor, mpar.alpha_ulang, mpar.random_state,
                mpar.filter_golongan_darah, mpar.filter_kecamatan,
                mpar.filter_tanggal_dari, mpar.filter_tanggal_sampai,
                mpar.filter_min_jumlah_donor, mpar.filter_jenis_kelamin
            ')
            ->join('model_parameter mpar', 'mpar.id_model = mp.id_model', 'left')
            ->where('mp.id_model', $idModel)
            ->get()
            ->getRowArray();

        if (! $row) {
            return null;
        }

        // Decode JSON golongan darah → PHP array
        $raw = $row['filter_golongan_darah'];
        $row['filter_golongan_darah'] = (! empty($raw))
            ? (json_decode($raw, true) ?? [])
            : [];

        return $row;
    }

    // =========================================================
    // UPDATE STATUS
    // =========================================================

    public function setAktif(string $idModel): void
    {
        $this->db->table('model_prediksi')->set('status', 'nonaktif')->update();
        $this->update($idModel, ['status' => 'aktif']);
    }

    // =========================================================
    // DATA TRAINING — JOIN historis + pendonor dengan filter
    // =========================================================

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
            ->select('
                h.id_pendonor, h.tanggal_donor, h.jumlah_donor, h.baru_ulang,
                h.status_donor, h.status_pengesahan,
                p.nama_pendonor, p.umur, p.jenis_kelamin, p.golongan_darah, p.kecamatan
            ')
            ->join('data_pendonor p', 'p.id_pendonor = h.id_pendonor')
            ->where('h.status_pengesahan', 'sudah');

        // ── Filter golongan darah ──────────────────────────────
        if (! empty($filters['golongan_darah'])) {
            $builder->whereIn('p.golongan_darah', (array) $filters['golongan_darah']);
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