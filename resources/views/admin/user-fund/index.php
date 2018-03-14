<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/main-temp');
?>

<?php
?>

<div class="well clearfix">
    <div class="row" style="margin-top: 20px">
        <form class="form-inline" action="/admin/user-fund" method="get">
            <div class="input-group">
                <input type="text" id="user_id" name="user_id" class="form-control input-medium" placeholder="用户ID"
                       value="<?= $params['user_id'] ?? '' ?>"/>

                <span class="input-group-btn"><button type="submit" class="btn btn-default">查询</button></span>
            </div>
            <!--            <a href="-->
            <? //= url('admin/seller/store/create') ?><!--" class="btn btn-success pull-right">新建</a>-->

        </form>
    </div>
</div>

<?= new \App\Components\BootstrapHelper\Widgets\ListView([
    'modelClass' => \Modules\UserFund\Models\Fund::class,
    'list' => $list,
    'columns' => [
//        'id' => [
//            "name" => "id",
//            "type" => "text",
//            "label" => "ID"
//        ],
        'user_id' => [
            "name" => "user_id",
            "type" => "text",
            "label" => "用户ID"
        ],
        'amount' => [
            "name" => "amount",
            "type" => "text",
            "label" => "余额",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
            }
        ],
        'locked' => [
            "name" => "locked",
            "type" => "text",
            "label" => "锁定余额",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
            }
        ],
        'total_earn' => [
            "name" => "total_earn",
            "type" => "text",
            "label" => "总收入",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
            }
        ],
        'total_pay' => [
            "name" => "total_pay",
            "type" => "text",
            "label" => "总支持",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
            }
        ],
        'total_withdraw' => [
            "name" => "total_withdraw",
            "type" => "text",
            "label" => "总提现",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
            }
        ],
        'total_recharge' => [
            "name" => "total_recharge",
            "type" => "text",
            "label" => "总充值",
            "renderFunc" => function ($model, $modelColumn) {
                $key = $modelColumn->name;
                return $model->$key / 100;
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
