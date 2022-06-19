<?php

namespace App\Models\v1\BusinessLogic;

use App\Models\v1\WalletModel;
use App\Entities\v1\WalletEntity;

class WalletBusinessLogic
{

    /**
     * 取得使用者帳戶餘額
     *
     * @param integer $u_key
     * @return WalletEntity|null
     */
    static function getWallet(int $u_key): ?WalletEntity
    {
        $walletModel = new WalletModel();

        $walletEntity = $walletModel->find($u_key);

        return $walletEntity;
    }
}
