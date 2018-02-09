<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/main-temp');
?>

<?php
$selectOperator = 0;
$operatorList = [
    1 => "完成"
];
?>

<div class="well clearfix">
    <div class="row" style="margin-top: 20px">
        <form class="form-inline" action="/admin/demo" method="get">
            <div class="input-group">
                <input type="text" id="keyword" name="keyword" class="form-control" placeholder="标题或备注"
                       value="<?= \Request::input('keyword') ?? '' ?>"/>

                <select name="operator" id="select-change-operator" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="0" <?= $selectOperator == '0' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($operatorList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $selectOperator ? 'selected' : '' ?>><?= $one ?></option>
                    <?php } ?>
                </select>

                <input type="date" id="start" name="start"
                       class="form-control"
                       style="width:200px;margin-right: 20px;"
                       placeholder="开始时间,如 2017-09-01"
                       value="<?= \Request::input('start') ?? '' ?>"/>

                <input type="date" id="end" name="end"
                       class="form-control"
                       style="width:200px;margin-right: 20px;"
                       placeholder="结束时间,如 2017-09-02"
                       value="<?= \Request::input('end') ?? '' ?>"/>

                <span class="input-group-btn"><button type="submit" class="btn btn-default">查询</button></span>
            </div>
            <a href="<?= url('admin/demo/create') ?>" class="btn btn-success pull-right">新建</a>

        </form>
    </div>
</div>

<?= new \App\Components\BootstrapHelper\Widgets\ListView([
    'modelClass' => \Modules\Account\Models\User::class,
    'list' => $list,
    'columns' => [
        'id' => [
            "name" => "id",
            "type" => "text",
            "label" => "ID"
        ],
        'username' => [
            "name" => "username",
            "type" => "text",
            "label" => "名称"
        ],
    ],
    'buttonsStyleClass' => new \App\Components\BootstrapHelper\Widgets\StyleClass([
        'style' => 'width: 150px',
    ]),
    'buttons' => [
        new \App\Components\BootstrapHelper\Widgets\ButtonWidget([
            'title' => '查看',
            'onClick' => function ($model) {
                return "window.location='" . url('/admin/demo/' . $model->id) . "'";
            },
        ]),
        new \App\Components\BootstrapHelper\Widgets\ButtonWidget([
            'title' => '编辑',
            'onClick' => function ($model) {
                return "window.location='" . url('/admin/demo/edit/' . $model->id) . "'";
            },
            'styleClass' => new \App\Components\BootstrapHelper\Widgets\StyleClass([
                'class' => 'btn btn-info',
            ]),
        ]),
        new \App\Components\BootstrapHelper\Widgets\ButtonWidget([
            'title' => '删除',
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
