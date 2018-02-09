<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/9
 * Time: 20:13
 *
 * @var ImgRenderFunc $widget
 *
 * @author limi
 */
use App\Components\BootstrapHelper\Widgets\Func\ImgRenderFunc;

?>

<a href="<?= $widget->getLink() ?>">
    <img src="<?= $widget->getPicField() ?>" class="img-responsive" style="max-height: 120px">
</a>