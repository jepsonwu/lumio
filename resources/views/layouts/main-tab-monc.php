<?= $this->layout('layouts/main') ?>

<header class="navbar-header" style="background: rgba(91, 192, 222, 0.44); margin: 10px 0;">
    <ul class="nav nav-tabs">
        <li><a href="/admin/monc/data-print-pie" class="nav-link">打印情况</a></li>
        <li><a href="/admin/monc/machine-rate" class="nav-link">版本分布</a></li>
        <li><a href="/admin/monc/data-error-rate" class="nav-link">故障率</a></li>
        <li><a href="/admin/monc/active" class="nav-link">日活信息</a></li>
        <li><a href="/admin/monc/data-error-rate?machine=B0212761,B0212760" target="_blank" class="nav-link">监控 K11</a></li>
        <li><a href="/admin/monc/data-error-rate?machine=R030005,R030010" target="_blank" class="nav-link">监控楼下机器</a></li>
        <li><a href="/admin/monc/data-error-rate?machine=R030032,R030031,R030030,R030029,R030028,R030027,R030026,R030025,R030024,R030023,R030022,R030021,R030020,R030019,R030018,R030017,R030016,R030015,R030014,R030013,R030012,R030011,R030010,R030009,R030008,R030007,R030006,R030005,R030003,R030002" target="_blank" class="nav-link">监控 2.0机器</a></li>

        <li><a href="/admin/monc/data-error" class="nav-link">错误列表</a></li>
        <li><a href="/admin/monc/tool" class="nav-link">小工具</a></li>
        <li><a href="/admin/monc/paster-live" class="nav-link">参数配置</a></li>
    </ul>
</header>

<?= $this->section('content') ?>
