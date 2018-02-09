<?php
/**
 * @var TableCheckboxRenderFunc $widget
 */
use App\Components\BootstrapHelper\Widgets\Func\TableCheckboxRenderFunc;
?>

<label>
    <input type="checkbox" class="table-checkbox" data-id="<?= $widget->getModel()->id ?>">
</label>
