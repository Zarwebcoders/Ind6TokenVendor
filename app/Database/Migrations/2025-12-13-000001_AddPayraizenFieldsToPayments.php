<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPayraizenFieldsToPayments extends Migration
{
    public function up()
    {
        $fields = [
            'gateway_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'method'
            ],
            'gateway_order_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'gateway_name'
            ],
            'gateway_response' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'gateway_order_id'
            ]
        ];

        $this->forge->addColumn('payments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('payments', ['gateway_name', 'gateway_order_id', 'gateway_response']);
    }
}
