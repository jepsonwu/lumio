<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午1:31
 */

namespace App\Components\BootstrapHelper\Widgets\Form;


class DatetimeFieldWidget extends FieldWidget
{
    const TYPE_KEY = 'datetime';

    public function init()
    {
        parent::init();
    }

    public function render()
    {
        !$this->formField->fieldValue && $this->formField->fieldValue = date('Y-m-d H:i');

        return $this->renderWidget('field', [
            'type' => self::TYPE_KEY
        ]);
    }
}
