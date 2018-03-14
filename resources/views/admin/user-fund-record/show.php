<?php

use App\Components\BootstrapHelper\Widgets\ModelView;

/** @var $this \League\Plates\Template\Template */
/** @var $model \Modules\Account\Models\User */
$this->layout('layouts/main-temp');
?>

<?= new ModelView([
    'model' => $model,
    'columns' => [
        'id' => "#ID",
        'username',
        'created_at',
        'updated_at',
    ],
    'buttons' => [
        new \App\Components\BootstrapHelper\Widgets\ButtonWidget([
            'title' => '编辑',
            'onClick' => function ($model) {
                return "window.location='" . url('/admin/demo/edit/' . $model->id) . "'";
            },
            'styleClass' => new \App\Components\BootstrapHelper\Widgets\StyleClass([
                'class' => 'btn btn-info',
            ]),
        ]),
    ],
]);

?>

