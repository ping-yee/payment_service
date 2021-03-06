<?php

namespace App\Entities\v1;

use CodeIgniter\Entity\Entity;

class WalletEntity extends Entity
{
    /**
     * 使用者外來鍵
     *
     * @var int
     */
    protected $u_key;

    /**
     * 帳戶餘額
     *
     * @var int
     */
    protected $balance;

    /**
     * 建立時間
     *
     * @var string
     */
    protected $createdAt;

    /**
     * 最後更新時間
     *
     * @var string
     */
    protected $updatedAt;

    /**
     * 刪除時間
     *
     * @var string
     */
    protected $deletedAt;

    protected $datamap = [
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
        'deletedAt' => 'deleted_at'
    ];

    protected $casts = [
        'u_key' => 'integer'
    ];

    protected $dates = []; 
}
