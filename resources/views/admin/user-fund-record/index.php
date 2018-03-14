<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/main-temp');
?>

<?php
$recordTypeList = [
    1 => "提现",
    2 => "充值",
    3 => "支出",
    4 => "收入",
];
$statusList = [
    0 => "等待审核",
    1 => "审核通过",
    2 => "审核失败",
    3 => "关闭",
];
?>

<div class="well clearfix">
    <div class="row" style="margin-top: 20px">
        <form class="form-inline" action="/admin/user-fund/record" method="get">
            <div class="input-group">
                <input type="text" id="user_id" name="user_id" class="form-control input-medium" placeholder="用户ID"
                       value="<?= $params['user_id'] ?? '' ?>"/>

                <select name="record_status" id="record_status" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="-1" <?= $params['record_status'] == '-1' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($statusList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $params['record_status'] ? 'selected' : '' ?>><?= $one ?></option>
                    <?php } ?>
                </select>

                <select name="record_type" id="record_type" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="-1" <?= $params['record_type'] == '-1' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($recordTypeList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $params['record_type'] ? 'selected' : '' ?>><?= $one ?></option>
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
    'modelClass' => \Modules\UserFund\Models\FundRecord::class,
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
        'amount' => [
            "name" => "amount",
            "type" => "text",
            "label" => "金额",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
            }
        ],
        'actual_amount' => [
            "name" => "actual_amount",
            "type" => "text",
            "label" => "实际金额",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
            }
        ],
        'commission' => [
            "name" => "commission",
            "type" => "text",
            "label" => "佣金",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
            }
        ],
        'record_type' => [
            "name" => "record_type",
            "type" => "text",
            "label" => "资金记录类型",
            "renderFunc" => function ($model, $modelColumn) use ($recordTypeList) {
                $key = $modelColumn->name;
                return $recordTypeList[$model->$key];
            }
        ],
        'record_status' => [
            "name" => "record_status",
            "type" => "text",
            "label" => "记录状态",
            "renderFunc" => function ($model, $modelColumn) use ($statusList) {
                $key = $modelColumn->name;
                return $statusList[$model->$key];
            }
        ],
        'remarks' => [
            "name" => "remarks",
            "type" => "text",
            "label" => "备注"
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
]) ?>

<script>
    $(function () {

    })
</script>
