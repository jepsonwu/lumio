<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/8
 * Time: 下午1:31
 */

namespace App\Components\BootstrapHelper\Widgets\Form;


class UploadImageFieldWidget extends FieldWidget
{
    public $imageFileName = 'image_file';

    const TYPE_KEY = 'upload_image';

    public function init()
    {
        parent::init();
    }

    public function render()
    {
        return $this->renderWidget('field', [
            'type' => self::TYPE_KEY
        ]);
    }
}
