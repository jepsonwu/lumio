<?php

use App\Components\BootstrapHelper\Widgets\FormWidget;

$this->layout('layouts/main-temp');

/** @var $model \Modules\Account\Models\User */
?>

<div class="form">
    <?php
    $form = new FormWidget([
        'action' => url('/admin/demo'),
        'model' => $model,
    ]);
    $form->begin();
    ?>
    <?= $form->field('id')->hiddenField() ?>
    <?= $form->field('username')->textField() ?>
    <?= $form->field('created_at')->displayField() ?>
    <?= $form->button('submit', '保存') ?>

    <?php $form->end() ?>
</div>

<script>

</script>
