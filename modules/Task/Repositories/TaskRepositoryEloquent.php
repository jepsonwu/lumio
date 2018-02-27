<?php

namespace Modules\Task\Repositories;

use App\Validators\GlobalValidator;
use Modules\Task\Models\Task;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class TaskRepositoryEloquent
 * @package namespace Modules\Task\Repositories;
 */
class TaskRepositoryEloquent extends BaseRepository
{
    /**
     * @var Task
     */
    protected $model;

    public function model()
    {
        return Task::class;
    }

    public function validator()
    {
        return GlobalValidator::class;
    }

    /**
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Collection|static[]|
     */
    public function getByUserId($userId)
    {
        $builder = $this->model->newQuery();
        $this->model->whereUserId($builder, $userId);
        return $builder->get();
    }

    public function checkActiveByGoods($goodsId)
    {
        $builder = $this->model->newQuery();
        $this->model->whereGoodsId($builder, $goodsId)->whereActive($builder);
        return $builder->limit(1)->first();
    }

    public function checkActiveByStore($storeId)
    {
        $builder = $this->model->newQuery();
        $this->model->whereStoreId($builder, $storeId)->whereActive($builder);
        return $builder->limit(1)->first();
    }

    public function incWaitingOrder(Task $task)
    {
        return $task->incWaitingOrder();
    }

    public function decWaitingOrder(Task $task)
    {
        return $task->decWaitingOrder();
    }

    public function incDoingOrder(Task $task)
    {
        return $task->incDoingOrder();
    }

    public function decDoingOrder(Task $task)
    {
        return $task->decDoingOrder();
    }

    public function incFinishedOrder(Task $task)
    {
        return $task->incFinishedOrder();
    }

    public function closeTask(Task $task)
    {
        return $task->closeTask();
    }
}
