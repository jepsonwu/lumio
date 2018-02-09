<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/14
 * Time: 17:38
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Form\Group;

use App\Components\BootstrapHelper\Widgets\IWidget;

interface FormField extends IWidget
{
    /**
     * @return string
     */
    public function render();

    /**
     * @param string $class
     *
     * @return void
     */
    public function addClass($class);
}