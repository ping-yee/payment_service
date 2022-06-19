<?php

namespace App\Database\Migrations;

use App\Database\Migrations\History;
use App\Database\Migrations\Payment;
use App\Database\Migrations\Wallet;

class initDataBase
{
    public static function initDataBase($group = "default")
    {
		\Config\Services::migrations()->setGroup($group);
        // self::createTable($group);
        return "success";

    }

    public static function createTable($group)
    {
        (new History(\Config\Database::forge($group)))->up();
        (new Payment(\Config\Database::forge($group)))->up();
        (new Wallet(\Config\Database::forge($group)))->up();
    }
}
