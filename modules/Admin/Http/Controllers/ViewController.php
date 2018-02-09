<?php

namespace Modules\Admin\Http\Controllers;


use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use League\Plates\Engine;

class ViewController extends ApiBaseController
{
    /**
     * @var Engine
     */
    private $templates;

    protected $title = '管理后台';
    protected $subTitle = '';
    protected $breads = [
        [
            '/super/',
            '主页',
        ],
    ];

    /**
     * 目标资源的名称.
     *
     * @var string
     */
    protected $targetName = '';

    /**
     * 操作名称. 请求路径上的可变部分.
     *
     * @var string
     */
    protected $action = '';


    public function __construct()
    {
        $this->templates = new Engine(resource_path('views'));
    }

    protected function render($name, array $data = [], $share = [])
    {
        $share = array_merge([
            'action' => $this->action,
            'targetName' => $this->targetName,
            'controller' => $this,
        ], $share);

        $this->templates->addData($share);

        $this->templates->addData([
            '__title' => $this->title,
            '__sub_title' => $this->subTitle,
            '__breads' => $this->breads,
        ], [
            'layouts/page-bar-temp',
        ]);

        return $this->templates->render($name, $data);
    }

    public function renderJson($succ, $data = [], $codeTpl = '')
    {

        /** @var \Illuminate\Http\Request $request */
        $request = app('request');
        if (in_array(strtoupper($request->getMethod()), ['PUT', 'DELETE', 'POST'])) {
            !isset($data['_token']) && $data['_token'] = csrf_token();
        }

        return parent::result($succ, $data, $codeTpl);
    }
}