<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDataHistorisDonor extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_histori' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
            ],
            'id_pendonor' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
            ],
            'no_trans' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
            ],
            'tanggal_donor' => [
                'type' => 'DATE',
            ],
            'jumlah_donor' => [
                'type' => 'INT',
            ],
            'status_donor' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'tidak_aktif'],
                'default'    => 'aktif',
            ],
            'status_pengesahan' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'disahkan', 'ditolak'],
                'default'    => 'pending',
            ],
            'baru_ulang' => [
                'type'       => 'ENUM',
                'constraint' => ['baru', 'ulang'],
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id_histori', true);
        $this->forge->addForeignKey(
            'id_pendonor',
            'data_pendonor',
            'id_pendonor',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('data_historis_donor');
    }

    public function down()
    {
        $this->forge->dropTable('data_historis_donor');
    }
}
