<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/main-temp');
?>

<?php
$accountTypeList = [
    1 => "银行卡",
    2 => "支付宝",
    3 => "微信",
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
        <form class="form-inline" action="/admin/user-fund/recharge" method="get">
            <div class="input-group">
                <input type="text" id="user_id" name="user_id" class="form-control input-medium" placeholder="用户ID"
                       value="<?= $params['user_id'] ?? '' ?>"/>

                <select name="recharge_status" id="recharge_status" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="-1" <?= $params['recharge_status'] == '-1' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($statusList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $params['recharge_status'] ? 'selected' : '' ?>><?= $one ?></option>
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
    'modelClass' => \Modules\UserFund\Models\FundRecharge::class,
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
        'source_account_id' => [
            "name" => "source_account_id",
            "type" => "text",
            "label" => "来源账号ID"
        ],
        'source_account_type' => [
            "name" => "source_account_type",
            "type" => "text",
            "label" => "来源账号类型",
            "renderFunc" => function ($model, $modelColumn) use ($accountTypeList) {
                $key = $modelColumn->name;
                return $accountTypeList[$model->$key];
            }
        ],
        'destination_account_id' => [
            "name" => "destination_account_id",
            "type" => "text",
            "label" => "目标账号ID"
        ],
        'destination_account_type' => [
            "name" => "destination_account_type",
            "type" => "text",
            "label" => "目标账号类型",
            "renderFunc" => function ($model, $modelColumn) use ($accountTypeList) {
                $key = $modelColumn->name;
                return $accountTypeList[$model->$key];
            }
        ],
        'recharge_status' => [
            "name" => "recharge_status",
            "type" => "text",
            "label" => "充值状态",
            "renderFunc" => function ($model, $modelColumn) use ($statusList) {
                $key = $modelColumn->name;
                return $statusList[$model->$key];
            }
        ],
        'recharge_time' => [
            "name" => "recharge_time",
            "type" => "datetime",
            "label" => "充值时间"
        ],
        'verify_remark' => [
            "name" => "verify_remark",
            "type" => "text",
            "label" => "审核备注"
        ],
        'verify_time' => [
            "name" => "verify_time",
            "type" => "datetime",
            "label" => "审核时间"
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
    'buttons' => [
        new \App\Components\BootstrapHelper\Widgets\ButtonWidget([
            'title' => '审核通过',
            'styleClass' => new \App\Components\BootstrapHelper\Widgets\StyleClass([
                'class' => 'btn btn-danger list-btn-ajax-remove',
            ]),
        ]),
    ],
]) ?>

<script>
    $(function () {

    })
</script>
