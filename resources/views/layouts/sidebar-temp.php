<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/6/12
 * Time: 下午5:17
 */
$sidebars = \App\Components\Helpers\SidebarHelper::getSidebars();
?>

<!--<style>-->
<!--    .page-sidebar .page-sidebar-menu{-->
<!--        color: #fff0ff;-->
<!--    }-->
<!--</style>-->

<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu" data-auto-scroll="true" data-slide-speed="200">
            <li class="start">
                <a href="/admin/">
                    <i class="icon-home"></i>
                    <span class="title">管理后台首页</span>
                </a>
            </li>

            <?php foreach ($sidebars as $groupName => $groupSidebar) { ?>
                <li data-role="dropdown" class="sticker sticker-color-dark dropdown active">
                    <a><i class="icon-list"></i><?= $groupName ?></a>
                    <ul class="sub-menu light sidebar-dropdown-menu open">
                        <?php foreach ($groupSidebar as $sidebar) { ?>
                            <li class=" ">
                                <a href="<?= $sidebar['url'] ?>">
                                    <i class="icon-home"></i>
                                    <span class="title"><?= $sidebar['title'] ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
