<?php

namespace Modules\Task\Http\Controllers;

use Nwidart\Modules\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;
class ResourcesController extends BaseController
{
    public function ping()
    {
        echo "ping";
    }
}
