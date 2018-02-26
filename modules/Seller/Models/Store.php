<?php

namespace Modules\Seller\Models;

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
 * @property int $store_type
 * @property string $store_url
 * @property string $store_name
 * @property string $store_account
 * @property int $verify_status
 * @property int $store_status
 * @property int $created_at
 * @property string $updated_at
 *
 * Class Store
 * @package Modules\Seller\Models
 */
class Store extends Model implements Transformable, IModelAccess
{
    use ModelAccess;

    use ErrorTrait;

    const TYPE_TAOBAO = 1;
    const TYPE_JD = 2;

    const VERIFY_STATUS_WAITING = 0;
    const VERIFY_STATUS_PASSED = 1;
    const VERIFY_STATUS_FAILED = 2;

    protected $table = "store";

    protected $fillable = [
        "id", "user_id", "store_type", "store_url", "store_name", "store_account", "verify_status",
        "store_status", "created_at", "updated_at"
    ];

    public function transform()
    {
        $result = parent::toArray();

        unset($result['updated_at'], $result['store_status']);
        return $result;
    }

    public function whereUserId(Builder &$builder, $userId)
    {
        $builder->where("user_id", $userId);
        return $this;
    }

    public function whereVerifyValid(Builder &$builder)
    {
        $builder->where("verify_status", self::VERIFY_STATUS_PASSED);
        return $this;
    }

    public function whereValid(Builder &$builder)
    {
        $builder->where("store_status", GlobalDBConstant::DB_TRUE);
        return $this;
    }

    public function isWaitingVerify()
    {
        return $this->verify_status == self::VERIFY_STATUS_WAITING;
    }

    public function isPassed()
    {
        return $this->verify_status == self::VERIFY_STATUS_PASSED;
    }

    public function isValid()
    {
        return $this->store_status == GlobalDBConstant::DB_TRUE;
    }

    public function deleteStore()
    {
        return $this->update([
            "store_status" => GlobalDBConstant::DB_FALSE
        ]);
    }

    public function pass()
    {
        return $this->update([
            "verify_status" => self::VERIFY_STATUS_PASSED
        ]);
    }

    public function fail()
    {
        return $this->update([
            "verify_status" => self::VERIFY_STATUS_FAILED
        ]);
    }

    public function isTaobao()
    {
        return $this->store_type == self::TYPE_TAOBAO;
    }

    public function isJd()
    {
        return $this->store_type == self::TYPE_JD;
    }
}

