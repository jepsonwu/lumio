<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午4:30
 */

namespace App\Components\BootstrapHelper;

interface IModelAccess
{

    public function getLabel($field);

    public function getLabels();

    public function getErrors();

    public function getError($key);

    /**
     * 该实例的描述.
     *
     * @return string
     */
    public function getTitle();

    /**
     * 列表中 tr 元素的 class.
     *
     * @return string
     */
    public function getTrClass();

    public function getPrimaryKeyValue();
}
