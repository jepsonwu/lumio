<?php
use Prettus\Repository\Database\Eloquent\Model;
use App\Components\BootstrapHelper\Widgets\ButtonWidget;

/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 上午11:04
 *
 * @var ButtonWidget $widget
 * @var Model        $model
 */
?>

<button
        type="<?= $widget->type ?>"
        id="<?= $widget->styleClass->id ?>"
        class="<?= $widget->styleClass->class ?>"
        onclick="<?php if ($widget->onClick) {
            $func = $widget->onClick;
            echo $func($model);
        } ?>"
        style="<?= $widget->styleClass->style ?>"
    <?= $widget->getAttrsString(); ?>
> <?= $widget->title ?>
</button>
