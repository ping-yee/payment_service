<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Wallet extends Seeder
{
    public function run()
    {
        $faker = static::faker();
        for ($i=0; $i < 5; $i++) {
            $balance = random_int(0, 100000);

            $this->db->table("wallet")
                     ->insert([
                         "u_key" => $i+1,
                         "balance" => $balance,
                         "created_at" => date("Y-m-d H:i:s"),
                         "updated_at" => date("Y-m-d H:i:s")
                     ]);

            $this->db->table("history")
                     ->insert([
                         "u_key" => $i+1,
                         "type" => "stored",
                         "amount" => $balance,
                         "created_at" => date("Y-m-d H:i:s"),
                         "updated_at" => date("Y-m-d H:i:s")
                     ]);
        }
    }
}
