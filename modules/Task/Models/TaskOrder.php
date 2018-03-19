<?php

namespace Modules\Task\Models;

use App\Components\BootstrapHelper\ErrorTrait;
use App\Components\BootstrapHelper\IModelAccess;
use App\Components\BootstrapHelper\ModelAccess;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $user_id
 * @property int $task_id
 * @property int $task_user_id
 * @property string $order_id
 * @property int $order_status
 * @property int $latest_order_status
 * @property int $operate_time
 * @property int $created_at
 * @property string $updated_at
 *
 * Class TaskOrder
 * @package Modules\Task\Models
 */
class TaskOrder extends Model implements Transformable, IModelAccess
{
    use ModelAccess;

    use ErrorTrait;

    const STATUS_NEW = 1;
    const STATUS_WAITING = 2;
    const STATUS_DOING = 3;
    const STATUS_SELLER_CONFIRM = 4;
    const STATUS_BUYER_CONFIRM = 5;
    const STATUS_DONE = 6;
    const STATUS_CLOSE = 7;
    const STATUS_FREEZE = 8;

    protected $table = "task_order";

    protected $fillable = [
        "id", "user_id", "task_id", "task_user_id", "order_id", "order_status", "operate_time"
        , "latest_order_status", "created_at", "updated_at"
    ];

    public function transform()
    {
        $result = parent::toArray();

        //todo 倒计时提醒 商家确认、买家确认、商家完成
        unset($result['updated_at']);
        return $result;
    }

    public function whereUserId(Builder &$builder, $userId)
    {
        $builder->where("user_id", $userId);
        return $this;
    }

    public function whereTaskId(Builder &$builder, $taskId)
    {
        $builder->where("task_id", $taskId);
        return $this;
    }

    public function whereTaskUserId(Builder &$builder, $taskUserId)
    {
        $builder->where("task_user_id", $taskUserId);
        return $this;
    }

    public function verify()
    {
        return $this->update([
            "order_status" => self::STATUS_WAITING
        ]);
    }

    public function doing($orderId)
    {
        return $this->update([
            "order_status" => self::STATUS_DOING,
            "order_id" => $orderId,
            "operate_time" => time()
        ]);
    }

    public function sellerConfirm()
    {
        return $this->update([
            "order_status" => self::STATUS_SELLER_CONFIRM,
            "operate_time" => time()
        ]);
    }

    public function buyerConfirm()
    {
        return $this->update([
            "order_status" => self::STATUS_BUYER_CONFIRM,
            "operate_time" => time()
        ]);
    }

    public function done()
    {
        return $this->update([
            "order_status" => self::STATUS_DONE
        ]);
    }

    public function close()
    {
        return $this->update([
            "order_status" => self::STATUS_CLOSE
        ]);
    }

    public function freeze()
    {
        return $this->update([
            "order_status" => self::STATUS_FREEZE,
            "latest_order_status" => $this->order_status
        ]);
    }

    public function unFreeze()
    {
        return $this->update([
            "order_status" => $this->latest_order_status,
        ]);
    }

    public function isAllowClose()
    {
        return $this->order_status < self::STATUS_CLOSE && $this->order_status > self::STATUS_WAITING;
    }

    public function isAllowFreeze()
    {
        return $this->order_status < self::STATUS_DONE;
    }

    public function isNew()
    {
        return $this->order_status == self::STATUS_NEW;
    }

    public function isTaskWaiting()
    {
        return $this->order_status < self::STATUS_DOING;
    }

    public function isWaiting()
    {
        return $this->order_status == self::STATUS_WAITING;
    }

    public function isDoing()
    {
        return $this->order_status == self::STATUS_DOING;
    }

    public function isSellerConfirm()
    {
        return $this->order_status == self::STATUS_SELLER_CONFIRM;
    }

    public function isBuyerConfirm()
    {
        return $this->order_status == self::STATUS_BUYER_CONFIRM;
    }

    public function isDone()
    {
        return $this->order_status == self::STATUS_DONE;
    }

    public function isClose()
    {
        return $this->order_status == self::STATUS_CLOSE;
    }

    public function isFreeze()
    {
        return $this->order_status == self::STATUS_FREEZE;
    }
}

