<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午1:31
 */

namespace App\Components\BootstrapHelper\Widgets\Form;


class CustomFieldWidget extends FieldWidget
{
    const TYPE_KEY = 'custom';

    public $optionsLayers;

    public function begin()
    {
        return parent::begin();
    }

    public function end()
    {
        return parent::end();
    }

    public function render()
    {
        return 'use begin and end';
    }
}
