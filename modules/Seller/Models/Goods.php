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
 * @property int $store_id
 * @property string $goods_url
 * @property string $goods_image
 * @property string $goods_price
 * @property string $goods_keywords
 * @property string $goods_name
 * @property int $goods_status
 * @property int $created_at
 * @property string $updated_at
 *
 * Class Goods
 * @package Modules\Seller\Models
 */
class Goods extends Model implements Transformable, IModelAccess
{
    use ModelAccess;

    use ErrorTrait;

    const TYPE_TAOBAO = 1;
    const TYPE_JD = 2;

    const VERIFY_STATUS_WAITING = 0;
    const VERIFY_STATUS_PASSED = 1;
    const VERIFY_STATUS_FAILED = 2;

    protected $table = "store_goods";

    protected $fillable = [
        "id", "user_id", "store_id", "goods_url", "goods_image", "goods_price", "goods_keywords",
        "goods_status", "goods_name", "created_at", "updated_at"
    ];

    public function transform()
    {
        $result = parent::toArray();

        unset($result['updated_at'], $result['goods_status']);
        return $result;
    }

    public function whereUserId(Builder &$builder, $userId)
    {
        $builder->where("user_id", $userId);
        return $this;
    }

    public function whereStoreId(Builder &$builder, $storeId)
    {
        $builder->where("store_id", $storeId);
        return $this;
    }

    public function whereValid(Builder &$builder)
    {
        $builder->where("goods_status", GlobalDBConstant::DB_TRUE);
        return $this;
    }

    public function isValid()
    {
        return $this->goods_status == GlobalDBConstant::DB_TRUE;
    }

    public function deleteGoods()
    {
        return $this->update([
            "goods_status" => GlobalDBConstant::DB_FALSE
        ]);
    }
}

