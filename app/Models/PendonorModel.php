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
        'kecamatan'
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
        if (! isset($data['data']['id_pendonor'])) {
            $data['data']['id_pendonor'] = 'PND-' . strtoupper(bin2hex(random_bytes(4)));
        }
        return $data;
    }
}
