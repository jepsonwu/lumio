<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午5:10
 */

namespace App\Components\BootstrapHelper;


trait ErrorTrait
{
    protected $errors;

    public function addError($key, $val)
    {
        $this->errors[$key] = $val;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getErrorsAsString()
    {
        return implode("\r\n", array_map(function($one){
            return $one;
        }, $this->errors));
    }

    public function getError($key)
    {
        return isset($this->errors[$key]) ? $this->errors[$key] : null;
    }
}
