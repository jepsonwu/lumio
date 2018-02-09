<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/9
 * Time: 16:01
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Func;

class ItemListRenderFunc extends AbstractRenderFunc
{
    /**
     * @var array
     */
    protected $list;

    public function __construct(array $list)
    {
        $this->list = $list;
    }

    public function invoke($model, $column)
    {
        return $this->renderWidget('table_itemlist', $this->list);
    }

    /**
     * @return \Generator
     */
    public function getList()
    {
        foreach ($this->list as $key => $val) {

            $field  = array_get($val, 'field', $val);
            $format = array_get($val, 'format');
            $value  = $this->getFieldValue($field, $format);

            $item = [
                'name'  => $key,
                'value' => $value,
                'price' => (bool) array_get($val, 'price'),
                'class' => $this->getItemClass($field, $format, $value, $key),
            ];

            yield $item;
        }
    }

    protected function getItemClass($field, $format, $value, $key)
    {
        if ($format === 'time') {
            $now = date('Y-m-d H:i');
            if ($key === '上线时间' && $value >= $now) {
                return 'text-info';
            }

            if ($key === '过期时间' && $value <= $now) {
                return 'text-danger';
            }
        }

        return '';
    }
}