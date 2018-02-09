<?php

namespace App\Components\BootstrapHelper\Widgets\Form;

/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午12:31
 */
abstract class FieldWidget extends \App\Components\BootstrapHelper\Widgets\BaseWidget
{
    /** @var FormField */
    public $formField;

    /** @var string */
    public $label;

    public function renderLabel()
    {
        return $this->renderWidget('field_label', ['widget' => $this]);
    }

    public function renderInfo()
    {
        return $this->renderWidget('field_info');
    }

    public function renderError()
    {
        return $this->renderWidget('field_error');
    }

    /**
     * @return $this
     */
    public function begin()
    {
        $class = $this->styleClass->class;
        if ($error = $this->formField->getError()) {
            $class .= ' has-error';
        }
        $c = <<<EOT
        <div class="{$class}">
EOT;

        return $c;
    }

    public function end()
    {
        $c = <<<EOT
    </div>
EOT;

        return $c;
    }

    public function init()
    {
        $id                   = $this->getFieldId();
        $this->styleClass->id = $id;
        ! $this->styleClass->class && $this->styleClass->class = 'form-group';
    }

    private $uniqId;

    /**
     * @return string
     */
    public function getFieldId()
    {

        ! $this->uniqId && $this->uniqId = uniqid();
        $ids = [
            $this->formField->fieldName,
            $this->uniqId,
        ];

        $id = implode('-', $ids);

        return $id;
    }
}
