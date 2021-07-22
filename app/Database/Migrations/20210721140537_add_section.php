<?php

namespace App\Database\Migrations;


use CodeIgniter\Database\Migration;

class AddSection extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 4,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name'        => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'information' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'sequence'    => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'status'      => [
                'type'       => 'TINYINT',
                'constraint' => 1,
            ],
        ]);
        // TODO: Implement up() method.
    }

    public function down()
    {
        // TODO: Implement down() method.
    }

}