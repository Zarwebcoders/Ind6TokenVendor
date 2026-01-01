<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGatewayFieldsToPayments extends Migration
{
    public function up()
    {
        $fields = [
            'gateway_txn_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'txn_id',
            ],
        ];

        $this->forge->addColumn('payments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('payments', 'gateway_txn_id');
    }
}
