<?php

namespace App\Controllers\v1;

use CodeIgniter\API\ResponseTrait;

use App\Controllers\v1\BaseController;
use App\Models\v1\PaymentModel;
use App\Entities\v1\PaymentEntity;

use App\Models\v1\BusinessLogic\PaymentBusinessLogic;
use App\Models\v1\BusinessLogic\WalletBusinessLogic;
use App\Services\User;

class PaymentController extends BaseController
{
    use ResponseTrait;

    /**
     * 使用者 key 從 user service 取得
     *
     * @var int
     */
    private $u_key;

    public function __construct()
    {
        $this->u_key = User::getUserKey();
    }

    /**
     * [GET] /api/v1/payments
     * 取得訂單付款清單
     * 
     * @return void
     */
    public function index()
    {
        $limit = $this->request->getGet("limit") ?? 10;
        $offset = $this->request->getGet("offset") ?? 0;
        $search = $this->request->getGet("search") ?? 0;
        $isDesc = $this->request->getGet("isDesc") ?? "desc";
        $u_key = $this->u_key;

        $paymentModel = new PaymentModel();
        $paymentEntity = new PaymentEntity();

        $query = $paymentModel->orderBy("created_at", $isDesc ? "DESC" : "ASC");
        if ($search !== 0) $query->like("o_key", $search);
        $amount = $query->countAllResults(false);
        $payments = $query->where("u_key",$u_key)->findAll($limit, $offset);

        $data = [
            "list" => [],
            "amount" => $amount
        ];

        if ($payments) {
            foreach ($payments as $paymentEntity) {
                $paymentData = [
                    "u_key" => $paymentEntity->u_key,
                    "o_key" => $paymentEntity->o_key,
                    "h_key" => $paymentEntity->h_key,
                    "total" => $paymentEntity->total
                ];
                $data["list"][] = $paymentData;
            }
        }else{
            return $this->fail("無資料",404);
        }

        return $this->respond([
            "msg" => "OK",
            "data" => $data
        ]);
    }

    /**
     * [GET] /api/v1/payments/{paymentKey}
     * 取得單一訂單付款資訊
     *
     * @param int $paymentKey
     * @return void
     */
    public function show($paymentKey = null)
    {
        if ($paymentKey == null) return $this->fail("無傳入訂單 key", 404);

        $paymentEntity = PaymentBusinessLogic::getPayment($paymentKey,$this->u_key);
        if (is_null($paymentEntity)) return $this->fail("無此訂單付款資訊", 404);

        $data = [
            "u_key" => $paymentEntity->u_key,
            "o_key" => $paymentEntity->o_key,
            "h_key" => $paymentEntity->h_key,
            "total" => $paymentEntity->total
        ];

        return $this->respond([
            "msg" => "OK",
            "data" => $data
        ]);
    }

    /**
     * [POST] /api/v1/payments
     * 新增付款、流水帳與使用者錢包扣款
     *
     * @return void
     */
    public function create()
    {
        $u_key = $this->u_key;
        $o_key = $this->request->getPost("o_key");
        $total = $this->request->getPost("total");
        $type = "orderPayment";

        if (is_null($u_key) || is_null($o_key) || is_null($total)) return $this->fail("傳入資料錯誤", 400);

        $paymentEntity = PaymentBusinessLogic::getPaymentByOrderKey($o_key, $this->u_key);
        if (!is_null($paymentEntity)) return $this->fail("已有此筆訂單紀錄，請確認是否重複輸入", 400);

        $paymentModel = new PaymentModel();

        $nowAmount = WalletBusinessLogic::getWallet($u_key)->balance;

        if($nowAmount < $total) return $this->fail("餘額不足",400);

        $createResult = $paymentModel->createPaymentTranscation($u_key,$o_key,$total,$nowAmount,$type);

        if(is_null($createResult)) return $this->fail("新增付款失敗",400);

        return $this->respond([
                    "msg" => "OK"
                ]);
    }

    /**
     * [PUT] /api/v1/payments
     * 更新訂單付款金額
     *
     * @return void
     */
    public function update()
    {
        $data = $this->request->getJSON(true);

        if (is_null($data["total"]) || is_null($data["p_key"])) return $this->fail("傳入資料錯誤", 400);

        $total = $data["total"];
        $p_key = $data["p_key"];

        $paymentModel = new PaymentModel();
        $paymentEntity = new PaymentEntity();

        $paymentEntity = PaymentBusinessLogic::getPayment($p_key,$this->u_key);
        if(is_null($paymentEntity)) return $this->fail("無此訂單付款資訊",404);

        $paymentEntity->total = $total;

        $result = $paymentModel->update($p_key, $paymentEntity->toRawArray(true));

        if($result){
            return $this->respond([
                        "msg" => "OK"
                    ]);
        }else{
            return $this->fail("更新付款金額失敗",400);
        }
    }

    /**
     * [DELETE] /api/v1/payments/{paymentKey}
     * 刪除訂單付款資訊
     *
     * @param [type] $paymentKey
     * @return void
     */
    public function delete($paymentKey = null)
    {
        if(is_null($paymentKey)) return $this->fail("請輸入訂單付款 key",404);

        $paymentEntity = PaymentBusinessLogic::getPayment($paymentKey, $this->u_key);
        if (is_null($paymentEntity)) return $this->fail("無此訂單付款資訊", 404);

        $paymentModel = new PaymentModel();

        $result = $paymentModel->deletePaymentTranscation($paymentKey,$paymentEntity->h_key);

        if($result){
            return $this->respond([
                "msg" => "OK"
            ]);
        }else{
            return $this->fail("刪除失敗",400);
        }
    }
}
