<?php

namespace App\Databse\Migrations;

use CodeIgniter\Database\Migration;

class AddPost extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => [
                'type'           => 'INT',
                'constraint'     => 4,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'users_id'     => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'username'     => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'title'        => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'content'      => [
                'type' => 'TEXT',
            ],
            'sequence'     => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'reply_number' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'status'       => [
                'type'       => 'TINYINT',
                'constraint' => 1,
            ],
            'created_at'   => [
                'type' => 'DATETIME',
            ],
            'updated_at'   => [
                'type' => 'DATETIME'
            ],
        ]);
    }

    public function down()
    {
        // TODO: Implement down() method.
    }
}
