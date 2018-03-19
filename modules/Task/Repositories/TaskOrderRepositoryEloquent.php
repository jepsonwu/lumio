<?php

namespace Modules\Task\Repositories;

use App\Validators\GlobalValidator;
use Modules\Task\Models\TaskOrder;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class TaskOrderRepositoryEloquent
 * @package namespace Modules\Task\Repositories;
 */
class TaskOrderRepositoryEloquent extends BaseRepository
{
    /**
     * @var TaskOrder
     */
    protected $model;

    public function model()
    {
        return TaskOrder::class;
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

    public function verify(TaskOrder $taskOrder)
    {
        return $taskOrder->verify();
    }

    public function doing(TaskOrder $taskOrder, $orderId, $price)
    {
        return $taskOrder->doing($orderId, $price);
    }

    public function sellerConfirm(TaskOrder $taskOrder)
    {
        return $taskOrder->sellerConfirm();
    }

    public function buyerConfirm(TaskOrder $taskOrder)
    {
        return $taskOrder->buyerConfirm();
    }

    public function done(TaskOrder $taskOrder)
    {
        return $taskOrder->done();
    }

    public function close(TaskOrder $taskOrder)
    {
        return $taskOrder->close();
    }

    public function freeze(TaskOrder $taskOrder)
    {
        return $taskOrder->freeze();
    }

    public function unFreeze(TaskOrder $taskOrder)
    {
        return $taskOrder->unFreeze();
    }
}
