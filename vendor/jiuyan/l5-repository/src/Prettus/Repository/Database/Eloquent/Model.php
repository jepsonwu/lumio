<?php

namespace Prettus\Repository\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as ElModel;
use Prettus\Repository\Database\Query\Builder as QueryBuilder;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Prettus\Repository\Database\Query\Builder
 */
abstract class Model extends ElModel
{
    private $sliceValue = -1;

    /**
     * 默认关闭对时间戳的自动维护
     * @var bool
     */
    public $timestamps = false;

    public function getSliceValue()
    {
        if (!is_int($this->sliceValue) || $this->sliceValue < 1) {
            throw new SliceTableException("invalid slice value,maybe is not integer");
        }

        return $this->sliceValue;
    }

    public function setSliceValue($sliceValue)
    {
        $this->sliceValue = (int)$sliceValue;

        return $this;
    }

    public function getSliceValueBySliceField()
    {
        return $this->{$this->getSliceField()};
    }

    public function getSliceField()
    {
        return "";
    }

    public function getTableCount()
    {
        return 1;
    }

    public function isEnableSlice()
    {
        return ($this->getTableCount() > 1) && $this->getSliceField();
    }

    public function getSliceTable()
    {
        return parent::getTable() . '_' . $this->getSliceValue() % $this->getTableCount();
    }

    public function getQualifiedKeyName()
    {
        $table = $this->getTable();
        $table && $table .= ".";
        return $table . $this->getKeyName();
    }

    public function getTable()
    {
        if ($this->isEnableSlice()) {
            return $this->getAttributes()
                ? $this->setSliceValue($this->getSliceValueBySliceField())->getSliceTable()
                : "";
        }

        return parent::getTable();
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Prettus\Repository\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        $builder = new Builder($query);
        $query->setEloquentBuilder($builder);
        return $builder;
    }

    /**
     * @return QueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new QueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }

    public static function destroy($ids)
    {
        $object = new static();
        if (with($object)->isEnableSlice()) {
            foreach ((array)$ids as $id) {
                with($object)->find($id)->delete();
            }
        } else {
            return parent::destroy($ids);
        }

        return true;
    }

    public function beginTransaction()
    {
        $this->getModel()->getConnection()->beginTransaction();
    }

    public function rollBack()
    {
        $this->getModel()->getConnection()->rollBack();
    }

    public function commit()
    {
        $this->getModel()->getConnection()->commit();
    }
}
