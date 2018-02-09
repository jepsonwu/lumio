<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/6
 * Time: 上午12:21
 */

namespace App\Components\BootstrapHelper\Widgets;

/**
 * Class TableHeader
 *
 * @deprecated
 *
 * @package App\Components\BootstrapHelper\Widgets
 */
class TableHeader extends BaseWidget
{
    protected $columns;

    protected $buttons;

    public function init()
    {
        // TODO: Implement init() method.
    }

    public function render()
    {
        return $this->renderWidget('table_header', [
            'columns' => $this->columns,
        ]);
    }
}
