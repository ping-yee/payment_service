<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Payment extends Seeder
{
    public function run()
    {
        $faker = static::faker();

        for ($i=0; $i < 100; $i++) {
            
            $now        = date("Y-m-d H:i:s");
            $u_key      = random_int(1, 5);
            $amount     = random_int(0, 2000);
            $o_key      = sha1($u_key . $amount . $now . random_int(0, 10000000));

            $this->db->table("history")
                     ->insert([
                         "u_key" => $u_key,
                         "type" => "orderPayment",
                         "amount" => $amount,
                         "created_at" => date("Y-m-d H:i:s"),
                         "updated_at" => date("Y-m-d H:i:s")
                     ]);

            $insertKey = $this->db->insertID();

            if(random_int(0, 1) == 1){
                $this->db->table("history")
                         ->insert([
                             "u_key" => $u_key,
                             "type" => "compensate",
                             "amount" => $amount,
                             "created_at" => date("Y-m-d H:i:s"),
                             "updated_at" => date("Y-m-d H:i:s")
                         ]);
            }
            
            $this->db->table("payment")
                     ->insert([
                         "u_key" => $u_key,
                         "o_key" => $o_key,
                         "h_key" => $insertKey,
                         "total" => random_int(0,10000),
                         "created_at" => date("Y-m-d H:i:s"),
                         "updated_at" => date("Y-m-d H:i:s")
                     ]);
        }
    }
}
