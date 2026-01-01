<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWalletAddressToVendors extends Migration
{
    public function up()
    {
        $fields = [
            'wallet_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'phone',
            ],
        ];

        $this->forge->addColumn('vendors', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('vendors', 'wallet_address');
    }
}
