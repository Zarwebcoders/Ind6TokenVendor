<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCheckoutFieldsToPayments extends Migration
{
    public function up()
    {
        $fields = [
            'platform_txn_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'unique'     => true,
                'after'      => 'id'
            ],
            'buyer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'vendor_id'
            ],
            'buyer_email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'buyer_name'
            ],
            'buyer_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'after'      => 'buyer_email'
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'default'    => 'upi',
                'after'      => 'buyer_phone'
            ],
            'failure_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status'
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at'
            ]
        ];

        $this->forge->addColumn('payments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('payments', [
            'platform_txn_id',
            'buyer_name',
            'buyer_email',
            'buyer_phone',
            'payment_method',
            'failure_reason',
            'completed_at'
        ]);
    }
}
