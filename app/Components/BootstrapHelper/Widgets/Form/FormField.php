<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午1:30
 */

namespace App\Components\BootstrapHelper\Widgets\Form;

use App\Components\BootstrapHelper\IModelAccess;
use App\Components\BootstrapHelper\InitTrait;
use App\Components\BootstrapHelper\Widgets\FormWidget;

class FormField
{
    /**
     * @var FormWidget
     */
    public $formWidget;

    /** @var IModelAccess */
    public $model;

    public $fieldName;
    public $fieldLabel;
    public $fieldValue;
    public $fieldHelp;

    use InitTrait;

    function __construct($opts = [])
    {
        $this->initTrait($opts);
    }

    public function displayField($opts = [])
    {
        return $this->fieldWidget(DisplayFieldWidget::TYPE_KEY, $opts);
    }

    public function hiddenField($opts = [])
    {
        return $this->fieldWidget(HiddenFieldWidget::TYPE_KEY, $opts);
    }

    public function textField($opts = [])
    {
        return $this->fieldWidget(TextFieldWidget::TYPE_KEY, $opts);
    }

    public function textAreaField($opts = [])
    {
        return $this->fieldWidget(TextAreaFieldWidget::TYPE_KEY, $opts);
    }

    public function selectField($opts = [])
    {
        return $this->fieldWidget(SelectFieldWidget::TYPE_KEY, $opts);
    }

    public function uploadImageField($opts = [])
    {
        return $this->fieldWidget(UploadImageFieldWidget::TYPE_KEY, $opts);
    }

    public function qiNiuUploadImageField($opts = [])
    {
        return $this->fieldWidget(QiniuUploadImageFieldWidget::TYPE_KEY, $opts);
    }

    public function datetimeField($opts = [])
    {
        return $this->fieldWidget(DatetimeFieldWidget::TYPE_KEY, $opts);
    }

    /**
     * @param array $opts
     *
     * @return CustomFieldWidget
     */
    public function customField($opts = [])
    {
        return $this->fieldWidget(CustomFieldWidget::TYPE_KEY, $opts);
    }

    private function fieldWidget($type, $opts)
    {

        /**
         * @var $widget FieldWidget
         */
        $widget = null;

        $opts['formField'] = $this;

        switch ($type) {
            case DisplayFieldWidget::TYPE_KEY:
                $widget = new DisplayFieldWidget($opts);
                break;
            case TextAreaFieldWidget::TYPE_KEY:
                $widget = new TextAreaFieldWidget($opts);
                break;
            case HiddenFieldWidget::TYPE_KEY:
                $widget = new HiddenFieldWidget($opts);
                break;
            case TextFieldWidget::TYPE_KEY:
                $widget = new TextFieldWidget($opts);
                break;
            case UploadImageFieldWidget::TYPE_KEY:
                $widget = new UploadImageFieldWidget($opts);
                break;
            case QiniuUploadImageFieldWidget::TYPE_KEY:
                $widget = new QiniuUploadImageFieldWidget($opts);
                break;
            case DatetimeFieldWidget::TYPE_KEY:
                $widget = new DatetimeFieldWidget($opts);
                break;
            case CustomFieldWidget::TYPE_KEY:
                $widget = new CustomFieldWidget($opts);
                break;
            case SelectFieldWidget::TYPE_KEY:
                $widget = new SelectFieldWidget($opts);
                break;
            default:
                throw new \RuntimeException('not supported type [' . $type . ']');
        }

        return $widget;
    }

    function __toString()
    {
        return '';
    }

    public function getError(){
        return $this->model->getError($this->fieldName);
    }
}
