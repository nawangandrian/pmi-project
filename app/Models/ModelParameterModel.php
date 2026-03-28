<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelParameterModel extends Model
{
    protected $table            = 'model_parameter';
    protected $primaryKey       = 'id_parameter';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_parameter',
        'id_model',
        // Hyperparameter
        'n_estimators',
        'min_samples_leaf',
        'class_weight',
        'test_size',
        'alpha_donor',
        'alpha_ulang',
        'random_state',
        // Filter data training
        'filter_golongan_darah',    // disimpan sebagai JSON string
        'filter_kecamatan',
        'filter_tanggal_dari',
        'filter_tanggal_sampai',
        'filter_min_jumlah_donor',
        'filter_jenis_kelamin',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    // =========================================================
    // QUERY METHODS
    // =========================================================

    public function getByIdModel(string $idModel): ?array
    {
        $row = $this->where('id_model', $idModel)->first();

        if ($row) {
            $row['filter_golongan_darah'] = $this->decodeGolongan($row['filter_golongan_darah']);
        }

        return $row;
    }

    /**
     * Upsert parameter untuk satu model.
     * filter_golongan_darah (array) di-encode ke JSON sebelum disimpan.
     */
    public function saveForModel(string $idModel, array $params): void
    {
        // Encode array golongan darah → JSON string untuk disimpan
        if (isset($params['filter_golongan_darah'])) {
            $gol = $params['filter_golongan_darah'];
            $params['filter_golongan_darah'] = (is_array($gol) && ! empty($gol))
                ? json_encode(array_values($gol))
                : null;
        }

        // Normalisasi nilai kosong filter string → null
        foreach (['filter_kecamatan', 'filter_jenis_kelamin', 'filter_tanggal_dari', 'filter_tanggal_sampai'] as $col) {
            if (isset($params[$col]) && $params[$col] === '') {
                $params[$col] = null;
            }
        }

        $existing = $this->where('id_model', $idModel)->first();

        if ($existing) {
            $this->update($existing['id_parameter'], $params);
        } else {
            $this->insert(array_merge([
                'id_parameter' => 'PRM-' . strtoupper(bin2hex(random_bytes(4))),
                'id_model'     => $idModel,
            ], $params));
        }
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function decodeGolongan(mixed $value): array
    {
        if (empty($value)) return [];
        if (is_array($value)) return $value;
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }
}