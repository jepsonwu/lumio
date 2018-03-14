<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/main-temp');
?>

<?php
$statusList = [
    0 => "无效",
    1 => "有效",
];
?>

<div class="well clearfix">
    <div class="row" style="margin-top: 20px">
        <form class="form-inline" action="/admin/user-fund/account" method="get">
            <div class="input-group">
                <input type="text" id="user_id" name="user_id" class="form-control input-medium" placeholder="用户ID"
                       value="<?= $params['user_id'] ?? '' ?>"/>

                <select name="account_status" id="account_status" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="-1" <?= $params['account_status'] == '-1' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($statusList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $params['account_status'] ? 'selected' : '' ?>><?= $one ?></option>
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
    'modelClass' => \Modules\UserFund\Models\Account::class,
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
        'real_name' => [
            "name" => "real_name",
            "type" => "text",
            "label" => "真实姓名"
        ],
        'id_card' => [
            "name" => "id_card",
            "type" => "text",
            "label" => "身份证号"
        ],
        'bank_card' => [
            "name" => "bank_card",
            "type" => "text",
            "label" => "银行卡号"
        ],
        'bank' => [
            "name" => "bank",
            "type" => "text",
            "label" => "银行名称"
        ],
        'bankfiliale' => [
            "name" => "bankfiliale",
            "type" => "text",
            "label" => "支行名称"
        ],
        'account_status' => [
            "name" => "account_status",
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
]) ?>

<script>
    $(function () {

    })
</script>
