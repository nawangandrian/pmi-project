<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDataPendonor extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pendonor' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
            ],
            'id_pendonor_pusat' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
            ],
            'nama_pendonor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'alamat' => [
                'type' => 'TEXT',
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'umur' => [
                'type' => 'INT',
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
            ],
            'golongan_darah' => [
                'type'       => 'ENUM',
                'constraint' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            ],
            'kecamatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id_pendonor', true);
        $this->forge->createTable('data_pendonor');
    }

    public function down()
    {
        $this->forge->dropTable('data_pendonor');
    }
}
