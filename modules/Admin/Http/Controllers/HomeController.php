<?php

namespace Modules\Admin\Http\Controllers;

class HomeController extends AdminController
{
    public function index()
    {
        return $this->render('admin/index', []);
    }
}
