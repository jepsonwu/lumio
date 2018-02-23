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
}