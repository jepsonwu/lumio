<?php

namespace App\Components\BootstrapHelper\Widgets\Func;

use Prettus\Repository\Database\Eloquent\Model;
use App\Support\Times;
use App\Components\BootstrapHelper\Widgets\ModelColumn;
use League\Plates\Engine;

/**
 * Created by PhpStorm.
 * Date: 16/9/9
 * Time: 15:48
 *
 * @author limi
 */
abstract class AbstractRenderFunc implements RenderFunc
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var ModelColumn
     */
    protected $column;

    function __invoke()
    {
        list($this->model, $this->column) = func_get_args();

        return $this->invoke($this->model, $this->column);
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param      $field
     * @param null $format
     *
     * @return string
     */
    protected function getFieldValue($field, $format = null)
    {
        $model = $this->model;

        if ($format instanceof \Closure) {
            return call_user_func($format, $model);
        }

        //        if (! array_key_exists($field, $model->getAttributes())) {
        //            throw new \RuntimeException("Unknown field {$this->model}");
        //        }
        //
        //        $value = $model->{$field};

        $value = $this->getObjectValueKeyPath($model, $field);

        switch ($format) {
            case 'time':
                return Times::format($value);
            default:
                return $value;
        }
    }

    /**
     * @param        $obj
     * @param string $key
     *
     * @return mixed
     */
    protected function getObjectValueKeyPath($obj, $key)
    {
        if (strpos($key, '.') === false) {
            return object_get($obj, $key);
        }

        list($attr, $keypath) = explode('.', $key, 2);

        $value = object_get($obj, $attr);
        if (is_array($value)) {
            return array_get($value, $keypath);
        }

        return $this->getObjectValueKeyPath($value, $keypath);
    }

    /**
     * @param string $name
     * @param array  $data
     *
     * @return string
     */
    protected function renderWidget($name, $data = [])
    {
        $engine = new Engine(dirname(__FILE__) . '/../../resources/widgets');
        $data += ['widget' => $this];

        return $engine->render($name, $data);
    }
}