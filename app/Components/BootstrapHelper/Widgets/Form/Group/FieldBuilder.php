<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/14
 * Time: 14:23
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Form\Group;

use Prettus\Repository\Database\Eloquent\Model;
use App\Components\BootstrapHelper\Widgets\IWidget;

/**
 * Class FormGroupBuilder
 *
 * @package App\Components\BootstrapHelper\Widgets\Form
 */
class FieldBuilder implements IWidget
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $fields;

    /**
     * FormGroupBuilder constructor.
     *
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $placeholder
     * @param null   $fieldname
     *
     * @return FieldBuilder
     */
    public function text($name, $title, $placeholder = null, $fieldname = null)
    {
        return $this->simpleInput('text', $name, $title, $placeholder, $fieldname);
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $placeholder
     *
     * @return FieldBuilder
     */
    public function number($name, $title, $placeholder = null)
    {
        return $this->simpleInput('number', $name, $title, $placeholder);
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $relative
     *
     * @return $this
     */
    public function datetimeLocal($name, $title, $relative = '+0 days')
    {
        $config = [
            'name'        => $name,
            'title'       => $title,
            'placeholder' => '',
            'value'       => date('Y-m-d\TH:i', object_get($this->model, $name, strtotime($relative, time()))),
        ];

        $this->fields[] = (new SimpleInputField($config, $this->model))->setType('datetime-local');

        return $this;
    }

    protected function simpleInput($type, $name, $title, $placeholder = null, $fieldname = null)
    {
        $config = compact('name', 'title', 'placeholder', 'fieldname');

        $this->fields[] = (new SimpleInputField($config, $this->model))->setType($type);

        return $this;
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $pic
     * @param null   $placeholder
     *
     * @return FieldBuilder
     */
    public function img($name, $title, $pic, $placeholder = null)
    {
        $config = compact('name', 'title', 'pic', 'placeholder');

        $this->fields[] = new ImgField($config, $this->model);

        return $this;
    }

    /**
     * @param string   $name
     * @param string   $title
     * @param callable $checkCallback
     *
     * @return $this
     */
    public function checkbox($name, $title, $checkCallback = null)
    {
        $config = compact('name', 'title', 'checkCallback');

        $this->fields[] = new CheckField($config, $this->model);

        return $this;
    }

    /**
     * @param string $name
     * @param string $title
     * @param array  $options
     * @param null   $select
     * @param null   $placeholder
     *
     * @return $this
     */
    public function select($name, $title, array $options, $select = null, $placeholder = null)
    {
        $config = compact('name', 'title', 'options', 'select', 'placeholder');

        $this->fields[] = new SelectField($config, $this->model);

        return $this;
    }

    /**
     * @return int
     */
    public function fieldsCount()
    {
        return count($this->fields);
    }

    /**
     * @return string
     */
    protected function formFieldClass()
    {
        return 'col-sm-' . (12 / $this->fieldsCount());
    }

    /**
     * @return string
     */
    public function render()
    {
        $fragments = array_reduce($this->fields, function ($fragments, FormField $field) {

            $field->addClass($this->formFieldClass());

            return $fragments = $fragments . $field->render();
        }, '');

        $template = '<div class="form-group"><div class="row">%s</div></div>';

        return sprintf($template, $fragments);
    }

    function __toString()
    {
        return $this->render();
    }
}