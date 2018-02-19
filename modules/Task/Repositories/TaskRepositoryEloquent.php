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

    public function incFinishedOrderNumber(Task $task)
    {
        return $task->incFinishedOrderNumber();
    }

    public function incDoingOrderNumber(Task $task)
    {
        return $task->incDoingOrderNumber();
    }

    public function decDoingOrderNumber(Task $task)
    {
        return $task->decDoingOrderNumber();
    }

    public function doingTask(Task $task)
    {
        return $task->doingTask();
    }

    public function doneTask(Task $task)
    {
        return $task->doneTask();
    }

    public function closeTask(Task $task)
    {
        return $task->closeTask();
    }
}
