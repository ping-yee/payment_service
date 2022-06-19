<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaymentServiceSeeder extends Seeder
{
    public function run()
    {
        $this->call("Payment");
        $this->call("Wallet");
    }
}
