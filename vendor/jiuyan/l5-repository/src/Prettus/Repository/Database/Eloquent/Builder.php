<?php

namespace Prettus\Repository\Database\Eloquent;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder as ElBuilder;

class Builder extends ElBuilder
{
    protected $model;

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * 查询必须包含分表字段
     * @param $columns
     * @return bool
     */
    public function isValidSliceColumns($columns)
    {
        $columns = (array)$columns;
        if ($this->getModel()->isEnableSlice()
            && !in_array($this->getModel()->getSliceField(), $columns)
        ) {
            throw new SliceTableException("invalid query for slice table,there aren't slice field in query columns");
        }

        return true;
    }

    /**
     *
     * 分表字段不支持where in\not in 查询
     * @param $column
     * @param $value
     * @return bool
     */
    public function isValidSliceWhereIn($column, $value)
    {
        if ($this->getModel()->isEnableSlice() && $column == $this->getModel()->getSliceField()
            && (is_array($value) || $value instanceof Arrayable)
        ) {
            throw new SliceTableException("invalid where in query for slice table");
        }

        return true;
    }

    /**
     * 重置分表表名
     * @param $column
     * @param null $operator
     * @param null $value
     * @return bool
     */
    public function resetSliceTableForWhere($column, $operator = null, $value = null)
    {
        if (!$this->getModel()->isEnableSlice()) {
            return true;
        }

        $count = count(func_get_args());
        $sliceField = $this->getModel()->getSliceField();
        $includeSliceField = false;
        $sliceValue = '';

        switch ($count) {
            case 1:
                isset($column[$sliceField]) && ($includeSliceField = true && $sliceValue = $column[$sliceField]);
                break;
            case 2:
                $column == $sliceField && ($includeSliceField = true && $sliceValue = $operator);
                break;
            case 3:
            case 4:
                $column == $sliceField && ($includeSliceField = true && $sliceValue = $value);
                break;
        }

        if ($includeSliceField) {
            $this->isValidSliceWhereIn($sliceField, $sliceValue);
            $this->query->from($this->getModel()->setSliceValue($sliceValue)->getSliceTable());
        }

        return true;
    }


    /**
     * Add a basic where clause to the query.
     *
     * @param  string|array|\Closure $column
     * @param  string $operator
     * @param  mixed $value
     * @param  string $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column instanceof \Closure) {
            if ($this->getModel()->isEnableSlice()) {
                throw new \RuntimeException("Slice Table Not Support Closure");
            }

            $query = $this->getModel()->newQueryWithoutScopes();

            $column($query);

            $this->query->addNestedWhereQuery($query->getQuery(), $boolean);
        } else {
            $this->resetSliceTableForWhere(...func_get_args());

            $this->query->where(...func_get_args());
        }

        return $this;
    }

    public function find($id, $columns = ['*'])
    {
        $this->isValidSliceColumns($this->getModel()->getKeyName());

        if ($this->getModel()->isEnableSlice() && (is_array($id) || $id instanceof Arrayable)
        ) {
            $result = [];

            foreach ($id as $single) {
                $result[] = parent::find($single, $columns);
            }

            return $result;
        }

        return parent::find(...func_get_args());
    }

    public function findMany($ids, $columns = ['*'])
    {
        if (empty($ids)) {
            return $this->getModel()->newCollection();
        }

        return $this->find($ids, $columns);
    }
}
