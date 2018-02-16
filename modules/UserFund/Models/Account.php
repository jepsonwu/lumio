<?php

namespace Modules\UserFund\Models;

use App\Components\BootstrapHelper\ErrorTrait;
use App\Components\BootstrapHelper\IModelAccess;
use App\Components\BootstrapHelper\ModelAccess;
use App\Constants\GlobalDBConstant;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $user_id
 * @property string $real_name
 * @property string $id_card
 * @property string $bank_card
 * @property string $bank
 * @property string $bankfiliale
 * @property int $account_status
 * @property int $created_at
 * @property string $updated_at
 *
 * Class Account
 * @package Modules\UserFund\Models
 */
class Account extends Model implements Transformable, IModelAccess
{
    use ModelAccess;

    use ErrorTrait;

    protected $table = "user_fund_account";

    protected $fillable = [
        "id", "user_id", "real_name", "id_card", "bank_card", "bank", "bankfiliale", "account_status",
        "created_at", "updated_at"
    ];

    public function transform()
    {
        $result = parent::toArray();

        unset($result['updated_at'], $result['account_status']);
        return $result;
    }

    public function whereBankCard(Builder &$builder, $bankCard)
    {
        $builder->where("bank_card", $bankCard);
        return $this;
    }

    public function whereUserId(Builder &$builder, $userId)
    {
        $builder->where("user_id", $userId);
        return $this;
    }

    public function whereValid(Builder &$builder)
    {
        $builder->where("account_status", GlobalDBConstant::DB_TRUE);
        return $this;
    }
}

