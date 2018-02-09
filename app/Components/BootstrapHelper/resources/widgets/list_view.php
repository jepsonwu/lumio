<?php

/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/5
 * Time: 下午10:48
 */

/**
 * @var string $header;
 * @var string $body;
 * @var string $footer;
 * @var App\Components\BootstrapHelper\Widgets\ListView $widget;
 */

?>
<table class="<?= $widget->styleClass->class ?>"
    style='<?= $widget->styleClass->style ?>'
    >
    <?= $header ?>
    <?= $body ?>
</table>
<div class="" style="text-align: right;">
    <?= $footer ?>
</div>
