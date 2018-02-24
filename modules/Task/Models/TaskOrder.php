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

    const STATUS_WAITING = 1;
    const STATUS_DOING = 2;
    const STATUS_DONE = 3;
    const STATUS_CLOSE = 4;

    protected $table = "task";

    protected $fillable = [
        "id", "user_id", "task_id", "task_user_id", "order_id", "order_status", "created_at", "updated_at"
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

    public function doing($orderId)
    {
        return $this->update([
            "order_status" => self::STATUS_DOING,
            "order_id" => $orderId
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

    public function isWaiting()
    {
        return $this->order_status == self::STATUS_WAITING;
    }

    public function isDoing()
    {
        return $this->order_status == self::STATUS_DOING;
    }

    public function isDone()
    {
        return $this->order_status == self::STATUS_DONE;
    }

    public function isClose()
    {
        return $this->order_status == self::STATUS_CLOSE;
    }
}

