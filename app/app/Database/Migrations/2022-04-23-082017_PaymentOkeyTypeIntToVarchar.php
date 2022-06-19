<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PaymentOkeyTypeIntToVarchar extends Migration
{
    public function up()
    {
        $fields = [
            'o_key'  => [
                'type'         => 'VARCHAR',
                'constraint'   => 200,
            ]
        ];

        $this->forge->modifyColumn('payment', $fields);
    }

    public function down()
    {
        $fields = [
            'o_key'  => [
                'type'         => 'INT',
                'constraint'   => 5,
            ]
        ];

        $this->forge->modifyColumn('payment', $fields);
    }
}
