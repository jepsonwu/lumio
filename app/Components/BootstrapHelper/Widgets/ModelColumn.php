<?php
/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/11
 * Time: 上午10:05
 */

namespace App\Components\BootstrapHelper\Widgets;


use App\Models\Channel;
use Prettus\Repository\Database\Eloquent\Model;
use App\Components\BootstrapHelper\DbAttrsFetcher;
use App\Components\BootstrapHelper\InitTrait;

class ModelColumn
{
    use InitTrait;

    public $name;
    public $label;
    public $type = 'text';
    public $comment;
    public $renderFunc;

    function __construct($opts)
    {
        $this->initTrait($opts);
    }

    /**
     * @param $model Model
     *
     * @return false|mixed|string
     */
    public function getValue($model)
    {

        $key = $this->name;

        if ($this->renderFunc) {
            $func = $this->renderFunc;

            return $func($model, $this);
        } else {

            $value = $model->$key;
            // type deal with
            switch ($this->type) {
                case 'date':
                    if (is_numeric($value)) {
                        return date('Y-m-d', $value);
                    } else {
                        // it is string, change it
                        return date('Y-m-d', strtotime($value));
                    }
                case 'datetime':
                    if (is_numeric($value)) {
                        return date('Y-m-d H:i:s', $value);
                    } else {
                        // it is string, change it
                        return date('Y-m-d H:i:s', strtotime($value));
                    }
                case 'time':
                    if (is_numeric($value)) {
                        return date('H:i:s', $value);
                    } else {
                        // it is string, change it
                        return date('H:i:s', strtotime($value));
                    }
                case 'images':
                    return $this->renderImages($value);
                case 'boolean':
                    return $value ? '是' : '否';
                case 'channel':
                    $channel = Channel::whereId($value)->first();

                    return $channel ? $channel->nick_name : $value;
                default:
                    if ($key == $model->getKeyName()):
                        return $value;
                    else:
                        return $value;
                    endif;
            }
        }
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function renderImages($value)
    {
        if (!$value) {
            return '';
        }

        \Log::info('', [$value, explode("\r\n", $value)]);

        return implode('<br/>', array_map(function ($one) {
                if (!$one) {
                    return '';
                }

                return '<a target="_blank" href="' . $one . '">
                            <img style="height: 50px;" src="' . $one . '"/></a>';
            }, explode("\r\n", $value))
        );
    }

    public function getLabel()
    {
        // 如果 name 已经是中文, 则直接返回
        if (!preg_match('/^[a-z_\.0-9]+$/i', $this->name)) {
            return $this->name;
        }

        $domain = 'models';
        if ($this->label) {
            return trans($this->label, [], $domain);
        }

        return trans($domain . '.' . $this->name, [], $domain);
    }

    /**
     * @param $class
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function buildAttrs($class)
    {
        return DbAttrsFetcher::fetchAttrs($class);
    }

    /**
     * 子类重写.
     *
     * @return array
     */
    public static function getDefaultColumns()
    {
        return [];
    }

    /**
     * @param       $modelClass
     *
     * @param array $columns
     *
     * @return array
     */
    public static function fetchColumns($modelClass, $columns = [])
    {
        // fetch model type
        $attrs = ModelColumn::buildAttrs($modelClass);

        if (!$columns) {
            $columns = ModelColumn::getDefaultColumns();
        }

        if (!$columns) {
            $listColumns = array_map(function ($one) {
                return new ModelColumn($one);
            }, $attrs, array_keys($attrs));
        } else {
            $listColumns = array_map(function ($one, $key) {

                if (is_string($one)) {
                    return new ModelColumn([
                        'name' => $one,
                    ]);
                }

                if ($one instanceof ModelColumn) {
                    $one->name = $key;

                    return $one;
                }

                return new ModelColumn(array_merge([
                    'name' => $key,
                ], $one));
            }, $columns, array_keys($columns));

        }

        return $listColumns;
    }
}
