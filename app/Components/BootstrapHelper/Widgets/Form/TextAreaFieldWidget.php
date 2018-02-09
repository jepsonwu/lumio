<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午1:31
 */

namespace App\Components\BootstrapHelper\Widgets\Form;


class TextAreaFieldWidget extends FieldWidget
{
    const TYPE_KEY = 'textarea';

    public function init()
    {
        parent::init();
    }

    public function render()
    {
        return $this->renderWidget('field', [
            'type' => self::TYPE_KEY
        ]);
    }
}

