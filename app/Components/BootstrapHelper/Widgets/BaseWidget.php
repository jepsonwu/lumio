<?php

namespace App\Components\BootstrapHelper\Widgets;

use Exception;
use App\Components\BootstrapHelper\InitTrait;
use League\Plates\Engine;

/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/5
 * Time: 下午10:29
 */
abstract class BaseWidget implements IWidget
{
    use InitTrait;

    protected $obContent;

    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var StyleClass
     */
    public $styleClass;

    /** @var array 其他 dom 属性 */
    public $attrs = [];

    public function begin()
    {
        throw new \RuntimeException('not supported for this widget' . __CLASS__);
    }

    public function end()
    {
        throw new \RuntimeException('not supported for this widget' . __CLASS__);
    }

    public function __construct($options = [])
    {
        $this->initTrait($options);

        $this->initBase();
        $this->init();
    }

    private function initBase()
    {
        !$this->engine && $this->engine = new Engine(dirname(__FILE__) . '/../resources/widgets');
        !$this->styleClass && $this->styleClass = new StyleClass();
    }

    protected function renderWidget($name, array $data = [])
    {
        $data['widget'] = $this;

        return $this->engine->render($name, $data);
    }

    public abstract function init();

    public function getAttrsString()
    {
        $attrs = $this->attrs;
        $attrs = array_map(function ($key, $one) {
            return "$key='$one'";
        }, array_keys($attrs), $attrs);

        return ' ' . implode(' ', $attrs) . ' ';
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $e) {
            \Log::error('fail to render, for', [$e]);

            return '<br/>' . $e->getMessage() . "<br/>" . $e->getTraceAsString();
        }
    }
}
