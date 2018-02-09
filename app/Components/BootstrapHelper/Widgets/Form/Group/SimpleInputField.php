<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/14
 * Time: 17:39
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Form\Group;

use App\Components\BootstrapHelper\Str;

/**
 * Class SimpleInputField
 *
 * @package App\Components\BootstrapHelper\Widgets\Form\Group
 */
class SimpleInputField extends AbstractFormField
{
    public function render()
    {
        $fieldname = array_get($this->meta, 'fieldname') ?: $this->getName();

        $template = '
            <label class="control-label" for=":name">:title</label>
            <input type=":type" class="form-control" name=":fieldname" id=":name" value=":value" placeholder=":placeholder">
        ';

        $this->meta += [
            'value' => $this->fieldValue($this->getName()),
        ];

        $meta = array_merge($this->meta, [
            'fieldname' => $fieldname,
        ]);

        $fragment = Str::bind($template, $meta);

        return $this->wrap($fragment);
    }

    public function setType($type)
    {
        if (isset($this->meta['type'])) {
            throw new \LogicException("Type can be set only once");
        }

        $this->meta['type'] = $type;

        return $this;
    }
}