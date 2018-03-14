<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/main-temp');
?>

<?php
$storeTypeList = [
    1 => "淘宝",
    2 => "京东",
];
$verifyStatusList = [
    0 => "待审核",
    1 => "审核通过",
    2 => "审核失败",
];
?>

<div class="well clearfix">
    <div class="row" style="margin-top: 20px">
        <form class="form-inline" action="/admin/seller/store" method="get">
            <div class="input-group">
                <input type="text" id="user_id" name="user_id" class="form-control input-medium" placeholder="用户ID"
                       value="<?= $params['user_id'] ?? '' ?>"/>
                <input type="text" id="store_name" name="store_name" class="form-control input-medium"
                       placeholder="店铺名称"
                       value="<?= $params['store_name'] ?? '' ?>"/>

                <select name="store_type" id="store_type" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="-1" <?= $params['store_type'] == '-1' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($storeTypeList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $params['store_type'] ? 'selected' : '' ?>><?= $one ?></option>
                    <?php } ?>
                </select>

                <select name="verify_status" id="verify_status" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="-1" <?= $params['verify_status'] == '-1' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($verifyStatusList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $params['verify_status'] ? 'selected' : '' ?>><?= $one ?></option>
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
    'modelClass' => \Modules\Seller\Models\Store::class,
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
        'store_type' => [
            "name" => "store_type",
            "type" => "text",
            "label" => "店铺类型",
            "renderFunc" => function ($model, $modelColumn) use ($storeTypeList) {//todo packages
                $key = $modelColumn->name;
                return $storeTypeList[$model->$key];
            }
        ],
        'store_url' => [
            "name" => "store_url",
            "type" => "text",
            "label" => "店铺url"
        ],
        'store_name' => [
            "name" => "store_name",
            "type" => "text",
            "label" => "店铺名称"
        ],
        'store_account' => [
            "name" => "store_account",
            "type" => "text",
            "label" => "店铺账号"
        ],
        'verify_status' => [
            "name" => "verify_status",
            "type" => "text",
            "label" => "审核状态",
            "renderFunc" => function ($model, $modelColumn) use ($verifyStatusList) {//todo packages
                $key = $modelColumn->name;
                return $verifyStatusList[$model->$key];
            }
        ],
        'verify_remark' => [
            "name" => "verify_remark",
            "type" => "text",
            "label" => "审核原因"
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
    'buttons' => function (\Modules\Seller\Models\Store $store) {
        if (!$store->isWaitingVerify()) {
            return [];
        }

        return [
            new \App\Components\BootstrapHelper\Widgets\ButtonWidget([
                'title' => '审核通过',
                'styleClass' => new \App\Components\BootstrapHelper\Widgets\StyleClass([
                    'class' => 'btn btn-danger list-btn-ajax-verify-pass',
                ]),
            ]),
            new \App\Components\BootstrapHelper\Widgets\ButtonWidget([
                'title' => '审核失败',
                'styleClass' => new \App\Components\BootstrapHelper\Widgets\StyleClass([
                    'class' => 'btn btn-success list-btn-ajax-verify-fail',
                ]),
                'attrs' => [
                    'data-toggle' => "modal",
                    'href' => "#verify_fail",
                ],
                'onClick' => function ($model) {
                    return "verifyFailClick('/admin/seller/store/',this)";
                },
            ]),
        ];
    }
]);

?>

<?php $this->insert('admin/common/_verify_fail'); ?>

<script>
</script>
