<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/16
 * Time: 08:57
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Form\Group;

use App\Components\BootstrapHelper\Str;

/**
 * Class SelectField
 *
 * @property array  $options
 * @property string $select
 *
 * @package App\Components\BootstrapHelper\Widgets\Form\Group
 */
class SelectField extends AbstractFormField
{
    public function __construct(array $meta, $model)
    {
        parent::__construct($meta, $model);

        $this->select = $this->select ?: $this->getName();
    }

    public function render()
    {
        $template = '
            <label for=":name" class="control-label">:title</label>
            <select class="form-control" name=":name" id=":name">
                :options
            </select>';

        $this->meta['options'] = $this->getOptionsHtml();

        return $this->wrap(Str::bind($template, $this->meta));
    }

    protected function getOptionsHtml()
    {
        $html = '';

        foreach ($this->options as $value => $desc) {
            $bindings = [
                'value'    => $value,
                'desc'     => $desc,
                'selected' => $this->getSelectedText($value),
            ];

            $html = $html . Str::bind('<option value=":value":selected>:desc</option>', $bindings);
        }

        return $html;
    }

    protected function getSelectedText($value)
    {
        $selectValue = $this->fieldValue($this->select);

        return $value == $selectValue ? ' selected="selected"' : '';
    }
}