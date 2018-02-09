<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/6/20
 * Time: 下午9:20
 *
 * @var                                          $model  \Prettus\Repository\Database\Eloquent\Model
 * @var App\Components\BootstrapHelper\Widgets\ModelColumn[] $columns
 * @var \App\Components\BootstrapHelper\Widgets\ModelView $widget
 */

?>

<table class="table">
    <thead>
    <tr data-id="<?= $model->getPrimaryKeyValue() ?>"
        class="<?= $model->getTrClass() ?>">
        <th>字段</th>
        <th>内容</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($columns as $one) {
        /** @var $one App\Components\BootstrapHelper\Widgets\ModelColumn */
        $key = $one->name;
        ?>
        <tr class="column-<?= $one->name ?>">
            <td>
                <?= $one->getLabel() ?>
            </td>
            <td>
                <?php $value = $one->getValue($model);
                echo is_array($value) ? print_r($value, 1) : $value ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="2">
            <?php if ($widget->buttons): ?>
                <?php foreach ($widget->buttons as $one):
                    /** @var $one App\Components\BootstrapHelper\Widgets\ButtonWidget */
                    ?>
                    <span><?= $one->setModel($model)->render(); ?></span>
                <?php endforeach; ?>
            <?php endif; ?>
        </td>
    </tr>
    </tfoot>
</table>