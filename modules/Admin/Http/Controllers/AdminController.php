<?php

namespace Modules\Admin\Http\Controllers;

use League\Plates\Engine;

class AdminController extends ViewController
{
    /**
     * @var Engine
     */
    private $templates;

    protected $title = '';
    protected $subTitle = '后台管理';
    protected $breads = [
        [
            '/admin/',
            '后台管理',
        ],
    ];
}