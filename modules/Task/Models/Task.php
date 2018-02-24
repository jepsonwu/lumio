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
 * @property int $store_id
 * @property int $goods_id
 * @property string $goods_name
 * @property int $goods_price
 * @property string $goods_image
 * @property string $goods_keyword
 * @property int $total_order_number
 * @property int $finished_order_number
 * @property int $doing_order_number
 * @property int $waiting_order_number
 * @property int $platform
 * @property int $task_status
 * @property int $created_at
 * @property string $updated_at
 *
 * Class Task
 * @package Modules\Task\Models
 */
class Task extends Model implements Transformable, IModelAccess
{
    use ModelAccess;

    use ErrorTrait;

    const PLATFORM_PC = 1;
    const PLATFORM_MOBILE = 2;

    const STATUS_WAITING = 1;
    const STATUS_DOING = 2;
    const STATUS_DONE = 3;
    const STATUS_CLOSE = 4;

    protected $table = "task";

    protected $fillable = [
        "id", "user_id", "store_id", "goods_id", "goods_name", "goods_price", "goods_image",
        "goods_keyword", "total_order_number", "finished_order_number", "doing_order_number",
        "waiting_order_number", "platform", "task_status", "created_at", "updated_at"
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

    public function incWaitingOrder()
    {
        $attributes = [
            "waiting_order_number" => $this->waiting_order_number + 1
        ];
        $this->isWaiting() && $attributes['task_status'] = self::STATUS_DOING;

        return $this->update($attributes);
    }

    public function decWaitingOrder()
    {
        return $this->decrement("waiting_order_number");
    }

    public function incDoingOrder()
    {
        $attributes = [
            "doing_order_number" => $this->doing_order_number + 1,
            "waiting_order_number" => $this->waiting_order_number - 1
        ];

        return $this->update($attributes);
    }

    public function decDoingOrder()
    {
        return $this->decrement("doing_order_number");
    }

    public function incFinishedOrder()
    {
        $attributes = [
            "finished_order_number" => $this->finished_order_number + 1,
            "doing_order_number" => $this->doing_order_number - 1
        ];

        $attributes['finished_order_number'] == $this->total_order_number
        && $attributes['task_status'] = self::STATUS_DONE;

        return $this->update($attributes);
    }

    public function closeTask()
    {
        return $this->update([
            "task_status" => self::STATUS_CLOSE
        ]);
    }

    public function isAllowApply()
    {
        return ($this->isWaiting() || $this->isDoing()) && $this->hasAvailableTotalOrderNumber();
    }

    public function hasAvailableTotalOrderNumber()
    {
        return $this->finished_order_number >
            (
                $this->waiting_order_number + $this->doing_order_number + $this->finished_order_number
            );
    }

    public function isWaiting()
    {
        return $this->task_status == self::STATUS_WAITING;
    }

    public function isDoing()
    {
        return $this->task_status == self::STATUS_DOING;
    }

    public function isDone()
    {
        return $this->task_status == self::STATUS_DONE;
    }

    public function isClose()
    {
        return $this->task_status == self::STATUS_CLOSE;
    }
}

