<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/9
 * Time: 15:51
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Func;

/**
 * Class FuncFactory
 *
 * @package App\Components\BootstrapHelper\Widgets\Func
 */
class FuncFactory
{
    /**
     * @param $value
     * @param $valueCallback
     *
     * @return SingleFieldRenderFunc
     */
    public static function field($value, $valueCallback = null)
    {
        return new SingleFieldRenderFunc($value, $valueCallback);
    }

    /**
     * @param string   $value
     * @param int      $maxWidthInPx
     * @param callable $valueCallback
     *
     * @return SingleFieldRenderFunc
     */
    public static function fieldWithin($value, $maxWidthInPx = 200, $valueCallback = null)
    {
        return self::field($value, function ($fieldValue, $model) use ($maxWidthInPx, $valueCallback) {
            $template = '<div style="max-width: %dpx"><span>%s</span></div>';

            if ($valueCallback) {
                $fieldValue = call_user_func($valueCallback, $fieldValue, $model);
            }

            return sprintf($template, $maxWidthInPx, $fieldValue);
        });
    }

    /**
     * @param array $list
     *
     * @return ItemListRenderFunc
     */
    public static function itemList(array $list)
    {
        return new ItemListRenderFunc($list);
    }

    /**
     * @param string $field
     * @param string $link
     * @param bool   $https
     *
     * @return ImgRenderFunc
     */
    public static function img($field, $link = null, $https = false)
    {
        return new ImgRenderFunc($field, $link, $https);
    }

    /**
     * @param null  $action
     * @param array $operations
     *
     * @return OperationRenderFunc
     */
    public static function operation($action = null, array $operations = [])
    {
        return new OperationRenderFunc($action, $operations);
    }

    /**
     * @return TableCheckboxRenderFunc
     */
    public static function tableCheckbox()
    {
        return new TableCheckboxRenderFunc();
    }
}