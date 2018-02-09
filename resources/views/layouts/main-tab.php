<?= $this->layout('layouts/main') ?>

<header class="navbar-header" style="background: rgba(91, 192, 222, 0.44); margin: 10px 0;">
    <ul class="nav nav-tabs">
        <li><a href="/admin/monc" class="nav-link">主页</a></li>
        <li><a href="/admin/home" class="nav-link">打印量最差</a></li>
        <!--            <li><a href="/admin/home/machine" class="nav-link">打印量最差</a></li>-->
        <li><a href="/admin/home/statistics?date=<?php echo date('Y-m-d') ?>" class="nav-link">状态汇总</a></li>
        <li><a href="/admin/home/status?machine=B0212303&date=<?php echo date('Y-m-d') ?>" class="nav-link">单机状态</a></li>
    </ul>
</header>

<?= $this->section('content') ?>
