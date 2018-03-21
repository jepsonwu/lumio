<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/main-temp');
?>

<?php
$statusList = [
    1 => "申请中",
    2 => "审核通过",
    3 => "进行中",
    4 => "卖家已确认",
    5 => "买家已确认",
    6 => "已完成",
    7 => "已关闭",
    8 => "冻结",
];
?>

<div class="well clearfix">
    <div class="row" style="margin-top: 20px">
        <form class="form-inline" action="/admin/task/order" method="get">
            <div class="input-group">
                <input type="text" id="user_id" name="user_id" class="form-control input-medium" placeholder="用户ID"
                       value="<?= $params['user_id'] ?? '' ?>"/>
                <input type="text" id="task_id" name="task_id" class="form-control input-medium" placeholder="任务ID"
                       value="<?= $params['task_id'] ?? '' ?>"/>
                <input type="text" id="task_user_id" name="task_user_id" class="form-control input-medium"
                       placeholder="任务用户ID"
                       value="<?= $params['task_user_id'] ?? '' ?>"/>

                <select name="order_status" id="order_status" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="-1" <?= $params['order_status'] == '-1' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($statusList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $params['order_status'] ? 'selected' : '' ?>><?= $one ?></option>
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
    'modelClass' => \Modules\Task\Models\TaskOrder::class,
    'list' => $list,
    'columns' => [
        'id' => [
            "name" => "id",
            "type" => "text",
            "label" => "ID"
        ],
        'user_id' => [
            "name" => "user_id",
            "type" => "text",
            "label" => "用户ID"
        ],
        'task_id' => [
            "name" => "task_id",
            "type" => "text",
            "label" => "任务ID"
        ],
        'task_user_id' => [
            "name" => "task_user_id",
            "type" => "text",
            "label" => "任务用户ID"
        ],
        'price' => [
            "name" => "price",
            "type" => "text",
            "label" => "实际价格",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
            }
        ],
        'order_id' => [
            "name" => "order_id",
            "type" => "text",
            "label" => "订单号"
        ],
        'order_status' => [
            "name" => "order_status",
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
