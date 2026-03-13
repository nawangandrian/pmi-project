<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHistoriPrediksiTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id_histori_prediksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'id_model' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'tanggal_prediksi' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'id_user' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'parameter_filter' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON: kecamatan, golongan_darah, max_usia, jenis_kelamin, top_k',
            ],
            'jumlah_hasil' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_histori_prediksi');
        $this->forge->addForeignKey('id_model', 'model_prediksi', 'id_model', 'CASCADE', 'CASCADE');
        $this->forge->createTable('histori_prediksi');
    }

    public function down(): void
    {
        $this->forge->dropTable('histori_prediksi');
    }
}