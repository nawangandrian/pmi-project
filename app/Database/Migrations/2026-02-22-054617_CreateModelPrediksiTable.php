<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModelPrediksiTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id_model' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'nama_model' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'parameter_model' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON: n_estimators, min_samples_leaf, class_weight, test_size, alpha_donor, alpha_ulang, random_state',
            ],
            'akurasi_model' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,4',
                'null'       => true,
            ],
            'f1_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,4',
                'null'       => true,
            ],
            'roc_auc' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,4',
                'null'       => true,
            ],
            'cv_roc_auc' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,4',
                'null'       => true,
                'comment'    => 'Cross-validation mean ROC-AUC',
            ],
            'tanggal_training' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'file_model' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Filename .joblib di writable/models/',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'nonaktif',
            ],
            'id_user' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_model');
        $this->forge->createTable('model_prediksi');
    }

    public function down(): void
    {
        $this->forge->dropTable('model_prediksi');
    }
}