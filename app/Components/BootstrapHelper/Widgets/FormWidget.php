<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 上午11:35
 */

namespace App\Components\BootstrapHelper\Widgets;

use Prettus\Repository\Database\Eloquent\Model;
use App\Components\BootstrapHelper\Widgets\Form\FormField;

class FormWidget extends BaseWidget
{
    public $method  = 'POST';
    public $enctype = 'multipart/form-data';

    /** @var Model */
    public $model;

    public $action     = '';
    public $actionType = '';

    public $onSubmit = '';

    public $csrf = true;

    public function init()
    {
        ! $this->styleClass->class && $this->styleClass->class = '';
        $this->styleClass->class = 'form ' . $this->styleClass->class;

        $this->actionType = $this->actionType ? $this->actionType :
            $this->model->exists() ? 'create' : 'edit';
    }

    public function render()
    {
        throw new \RuntimeException('not supported');
    }

    public function field($fieldName, $opts = [])
    {
        $field             = new FormField($opts);
        $field->fieldName  = $fieldName;
        $field->fieldValue = $this->model->$fieldName;
        ! $field->fieldLabel && $field->fieldLabel = $this->model->getLabel($fieldName);
        $field->model = $this->model;

        return $field;
    }

    public function button($type = 'submit', $label = '提交', $options = [])
    {
        $button = new ButtonWidget(
            array_merge([
                'title'      => $label,
                'type'       => $type,
                'styleClass' => new StyleClass([
                    'class' => 'btn btn-primary',
                    //                'style' => 'margin-left: 19%',
                ]),
            ], $options));

        return $button;
    }

    public function begin()
    {
        ob_start();
        echo <<<EOT
    <form style='{$this->styleClass->style}'
            id='{$this->styleClass->id}'
            class='{$this->styleClass->class}'
            action='{$this->action}'
            method='{$this->method}' 
            enctype='{$this->enctype}'
            onSubmit='{$this->onSubmit}'
            >
    
EOT;
        if ($this->csrf) {
            echo csrf_field();
        }
        echo $this->renderError();
    }

    public function renderError()
    {
        return $this->renderWidget('form_error', [
            'model' => $this->model,
        ]);
    }

    public function end()
    {
        echo <<<EOT
        </form>
EOT;
        $c = ob_get_clean();
        echo $c;
    }
}
