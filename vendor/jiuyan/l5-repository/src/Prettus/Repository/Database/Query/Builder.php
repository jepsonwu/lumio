<?php

namespace Prettus\Repository\Database\Query;

use Illuminate\Database\Query\Builder as QBuilder;
use Prettus\Repository\Database\Eloquent\Builder as ElBuilder;

/**
 *
 * Class Builder
 * @package Prettus\Repository\Database\Query
 */
class Builder extends QBuilder
{
    /**
     * @var ElBuilder
     */
    protected $eloquentBuilder;

    public function setEloquentBuilder(ElBuilder $builder)
    {
        $this->eloquentBuilder = $builder;
        return $this;
    }

    public function toSql()
    {
        $this->isValidSliceSql();
        return parent::toSql();
    }

    protected function isValidSliceSql()
    {
        $columns = [];
        foreach ($this->wheres as $where) {
            $columns[] = $where['column'];
        }

        $this->eloquentBuilder->isValidSliceColumns($columns);
    }

    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $this->eloquentBuilder->isValidSliceWhereIn($column, $values);
        return parent::whereIn($column, $values, $boolean, $not);
    }
}