<?php

namespace Modules\UserFund\Models;

use App\Components\BootstrapHelper\ErrorTrait;
use App\Components\BootstrapHelper\IModelAccess;
use App\Components\BootstrapHelper\ModelAccess;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $user_id
 * @property int $amount
 * @property int $locked
 * @property int $total_earn
 * @property int $total_pay
 * @property int $total_withdraw
 * @property int $total_recharge
 * @property int $created_at
 * @property string $updated_at
 *
 * Class Fund
 * @package Modules\UserFund\Models
 */
class Fund extends Model implements Transformable, IModelAccess
{
    use ModelAccess;

    use ErrorTrait;

    protected $table = "user_fund";

    protected $fillable = [
        "user_id", "amount", "locked", "total_earn", "total_pay", "total_withdraw", "total_recharge",
        "created_at", "updated_at"
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

    public function prepareWithdraw($amount)
    {
        return $this->update([
            "amount" => $this->amount - $amount,
            "locked" => $this->locked + $amount
        ]);
    }

    public function withdraw($amount)
    {
        return $this->update([
            "locked" => $this->locked - $amount,
            "total_withdraw" => $this->total_withdraw + $amount
        ]);
    }

    public function cancelWithdraw($amount)
    {
        return $this->update([
            "locked" => $this->locked - $amount,
            "amount" => $this->amount + $amount,
        ]);
    }

    public function recharge($amount)
    {
        return $this->update([
            "amount" => $this->amount + $amount,
            "total_recharge" => $this->total_recharge + $amount
        ]);
    }

    public function earn($amount)
    {
        return $this->update([
            "amount" => $this->amount + $amount,
            "total_earn" => $this->total_earn + $amount
        ]);
    }

    public function preparePay($amount)
    {
        return $this->update([
            "amount" => $this->amount - $amount,
            "locked" => $this->locked + $amount
        ]);
    }

    public function pay($amount)
    {
        return $this->update([
            "locked" => $this->locked - $amount,
            "total_pay" => $this->total_pay + $amount
        ]);
    }

    public function cancelPay($amount)
    {
        return $this->update([
            "locked" => $this->locked - $amount,
            "amount" => $this->amount + $amount,
        ]);
    }
}

