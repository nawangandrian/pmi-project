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

    protected $beforeInsert = ['generateId'];

    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $dateFormat    = 'datetime';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function generateId(array $data)
    {
        if (! isset($data['data']['id_histori'])) {
            $data['data']['id_histori'] = 'HSD-' . strtoupper(bin2hex(random_bytes(4)));
        }
        return $data;
    }
}
