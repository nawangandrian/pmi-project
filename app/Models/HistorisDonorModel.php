<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorisDonorModel extends Model
{
    protected $table            = 'data_historis_donor';
    protected $primaryKey       = 'id_histori';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_histori',
        'id_pendonor',
        'no_trans',
        'tanggal_donor',
        'jumlah_donor',
        'status_donor',
        'status_pengesahan',
        'baru_ulang'
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
        if (!isset($data['data']['id_histori'])) {
            $data['data']['id_histori'] = 'HSD-' . strtoupper(bin2hex(random_bytes(4)));
        }
        return $data;
    }

    /**
     * Ambil data dengan join pendonor, filter opsional
     */
    public function getWithPendonor(array $filters = []): array
    {
        $builder = $this->db->table('data_historis_donor h')
            ->select('h.*, p.nama_pendonor, p.id_pendonor_pusat')
            ->join('data_pendonor p', 'p.id_pendonor = h.id_pendonor');

        if (!empty($filters['status_donor'])) {
            $builder->where('h.status_donor', $filters['status_donor']);
        }
        if (!empty($filters['status_pengesahan'])) {
            $builder->where('h.status_pengesahan', $filters['status_pengesahan']);
        }
        if (!empty($filters['baru_ulang'])) {
            $builder->where('h.baru_ulang', $filters['baru_ulang']);
        }
        if (!empty($filters['tanggal_dari'])) {
            $builder->where('DATE(h.tanggal_donor) >=', $filters['tanggal_dari']);
        }
        if (!empty($filters['tanggal_sampai'])) {
            $builder->where('DATE(h.tanggal_donor) <=', $filters['tanggal_sampai']);
        }

        return $builder->orderBy('h.tanggal_donor', 'DESC')->get()->getResultArray();
    }

    /**
     * Hapus semua historis donor (truncate aman tanpa FK issue karena
     * historis_donor adalah tabel child, bukan parent)
     */
    public function truncateAll(): void
    {
        $db = $this->db;
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        $db->table($this->table)->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}