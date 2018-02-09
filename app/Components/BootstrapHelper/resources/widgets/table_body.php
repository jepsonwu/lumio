<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/6
 * Time: 上午12:32
 */

/**
 * @var App\Components\BootstrapHelper\Widgets\ModelColumn[] $columns
 * @var Prettus\Repository\Database\Eloquent\Model[] $list
 * @var App\Components\BootstrapHelper\Widgets\ListView $widget
 */
?>

<tbody>
<?php
!$list && $list = [];
foreach ($list as $key => $model):
    ?>
    <tr data-id="<?= $model->getKey() ?>"
        class="<?= $model->getTrClass() ?>">
        <?php
        !$columns && $columns = [];
        foreach ($columns as $one):
            /** @var $one App\Components\BootstrapHelper\Widgets\ModelColumn */
            $key = $one->name;
            ?>
            <td style="max-width: 150px" class="column-<?= $one->name ?>">
                <?= $one->getValue($model) ?>
            </td>
        <?php endforeach; ?>

        <?php if ($widget->buttons): ?>
            <td <?= $widget->buttonsStyleClass ? $widget->buttonsStyleClass : '' ?>>
                <?php foreach ($widget->buttons as $one):
                    /** @var $one App\Components\BootstrapHelper\Widgets\ButtonWidget */
                    ?>
                    <span><?= $one->setModel($model)->render(); ?></span>
                <?php endforeach; ?>
            </td>
        <?php endif; ?>

    </tr>
<?php endforeach ?>
</tbody>
