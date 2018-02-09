<?php

namespace App\Components\BootstrapHelper\Widgets\Func;

use Prettus\Repository\Database\Eloquent\Model;
use App\Components\BootstrapHelper\Widgets\ModelColumn;

/**
 * Created by PhpStorm.
 * Date: 16/9/9
 * Time: 15:45
 *
 * @author limi
 */
interface RenderFunc
{
    /**
     * @param Model       $model
     * @param ModelColumn $column
     *
     * @return string
     */
    public function invoke($model, $column);
}