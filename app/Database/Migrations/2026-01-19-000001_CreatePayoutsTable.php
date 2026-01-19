<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayoutsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'vendor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '20,8',
            ],
            'txn_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'gateway_order_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'gateway_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'kay2pay',
            ],
            'gateway_response' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'beneficiary_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'account_number' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'ifsc_code' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'bank_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'utr' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'processing', 'completed', 'failed'],
                'default' => 'pending',
            ],
            'method' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'bank_transfer',
            ],
            'failure_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'verify_source' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('payouts');
    }

    public function down()
    {
        $this->forge->dropTable('payouts');
    }
}
