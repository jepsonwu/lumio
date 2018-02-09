<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/11
 * Time: 上午11:15
 *
 * @var $widget App\Components\BootstrapHelper\Widgets\FormWidget
 */
?>
<?php
if (!$errors = $widget->model->getErrors()) {
    return;
}
?>
<div class="alert alert-danger">
    <?php foreach ($errors as $key => $error): ?>
        <?= ($key !== 'global' ? $key. ' : ' : '') . $error ?>
    <?php endforeach ?>
</div>
