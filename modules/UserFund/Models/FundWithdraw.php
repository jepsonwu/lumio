<?php

namespace Modules\UserFund\Models;

use App\Components\BootstrapHelper\ErrorTrait;
use App\Components\BootstrapHelper\IModelAccess;
use App\Components\BootstrapHelper\ModelAccess;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $user_id
 * @property int $fund_record_id
 * @property int $amount
 * @property int $account_id
 * @property int $account_type
 * @property int $withdraw_status
 * @property int $withdraw_time
 * @property int $verify_time
 * @property int $verify_user_id
 * @property string $verify_remark
 * @property int $created_at
 * @property string $updated_at
 *
 * Class FundWithdraw
 * @package Modules\UserFund\Models
 */
class FundWithdraw extends Model implements Transformable, IModelAccess
{
    use ModelAccess;

    use ErrorTrait;

    protected $table = "user_fund_withdraw";

    const ACCOUNT_TYPE_BACK = 1;
    const ACCOUNT_TYPE_ALIPAY = 2;
    const ACCOUNT_TYPE_WECHAT = 3;

    const WITHDRAW_STATUS_VERIFYING = 0;
    const WITHDRAW_STATUS_DONE = 1;
    const WITHDRAW_STATUS_FAILED = 2;
    const WITHDRAW_STATUS_CLOSE = 3;

    protected $fillable = [
        "id", "user_id", "amount", "account_id", "account_type", "withdraw_status", "withdraw_time",
        "verify_remark", "verify_time", "verify_user_id", "fund_record_id", "created_at", "updated_at"
    ];

    public function transform()
    {
        $result = parent::toArray();

        unset($result['updated_at']);
        return $result;
    }

    public function whereUserId(Builder &$builder, $userId)
    {
        $builder->where("user_id", $userId);
        return $this;
    }

    public function isWaiting()
    {
        return $this->withdraw_status == self::WITHDRAW_STATUS_VERIFYING;
    }

    public function pass($verifyUserId)
    {
        return $this->update([
            "withdraw_status" => self::WITHDRAW_STATUS_DONE,
            "verify_user_id" => $verifyUserId,
            "verify_time" => time()
        ]);
    }

    public function fail($verifyUserId, $reason)
    {
        return $this->update([
            "withdraw_status" => self::WITHDRAW_STATUS_FAILED,
            "verify_user_id" => $verifyUserId,
            "verify_remark" => $reason,
            "verify_time" => time()
        ]);
    }

    public function close()
    {
        return $this->update([
            "withdraw_status" => self::WITHDRAW_STATUS_CLOSE
        ]);
    }
}

