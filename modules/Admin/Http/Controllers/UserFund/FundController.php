<?php

namespace Modules\Admin\Http\Controllers\UserFund;

use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\UserFund\Models\Fund;

class FundController extends AdminController
{

    protected $title = '资金管理';

    public function index(Request $request)
    {
        $this->subTitle = '资金列表';

        $this->validate($request, $rules = [
            "user_id" => ["integer"],
        ]);

        $conditions = [

        ];

        $params = $request->only(array_keys($rules));

        $params['user_id'] && $conditions[] = ["user_id", $params['user_id']];

        $query = Fund::query();
        $list = $query->where($conditions)->paginate(10);

        return $this->render('admin/user-fund/index', [
            'list' => $list,
            'params' => $params
        ]);
    }
}