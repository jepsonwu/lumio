<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/9
 * Time: 16:08
 *
 * @var ItemListRenderFunc $widget
 *
 * @author limi
 */
use App\Components\BootstrapHelper\Widgets\Func\ItemListRenderFunc;
?>

<ul class="item-list">
    <?php foreach ($widget->getList() as $item): ?>
        <li class="<?= $item['class'] ?? '' ?>">
            <strong><?= $item['name'] ?></strong>
            <span class="<?= $item['price'] ? 'price' : '' ?>"><?= $item['value'] ?></span>
        </li>
    <?php endforeach; ?>
</ul>
