<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/6
 * Time: 上午12:32
 */
use App\Components\BootstrapHelper\Widgets\ListView;

/**
 * @var ListView $widget
 * @var array[]  $columns
 */
?>

<thead>
<tr>
    <?php
    ! $columns && $columns = [];
    foreach ($columns as $one):
        /** @var $one App\Components\BootstrapHelper\Widgets\ModelColumn */
        ?>
        <th>
            <?= $one->getLabel() ?>
        </th>
    <?php endforeach; ?>
    <?php if ($widget->buttons): ?>
        <th>操作</th>
    <?php endif; ?>
</tr>
</thead>
