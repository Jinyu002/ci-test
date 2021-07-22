<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => [
                'type'           => 'INT',
                'constraint'     => 4,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username'      => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'password'      => [
                'type'       => 'CHAR',
                'constraint' => '32'
            ],
            'nickname'      => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'head'          => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
                'null'       => true,
            ],
            'email'         => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'birthday'      => [
                'type' => 'DATE',
                'null' => true,
            ],
            'sex'           => [
                'type'       => 'TINYINT',
                'constraint' => 1,
            ],
            'address'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'post_number'   => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'reply_number'  => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'status'        => [
                'type'       => 'TINYINT',
                'constraint' => 1,
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at'    => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'    => [
                'type' => 'DATETIME',
                'null' => true,
            ],

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
        // TODO: Implement down() method.
    }
}
