<?php
/**
 * Created by PhpStorm.
 * Date: 2016/12/28
 * Time: 10:48
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Func;

/**
 * Class TableCheckboxRenderFunc
 *
 * @package App\Components\BootstrapHelper\Widgets\Func
 */
class TableCheckboxRenderFunc extends AbstractRenderFunc
{
    public function invoke($model, $column)
    {
        return $this->renderWidget('table_checkbox');
    }
}