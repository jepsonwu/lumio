<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/14
 * Time: 17:40
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Form\Group;

use Prettus\Repository\Database\Eloquent\Model;
use App\Components\BootstrapHelper\Str;

/**
 * Class AbstractFormField
 *
 * @property string $name
 * @property string $title
 * @property string $placeholder
 *
 * @package App\Components\BootstrapHelper\Widgets\Form\Group
 */
abstract class AbstractFormField implements FormField
{
    /**
     * @var array
     */
    protected $meta;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $classes = [];

    public function __construct(array $meta, $model)
    {
        $this->meta  = ($meta = $meta + ['helpText' => '该字段不能为空']);
        $this->model = $model;
    }

    public function addClass($class)
    {
        $this->classes[] = $class;
    }

    /**
     * @param $fragment
     *
     * @return string
     */
    protected function wrap($fragment)
    {
        $template = '<div class=":class">:fragment:helpBlock</div>';

        $helpBlock = '';
        if ($this->hasError()) {
            $this->classes[] = 'has-error';

            $helpBlock = Str::bind('<p class="help-block">:helpText</p>', $this->meta);
        }

        $bindings = [
            'class'     => implode(' ', $this->classes),
            'fragment'  => $fragment,
            'helpBlock' => $helpBlock,
        ];

        return Str::bind($template, $bindings);
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function fieldValue($name, $default = null)
    {
        if (strpos($name, '.') === false) {
            return object_get($this->model, $name, $default);
        }

        list($attribute, $key) = explode('.', $name, 2);

        $value = object_get($this->model, $attribute);

        if (is_array($value)) {
            return array_get($value, $key, $default);
        }

        return object_get($value, $key);
    }

    protected function hasError()
    {
        return (bool) $this->model->getError($this->getName());
    }

    public function getName()
    {
        return $this->meta['name'];
    }

    public function getTitle()
    {
        return $this->meta['title'];
    }

    public function getPlaceholder($default = null)
    {
        return array_get($this->meta, 'placeholder', $default);
    }

    function __get($name)
    {
        return array_get($this->meta, $name);
    }

    function __isset($name)
    {
        return array_key_exists($name, $this->meta);
    }

    function __set($name, $value)
    {
        $this->meta[$name] = $value;
    }
}