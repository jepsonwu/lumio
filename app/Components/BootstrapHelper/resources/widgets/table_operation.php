<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/9
 * Time: 20:28
 *
 * @var OperationRenderFunc $widget
 *
 * @author limi
 */
use App\Components\BootstrapHelper\Widgets\Func\OperationRenderFunc;

?>

<?php foreach ($widget->getOperations() as $operation): ?>
    <a href="<?= $operation['href'] ?>" class="<?= $operation['class'] ?>"
       <?= sprintf(' target="%s"', $operation['target'] ?? '_self') ?>
        <?php foreach ($operation['data'] as $key => $val): ?>
            <?= sprintf('data-%s="%s"', $key, $val) ?>
        <?php endforeach; ?>
    ><?= $operation['title'] ?></a>
<?php endforeach; ?>
