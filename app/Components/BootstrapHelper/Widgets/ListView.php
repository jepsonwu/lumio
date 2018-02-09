<?php

namespace App\Components\BootstrapHelper\Widgets;

/**
 * Created by IntelliJ IDEA.
 * User: yidu
 * Date: 16/8/5
 * Time: 下午10:30
 */
class ListView extends BaseWidget
{
    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator
     */
    protected $list;

    /** @var string */
    protected $modelClass;

    //protected $_temp;

    protected $model;

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
    public $image;
    /** @var StyleClass */
    public $buttonsStyleClass;

    public function init()
    {
        $modelClass = $this->modelClass;

        $this->listColumns = ModelColumn::fetchColumns($modelClass, $this->columns);

        if (!$this->styleClass->class) {
            $this->styleClass->class = 'table table-striped table-hover table-bordered';
        }
    }

    public function render()
    {
        $map = [
            'header' => $this->header(),
            'body' => $this->body(),
            'footer' => $this->footer(),
        ];

        return $this->renderWidget('list_view', $map);
    }

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getList()
    {
        return $this->list;
    }

    protected function header()
    {
        return $this->renderWidget('table_header', [
            'columns' => $this->listColumns,
        ]);
    }

    protected function body()
    {
        return $this->renderWidget('table_body', [
            'list' => $this->list,
            'columns' => $this->listColumns,
        ]);
    }

    protected function footer()
    {
        if ($this->list) {
            return $this->list->render();
        }

        return '';
    }

    public function main()
    {
        echo $this->render();
    }
}
