<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrediksiPendonorPotensialTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id_prediksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'id_histori_prediksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'id_pendonor' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'probabilitas_donor' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,4',
                'null'       => true,
                'comment'    => 'Skor p_return_adjusted',
            ],
            'label_prediksi' => [
                'type'       => 'ENUM',
                'constraint' => ['potensial', 'tidak_potensial'],
                'default'    => 'tidak_potensial',
            ],
            'peringkat' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_prediksi');
        $this->forge->addForeignKey('id_histori_prediksi', 'histori_prediksi', 'id_histori_prediksi', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_pendonor', 'data_pendonor', 'id_pendonor', 'CASCADE', 'CASCADE');
        $this->forge->createTable('prediksi_pendonor_potensial');
    }

    public function down(): void
    {
        $this->forge->dropTable('prediksi_pendonor_potensial');
    }
}