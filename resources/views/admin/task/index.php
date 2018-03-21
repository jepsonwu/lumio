<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/main-temp');
?>

<?php
$platformList = [
    1 => "电脑",
    2 => "手机",
];
$statusList = [
    1 => "等待中",
    2 => "进行中",
    3 => "已完成",
    4 => "已关闭",
];
?>

<div class="well clearfix">
    <div class="row" style="margin-top: 20px">
        <form class="form-inline" action="/admin/task" method="get">
            <div class="input-group">
                <input type="text" id="user_id" name="user_id" class="form-control input-medium" placeholder="用户ID"
                       value="<?= $params['user_id'] ?? '' ?>"/>
                <input type="text" id="store_id" name="store_id" class="form-control input-medium" placeholder="店铺ID"
                       value="<?= $params['store_id'] ?? '' ?>"/>

                <select name="platform" id="platform" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="-1" <?= $params['platform'] == '-1' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($platformList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $params['platform'] ? 'selected' : '' ?>><?= $one ?></option>
                    <?php } ?>
                </select>

                <select name="task_status" id="task_status" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="-1" <?= $params['task_status'] == '-1' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($statusList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $params['task_status'] ? 'selected' : '' ?>><?= $one ?></option>
                    <?php } ?>
                </select>

                <span class="input-group-btn"><button type="submit" class="btn btn-default">查询</button></span>
            </div>
            <!--            <a href="-->
            <? //= url('admin/seller/store/create') ?><!--" class="btn btn-success pull-right">新建</a>-->

        </form>
    </div>
</div>

<?= new \App\Components\BootstrapHelper\Widgets\ListView([
    'modelClass' => \Modules\Task\Models\Task::class,
    'list' => $list,
    'columns' => [
        'id' => [
            "name" => "id",
            "type" => "text",
            "label" => "ID"
        ],
        'task_name' => [
            "name" => "task_name",
            "type" => "text",
            "label" => "任务名称"
        ],
        'user_id' => [
            "name" => "user_id",
            "type" => "text",
            "label" => "用户ID"
        ],
        'store_id' => [
            "name" => "store_id",
            "type" => "text",
            "label" => "店铺ID"
        ],
        'goods_id' => [
            "name" => "goods_id",
            "type" => "text",
            "label" => "商品ID"
        ],
        'goods_name' => [
            "name" => "goods_name",
            "type" => "text",
            "label" => "商品名称"
        ],
        'goods_price' => [
            "name" => "goods_price",
            "type" => "text",
            "label" => "商品价格",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
            }
        ],
        'goods_image' => [
            "name" => "goods_image",
            "type" => "text",
            "label" => "关键字"
        ],
        'goods_keyword' => [
            "name" => "goods_keyword",
            "type" => "text",
            "label" => "店铺账号"
        ],
        'total_order_number' => [
            "name" => "total_order_number",
            "type" => "text",
            "label" => "总任务数"
        ],
        'waiting_order_number' => [
            "name" => "waiting_order_number",
            "type" => "text",
            "label" => "等待中任务数"
        ],
        'doing_order_number' => [
            "name" => "doing_order_number",
            "type" => "text",
            "label" => "进行中任务数"
        ],
        'finished_order_number' => [
            "name" => "finished_order_number",
            "type" => "text",
            "label" => "完成任务数"
        ],
        'platform' => [
            "name" => "platform",
            "type" => "text",
            "label" => "平台",
            "renderFunc" => function ($model, $modelColumn) use ($platformList) {
                $key = $modelColumn->name;
                return $platformList[$model->$key];
            }
        ],
        'task_status' => [
            "name" => "task_status",
            "type" => "text",
            "label" => "状态",
            "renderFunc" => function ($model, $modelColumn) use ($statusList) {
                $key = $modelColumn->name;
                return $statusList[$model->$key];
            }
        ],
        'created_at' => [
            "name" => "created_at",
            "type" => "datetime",
            "label" => "创建时间"
        ],
    ],
    'buttonsStyleClass' => new \App\Components\BootstrapHelper\Widgets\StyleClass([
        'style' => 'width: 150px',
    ]),
    'buttons' => []
]);

?>
<script>
</script>
