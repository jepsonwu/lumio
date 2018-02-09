<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/14
 * Time: 18:18
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Form\Group;

use App\Components\BootstrapHelper\Str;

/**
 * Class ImgField
 *
 * @property string $pic
 *
 * @package App\Components\BootstrapHelper\Widgets\Form\Group
 */
class ImgField extends AbstractFormField
{
    public function render()
    {
        $template = '
            <label for=":name" class="control-label">:title</label>
            <input type="file" class="form-control" name=":name" id=":name" />';

        $this->classes[] = 'col-sm-6';
        $input           = $this->wrap(Str::bind($template, $this->meta));

        $template = '
            <div class="col-sm-6">
                <div class="pull-right">
                    <img id=":name-preview" src=":src" class="img-responsive" style="height: 150px">
                </div>
            </div>';

        $preview = Str::bind($template, ['name' => $this->getName(), 'src' => $this->fieldValue($this->pic)]);

        return $input . $preview;
    }

    public function addClass($class)
    {
        // 不接受外部 class 的设置
    }
}