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
 * @property int $amount
 * @property int $actual_amount
 * @property int $commission
 * @property int $record_type
 * @property int $record_status
 * @property string $remarks
 * @property int $created_at
 * @property string $updated_at
 *
 * Class FundRecord
 * @package Modules\UserFund\Models
 */
class FundRecord extends Model implements Transformable, IModelAccess
{
    use ModelAccess;

    use ErrorTrait;

    protected $table = "user_fund";

    const RECORD_TYPE_WITHDRAW = 1;
    const RECORD_TYPE_RECHARGE = 2;
    const RECORD_TYPE_PAY = 3;
    const RECORD_TYPE_EARN = 4;

    const RECORD_STATUS_VERIFYING = 0;
    const RECORD_STATUS_DONE = 1;
    const RECORD_STATUS_FAILED = 2;
    const RECORD_STATUS_CLOSE = 3;

    protected $fillable = [
        "id", "user_id", "amount", "actual_amount", "commission", "record_type", "record_status",
        "remarks", "created_at", "updated_at"
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

    public function pass()
    {
        return $this->update([
            "record_status" => self::RECORD_STATUS_DONE
        ]);
    }

    public function fail()
    {
        return $this->update([
            "record_status" => self::RECORD_STATUS_FAILED
        ]);
    }

    public function close()
    {
        return $this->update([
            "record_status" => self::RECORD_STATUS_CLOSE
        ]);
    }
}

