<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/6/20
 * Time: 下午9:19
 */

namespace App\Components\BootstrapHelper\Widgets;


use Prettus\Repository\Database\Eloquent\Model;

class ModelView extends BaseWidget
{
    /** @var Model */
    public $model;

    /**
     * 自定义显示列.
     *
     * @var array
     */
    protected $columns;

    /** @var ModelColumn[] */
    private $listColumns;

    /**
     * @var ButtonWidget[]
     */
    public $buttons;

    public function init()
    {
        if (! is_subclass_of($this->model, Model::class)) {
            throw new \RuntimeException("invalid model");
        }
        $modelClass = get_class($this->model);

        $this->listColumns = ModelColumn::fetchColumns($modelClass, $this->columns);
    }

    public function render()
    {
        return $this->renderWidget('model_view', [
            'model'   => $this->model,
            'columns' => $this->listColumns,
        ]);
    }
}