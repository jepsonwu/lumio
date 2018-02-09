<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 上午11:03
 */

namespace App\Components\BootstrapHelper\Widgets;


class ButtonWidget extends BaseWidget
{
    public $onClick;
    public $title;
    public $type = 'button';

    public function init()
    {
        if (!$this->styleClass->class) {
            $this->styleClass->class = 'btn btn-primary';
        }
    }

    protected $model;

    public function setModel($model){
        $this->model = $model;
        return $this;
    }

    public function render()
    {
        return $this->renderWidget('btn', [
            'model' => $this->model,
        ]);
    }
}
