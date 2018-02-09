<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/16
 * Time: 19:59
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Form\Group;

use App\Components\BootstrapHelper\Str;

/**
 * Class CheckField
 *
 * @property callable|null $checkCallback
 *
 * @package App\Components\BootstrapHelper\Widgets\Form\Group
 */
class CheckField extends AbstractFormField
{
    public function render()
    {
        $template = '
            <div class="checkbox" style="margin-top: 30px">
                <label class="checkbox-inline">
                    <input type="checkbox" name=":name" id=":name":checked> :title
                </label>
            </div>';

        $this->meta['checked'] = $this->resolveCheckedText();
        $fragment              = Str::bind($template, $this->meta);

        return $this->wrap($fragment);
    }

    protected function resolveCheckedText()
    {
        if ($this->checkCallback) {
            $checked = call_user_func($this->checkCallback, $this->model);
        }
        else {
            $checked = (bool) object_get($this->model, $this->getName());
        }

        return $checked ? ' checked="checked"' : '';
    }
}