<?php

/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午1:32
 */
use App\Components\BootstrapHelper\Widgets\Form\FieldWidget;
use App\Components\BootstrapHelper\Widgets\Form\QiniuUploadImageFieldWidget;
use App\Components\BootstrapHelper\Widgets\Form\UploadImageFieldWidget;

/**
 * @var $widget FieldWidget
 * @var $type   string
 * @var $this   \League\Plates\Template\Template
 */
?>

<?php $this->layout('field_wrapper', ['widget' => $widget, 'type' => $type]) ?>

<?php
switch ($type) {

    // upload image
    case UploadImageFieldWidget::TYPE_KEY:
        /** @var $widget UploadImageFieldWidget */
        ?>
        <?= $this->insert('field_label', ['widget' => $widget, 'type' => $type]) ?>
        <div class="form-control-block form-file-upload">
            <input type="hidden" name="<?= $widget->formField->fieldName ?>"
                   class="data-<?= $widget->formField->fieldName ?>"
                   value="<?= $widget->formField->fieldValue ?>"/>
            <div class="col-xs-8">
                <input type="file" class="form-control ipt-upload"
                       name="<?= $widget->imageFileName ?>"
                       id="<?= $widget->styleClass->id ?>"/>
            </div>
            <div class="col-xs-4">
                <img class="img-preview"
                     src="<?= $widget->formField->fieldValue ?>" style="max-height: 100px">
            </div>
        </div>
        <?php
        break;

    // upload image
    case QiniuUploadImageFieldWidget::TYPE_KEY:
        /** @var $widget UploadImageFieldWidget */
        ?>
        <?= $this->insert('field_label', ['widget' => $widget, 'type' => $type]) ?>
        <?= $this->insert('field_qiniu', ['widget' => $widget, 'type' => $type]) ?>
        <?php
        break;

    // datetime picker
    case App\Components\BootstrapHelper\Widgets\Form\DatetimeFieldWidget::TYPE_KEY:
        /** @var $widget App\Components\BootstrapHelper\Widgets\Form\DatetimeFieldWidget */
        ?>
        <div class="form-datetime-picker">

            <?= $this->insert('field_label', ['widget' => $widget, 'type' => $type]) ?>
            <div class="form-control-block">
                <div class="input-append date ipt-datetimepicker" data-date="<?= $widget->formField->fieldValue ?>"
                     data-date-format="yyyy-mm-dd hh:ii">
                    <input size="16" type="text" value="<?= $widget->formField->fieldValue ?>"
                           class="form-control data-<?= $widget->formField->fieldName ?>"
                           title="" name='<?= $widget->formField->fieldName ?>'>
                    <span class="add-on"><i class="icon-remove"></i></span>
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
            </div>
        </div>
        <?php
        break;

    // datetime picker
    case App\Components\BootstrapHelper\Widgets\Form\SelectFieldWidget::TYPE_KEY:
        /** @var $widget App\Components\BootstrapHelper\Widgets\Form\SelectFieldWidget */
        ?>
        <div class="form-datetime-picker">

            <?= $this->insert('field_label', ['widget' => $widget, 'type' => $type]) ?>

            <div class="form-control-block">
                <select
                        name="<?= $widget->formField->fieldName ?>" id="<?= $widget->styleClass->id ?>"
                        class="form-control data-<?= $widget->formField->fieldName ?>"
                        title="<?= $widget->styleClass->title ?>">
                    <?php foreach ($widget->dataColumn as $key => $one) {
                        ?>
                        <option
                                value="<?= $key ?>" <?= $key == $widget->formField->fieldValue ? 'selected' : '' ?> ><?= $one ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <?php
        break;
    case App\Components\BootstrapHelper\Widgets\Form\HiddenFieldWidget::TYPE_KEY:
        ?>
        <input type="hidden" name="<?= $widget->formField->fieldName ?>"
               class="data-<?= $widget->formField->fieldName ?>"
               id="<?= $widget->styleClass->id ?>"
               placeholder="<?= $widget->styleClass->placeHolder ?>"
               value="<?= $widget->formField->fieldValue ?>"/>
        <?php
        break;
    case App\Components\BootstrapHelper\Widgets\Form\TextAreaFieldWidget::TYPE_KEY:
        ?>
        <?= $this->insert('field_label', ['widget' => $widget, 'type' => $type]) ?>
        <div class="form-control-block">
            <textarea name="<?= $widget->formField->fieldName ?>"
                      id="<?= $widget->styleClass->id ?>" cols="30" rows="10"
                      class="form-control data-<?= $widget->formField->fieldName ?>"
                      placeholder="<?= $widget->styleClass->placeHolder ?>"
            ><?= $widget->formField->fieldValue ?></textarea>
        </div>
        <?php
        break;
    case App\Components\BootstrapHelper\Widgets\Form\DisplayFieldWidget::TYPE_KEY:
        ?>
        <?= $this->insert('field_label', ['widget' => $widget, 'type' => $type]) ?>
        <div class="form-control-block">
            <span class=""><?= $widget->formField->fieldValue ?></span>
        </div>
        <?php
        break;
    default: ?>
        <?= $this->insert('field_label', ['widget' => $widget, 'type' => $type]) ?>
        <div class="form-control-block">
            <input type="text" name="<?= $widget->formField->fieldName ?>"
                   id="<?= $widget->styleClass->id ?>" placeholder="<?= $widget->styleClass->placeHolder ?>"
                   class="form-control data-<?= $widget->formField->fieldName ?>"
                   value="<?= $widget->formField->fieldValue ?>"/>
        </div>
        <?php
}
?>

<?= $widget->renderInfo() ?>
<?= $widget->renderError() ?>
