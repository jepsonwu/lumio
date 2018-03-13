<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/main-temp');
?>

<?php
$roleList = [
    0 => "普通",
    1 => "买家",
    2 => "卖家",
];
$genderList = [
    0 => "女",
    1 => "男",
    2 => "未知",
];
?>

<div class="well clearfix">
    <div class="row" style="margin-top: 20px">
        <form class="form-inline" action="/admin/account/user" method="get">
            <div class="input-group">
                <input type="text" id="mobile" name="mobile" class="form-control input-medium" placeholder="手机号"
                       value="<?= $params['mobile'] ?? '' ?>"/>

                <select name="role" id="role" class="form-control"
                        style="width: 200px;margin-right: 20px;">
                    <option value="-1" <?= $params['role'] == '-1' ? 'selected' : '' ?>>全部</option>
                    <?php foreach ($roleList as $key => $one) { ?>
                        <option value="<?= $key ?>" <?= $key == $params['role'] ? 'selected' : '' ?>><?= $one ?></option>
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
            "label" => "用户名称"
        ],
        'avatar' => [
            "name" => "avatar",
            "type" => "text",
            "label" => "头像"
        ],
        'mobile' => [
            "name" => "mobile",
            "type" => "text",
            "label" => "手机号"
        ],
        'gender' => [
            "name" => "gender",
            "type" => "text",
            "label" => "性别",
            "renderFunc" => function ($model, $modelColumn) use ($genderList) {
                $key = $modelColumn->name;
                return $genderList[$model->$key];
            }
        ],
        'qq' => [
            "name" => "qq",
            "type" => "text",
            "label" => "QQ"
        ],
        'email' => [
            "name" => "email",
            "type" => "text",
            "label" => "邮箱"
        ],
        'invited_user_id' => [
            "name" => "invited_user_id",
            "type" => "text",
            "label" => "邀请用户"
        ],
        'invite_code' => [
            "name" => "invite_code",
            "type" => "text",
            "label" => "邀请码"
        ],
        'role' => [
            "name" => "role",
            "type" => "text",
            "label" => "角色",
            "renderFunc" => function ($model, $modelColumn) use ($roleList) {
                $key = $modelColumn->name;
                return $roleList[$model->$key];
            }
        ],
        'level' => [
            "name" => "level",
            "type" => "text",
            "label" => "等级"
        ],
        'open_status' => [
            "name" => "open_status",
            "type" => "boolean",
            "label" => "任务开启状态"
        ],
        'taobao_account' => [
            "name" => "taobao_account",
            "type" => "text",
            "label" => "淘宝账号"
        ],
        'jd_account' => [
            "name" => "jd_account",
            "type" => "text",
            "label" => "京东账号"
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
//        new \App\Components\BootstrapHelper\Widgets\ButtonWidget([
//            'title' => '审核通过',
//            'styleClass' => new \App\Components\BootstrapHelper\Widgets\StyleClass([
//                'class' => 'btn btn-danger list-btn-ajax-remove',
//            ]),
//        ]),
    ],
]) ?>

<script>
    $(function () {

    })
</script>
