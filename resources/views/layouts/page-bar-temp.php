<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/6/12
 * Time: 下午5:36
 */
/** @var $this \League\Plates\Template\Template */
?>

<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
<h3 class="page-title">
    <?= $__title ?>
    <small><?= $__sub_title ?></small>
</h3>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i>
        </li>
        <?php foreach ($__breads as $one) { ?>
            <li>
                <a href="<?= $one[0] ?>"><?= $one[1] ?></a>
                <i class="fa fa-angle-right"></i>
            </li>
        <?php } ?>
        <li>
            <?= $__sub_title ?>
        </li>
    </ul>
    <div class="page-toolbar">
        <!--        <div id="dashboard-report-range" class="pull-right tooltips btn btn-fit-height grey-salt" data-placement="top"-->
        <!--             data-original-title="Change dashboard date range">-->
        <!--            <i class="icon-calendar"></i>&nbsp; <span class="thin uppercase visible-lg-inline-block"></span>&nbsp; <i-->
        <!--                    class="fa fa-angle-down"></i>-->
        <!--        </div>-->
    </div>
</div>

