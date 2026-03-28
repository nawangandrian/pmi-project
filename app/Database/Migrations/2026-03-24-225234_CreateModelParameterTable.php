<?php
// app/Database/Migrations/2026-02-22-000002_CreateModelParameterTable.php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModelParameterTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id_parameter' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'id_model' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],

            // --- Hyperparameter ---
            'n_estimators' => [
                'type'    => 'INT',
                'null'    => true,
                'comment' => 'Jumlah pohon di Random Forest',
            ],
            'min_samples_leaf' => [
                'type'    => 'INT',
                'null'    => true,
                'comment' => 'Min sampel per daun',
            ],
            'class_weight' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Misal: balanced',
            ],
            'test_size' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,2',
                'null'       => true,
                'comment'    => 'Proporsi data test (0.0–1.0)',
            ],
            'alpha_donor' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,3',
                'null'       => true,
            ],
            'alpha_ulang' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,3',
                'null'       => true,
            ],
            'random_state' => [
                'type' => 'INT',
                'null' => true,
            ],

            // --- Filter data training ---
            'filter_golongan_darah' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'JSON array, misal: ["A","B"]',
            ],
            'filter_kecamatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'filter_tanggal_dari' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'filter_tanggal_sampai' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'filter_min_jumlah_donor' => [
                'type' => 'INT',
                'null' => true,
            ],
            'filter_jenis_kelamin' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
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

        $this->forge->addPrimaryKey('id_parameter');
        $this->forge->addKey('id_model');         // index biasa
        $this->forge->addForeignKey(
            'id_model',                           // kolom lokal
            'model_prediksi',                     // tabel referensi
            'id_model',                           // kolom referensi
            'CASCADE',                            // ON DELETE
            'CASCADE'                             // ON UPDATE
        );

        $this->forge->createTable('model_parameter');
    }

    public function down(): void
    {
        $this->forge->dropTable('model_parameter');
    }
}