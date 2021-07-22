<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReply extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'constraint'     => 4,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'post_id'    => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'floor'      => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'users_id'   => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'username'   => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'content'    => [
                'type' => 'TEXT',
            ],
            'status'     => [
                'type'       => 'TINYINT',
                'constraint' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME'
            ],

        ]);
        // TODO: Implement up() method.
    }

    public function down()
    {
        // TODO: Implement down() method.
    }
}
