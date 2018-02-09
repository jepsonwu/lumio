<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午4:30
 */

namespace App\Components\BootstrapHelper;

trait ModelAccess
{
    public function getLabels()
    {
        return [];
    }

    public function getLabel($field)
    {
        $labels = $this->getLabels();
        $domain = 'models';
        if (! isset($labels[$field])) {
            return trans($domain . '.' . $field, [], $domain);
        }

        return trans($labels[$field], [], $domain);
    }

    /**
     * 实例描述.
     *
     * @return string
     */
    public function getTitle()
    {
        if (isset($this->title) && $this->title) {
            return $this->title;
        }

        return isset($this->id) ? $this->id : 0;
    }

    public function getTrClass()
    {
        return '';
    }

    public function getPrimaryKeyValue()
    {
        $key = isset($this->primaryKey) ? $this->primaryKey : 'id';

        return $this->$key;
    }
}
