<?php
/**
 * Created by PhpStorm.
 * Date: 16/9/9
 * Time: 20:27
 *
 * @author limi
 */

namespace App\Components\BootstrapHelper\Widgets\Func;

/**
 * Class OperationRenderFunc
 *
 * @package App\Components\BootstrapHelper\Widgets\Func
 */
class OperationRenderFunc extends AbstractRenderFunc
{
    /**
     * @var array
     */
    protected $defaultOperationTitles = ['编辑', '删除'];

    /**
     * @var array
     */
    protected $operations;

    /**
     * @var
     */
    protected $action;

    public function __construct($action = null, array $operations = [])
    {
        $this->action     = $action;
        $this->operations = $operations ?: array_flip($this->defaultOperationTitles);
    }

    public function invoke($model, $column)
    {
        return $this->renderWidget('table_operation');
    }

    /**
     * @return \Generator
     */
    public function getOperations()
    {
        $this->mergeDefaultOperations();

        foreach ($this->operations as $key => $val) {
            $operation = [
                'title'  => $this->resolveOperationTitle($key, $val),
                'class'  => array_get($val, 'class', $val),
                'href'   => array_get($val, 'href', 'javascript:void(0);'),
                'data'   => array_get($val, 'data', []),
                'target' => array_get($val, 'target', '_self'),
            ];

            yield $operation;
        }
    }

    protected function mergeDefaultOperations()
    {
        $keys = array_keys($this->operations);

        if (in_array('编辑', $keys)) {
            $this->operations['编辑'] = [
                'class'  => '',
                'href'   => $this->resolveEditHref(),
                'target' => '_blank',
            ];
        }

        if (in_array('删除', $keys)) {
            $url                    = sprintf('/admin/%s/%s', $this->resolveAction(), $this->model->id);
            $this->operations['删除'] = [
                'class' => 'text-danger delete',
                'data'  => [
                    'url'   => $url,
                    'title' => $this->model->getTitle(),
                ],
            ];
        }
    }

    /**
     * @param $key
     * @param $val
     *
     * @return mixed
     */
    protected function resolveOperationTitle($key, $val)
    {
        $title = array_get($val, 'title', $key);

        if (is_callable($title)) {
            return call_user_func($title, $this->model);
        }

        return $title;
    }

    /**
     * @return string
     */
    protected function resolveEditHref()
    {
        $id     = $this->model->id;
        $action = $this->resolveAction();

        return "/admin/{$action}/update/{$id}";
    }

    /**
     * @return string
     */
    protected function resolveAction()
    {
        return $this->action ?: strtolower($this->model->getTable());
    }
}