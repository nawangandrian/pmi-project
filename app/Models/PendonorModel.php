<?php

namespace App\Models;

use CodeIgniter\Model;

class PendonorModel extends Model
{
    protected $table            = 'data_pendonor';
    protected $primaryKey       = 'id_pendonor';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_pendonor',
        'id_pendonor_pusat',
        'nama_pendonor',
        'alamat',
        'no_hp',
        'umur',
        'jenis_kelamin',
        'golongan_darah',
        'kecamatan',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $beforeInsert  = ['generateId'];

    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts        = [];
    protected array $castHandlers = [];

    protected $dateFormat   = 'datetime';
    protected $deletedField = 'deleted_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function generateId(array $data): array
    {
        if (! isset($data['data']['id_pendonor'])) {
            $data['data']['id_pendonor'] = 'PND-' . strtoupper(bin2hex(random_bytes(4)));
        }
        return $data;
    }

    public function searchByKeyword(string $keyword): array
    {
        return $this->like('id_pendonor_pusat', $keyword)
            ->orLike('nama_pendonor', $keyword)
            ->select('id_pendonor, id_pendonor_pusat, nama_pendonor')
            ->limit(20)
            ->findAll();
    }

    public function getFiltered(array $filters = []): array
    {
        $builder = $this->builder();

        if (! empty($filters['golongan_darah'])) {
            $builder->whereIn('golongan_darah', (array) $filters['golongan_darah']);
        }
        if (! empty($filters['jenis_kelamin'])) {
            $builder->where('jenis_kelamin', $filters['jenis_kelamin']);
        }
        if (! empty($filters['kecamatan'])) {
            $builder->like('kecamatan', $filters['kecamatan']);
        }

        return $builder->get()->getResultArray();
    }

    public function getKecamatanList(): array
    {
        return $this->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan', 'ASC')
            ->findAll();
    }

    /**
     * Ambil daftar golongan darah yang ada di database.
     * Digunakan untuk populate dropdown di form prediksi.
     */
    public function getGolonganDarahList(): array
    {
        return $this->select('golongan_darah')
            ->distinct()
            ->orderBy('golongan_darah', 'ASC')
            ->findAll();
    }

    /**
     * Ambil kandidat prediksi: snapshot terbaru per pendonor,
     * minimal $minGapHari hari sejak donor terakhir.
     *
     * @param string $kecamatan     Nama kecamatan, atau 'all' untuk semua kecamatan
     * @param string $golonganDarah Kode golongan darah (e.g. 'A+'), atau 'all' untuk semua
     * @param int    $maxUsia       Batas usia maksimum
     * @param string $jk            'L', 'P', atau 'all' untuk semua jenis kelamin
     * @param int    $minGapHari    Minimum hari sejak donor terakhir (default: 60)
     */
    public function getKandidatPrediksi(
        string $kecamatan,
        string $golonganDarah,
        int    $maxUsia,
        string $jk = 'all',
        int    $minGapHari = 60
    ): array {
        $cutoff = date('Y-m-d', strtotime("-{$minGapHari} days"));

        $builder = $this->db->table('data_historis_donor h')
            ->select(
                'h.id_pendonor,
                 MAX(h.tanggal_donor)     AS tanggal_donor,
                 MAX(h.jumlah_donor)      AS donor_ke,
                 MAX(h.baru_ulang)        AS baru_ulang,
                 MAX(h.status_donor)      AS status_donor,
                 MAX(h.status_pengesahan) AS status_pengesahan,
                 p.nama_pendonor,
                 p.id_pendonor_pusat,
                 p.alamat,
                 p.no_hp,
                 p.umur,
                 p.jenis_kelamin,
                 p.golongan_darah,
                 p.kecamatan'
            )
            ->join('data_pendonor p', 'p.id_pendonor = h.id_pendonor')
            ->where('p.umur <=', $maxUsia)
            ->where('DATE(h.tanggal_donor) <=', $cutoff)
            ->groupBy('h.id_pendonor');

        // Filter kecamatan — lewati jika 'all'
        if ($kecamatan !== 'all' && $kecamatan !== '') {
            $builder->where('p.kecamatan', $kecamatan);
        }

        // Filter golongan darah — lewati jika 'all'
        if ($golonganDarah !== 'all' && $golonganDarah !== '') {
            $builder->where('p.golongan_darah', $golonganDarah);
        }

        // Filter jenis kelamin — lewati jika 'all'
        if ($jk !== 'all' && $jk !== '') {
            $builder->where('p.jenis_kelamin', $jk);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Hapus semua pendonor (dan opsional historisnya).
     */
    public function truncateAll(bool $withHistoris = false): void
    {
        $db = $this->db;
        $db->query('SET FOREIGN_KEY_CHECKS=0');

        if ($withHistoris) {
            $db->table('data_historis_donor')->truncate();
        }

        $db->table($this->table)->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}