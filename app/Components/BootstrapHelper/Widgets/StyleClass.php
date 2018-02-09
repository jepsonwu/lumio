<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 上午11:16
 */

namespace App\Components\BootstrapHelper\Widgets;


class StyleClass
{

    public $class;
    public $onClick;
    public $id;
    public $title;
    public $style;
    public $placeHolder;

    function __construct($options = [])
    {
        foreach ($options as $key => $val) {
            if (! property_exists($this, $key)) {
                throw new \RuntimeException('not found property exception' . print_r([$key, $val], 1));
            }
            $this->$key = $val;
        }
    }

    public function __toString()
    {
        return "
        style='{$this->style}' 
        onClick='{$this->onClick}' 
        title='{$this->title}' 
        id='{$this->id}' 
        class='{$this->class}' 
        placeHolder='{$this->placeHolder}' 
        ";
    }
}
