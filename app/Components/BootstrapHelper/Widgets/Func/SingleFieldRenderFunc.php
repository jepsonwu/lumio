<?php

namespace App\Components\BootstrapHelper\Widgets\Func;

/**
 * Created by PhpStorm.
 * Date: 16/9/9
 * Time: 15:46
 *
 * @author limi
 */
class SingleFieldRenderFunc extends AbstractRenderFunc
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var callable
     */
    protected $valueCallback;

    public function __construct($field, $valueCallback)
    {
        $this->field         = $field;
        $this->valueCallback = $valueCallback;
    }

    public function invoke($model, $column)
    {
        $value = $this->getFieldValue($this->field);

        if ($this->valueCallback) {
            return call_user_func($this->valueCallback, $value, $this->model);
        }

        return $value;
    }
}