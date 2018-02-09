<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午1:31
 */

namespace App\Components\BootstrapHelper\Widgets\Form;


class SelectFieldWidget extends FieldWidget
{
    const TYPE_KEY = 'select';

    public $dataColumn;

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

