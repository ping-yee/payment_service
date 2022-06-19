<?php

namespace App\Controllers\v1;

use CodeIgniter\API\ResponseTrait;

use App\Controllers\v1\BaseController;
use App\Models\v1\WalletModel;
use App\Models\v1\BusinessLogic\WalletBusinessLogic;
use App\Services\User;

class WalletController extends BaseController
{
    use ResponseTrait;
    
    private $u_key;

    public function __construct()
    {   
        $this->u_key = User::getUserKey();
    }

    /**
     * [GET] /api/v1/wallet/{userKey}
     * 取得單一使用者錢包餘額
     *
     * @param int $userKey
     * @return void
     */
    public function show()
    {
        $walletEntity = WalletBusinessLogic::getWallet($this->u_key);
        if (is_null($walletEntity)) return $this->fail("無此使用者錢包資訊", 404);

        $data = [
            "u_key" => $walletEntity->u_key,
            "balance" => $walletEntity->balance
        ];

        return $this->respond([
            "msg" => "OK",
            "data" => $data
        ]);
    }

    /**
     * [POST] /api/v1/wallet
     * 錢包儲值
     *
     * @return void
     */
    public function create()
    {
        $u_key = $this->u_key;
        $addAmount = $this->request->getPost("addAmount");
        $type = "store";

        if(is_null($u_key) || is_null($addAmount)) return $this->fail("輸入資料錯誤",400);

        $walletEntity = WalletBusinessLogic::getWallet($u_key);
        if(is_null($walletEntity)) return $this->fail("找不到此使用者錢包資訊",404);

        $balance = $walletEntity->balance;

        $walletModel = new WalletModel();
        $result = $walletModel->addBalanceTranscation($u_key,$type,$balance,$addAmount);

        if($result){
            return $this->respond([
                "msg" => "OK"
            ]);
        }else{
            return $this->fail("儲值失敗",400);
        }
    }

    /**
     * [POST] /api/v1/wallet/compensate
     * 錢包補償
     *
     * @return void
     */
    public function compensate()
    {
        $u_key = $this->u_key;
        $addAmount = $this->request->getPost("addAmount");
        $type = "compensate";

        if (is_null(($u_key)) || is_null(($addAmount))) return $this->fail("輸入資料錯誤", 400);

        $walletEntity = WalletBusinessLogic::getWallet($u_key);
        if (is_null($walletEntity)) return $this->fail("找不到此使用者錢包資訊", 404);

        $balance = $walletEntity->balance;

        $walletModel = new WalletModel();
        $result = $walletModel->addBalanceTranscation($u_key, $type, $balance, $addAmount);

        if ($result) {
            return $this->respond([
                "msg" => "OK"
            ]);
        } else {
            return $this->fail("儲值失敗", 400);
        }
    }
}
