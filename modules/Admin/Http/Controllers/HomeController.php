<?php

namespace Modules\Admin\Http\Controllers;

use App\Components\Helpers\AuthHelper;

class HomeController extends AdminController
{
    public function index()
    {
        return $this->render('admin/index', [
            "params" => [
                "user" => AuthHelper::user()
            ]
        ]);
    }
}
