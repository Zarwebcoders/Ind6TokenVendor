<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKycFieldsToVendors extends Migration
{
    public function up()
    {
        $fields = [
            'pan_number' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'kyc_status'
            ],
            'gst_number' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'pan_number'
            ],
            'hsn_code' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'gst_number'
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'hsn_code'
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'address'
            ],
            'pincode' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
                'after' => 'city'
            ],
            'pan_document' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'pincode'
            ],
            'gst_document' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'pan_document'
            ],
        ];

        $this->forge->addColumn('vendors', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('vendors', ['pan_number', 'gst_number', 'hsn_code', 'address', 'city', 'pincode', 'pan_document', 'gst_document']);
    }
}
