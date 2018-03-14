<?php

namespace Modules\Admin\Http\Controllers\UserFund;

use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\UserFund\Models\Account;

class AccountController extends AdminController
{

    protected $title = '资金账号管理';

    public function index(Request $request)
    {
        $this->subTitle = '资金账号列表';

        $this->validate($request, $rules = [
            "user_id" => ["integer"],
            "account_status" => ["in:-1,0,1"],
        ]);

        $conditions = [

        ];

        $params = $request->only(array_keys($rules));
        $params['account_status'] == "" && $params['account_status'] = '-1';//给默认值

        $params['user_id'] && $conditions[] = ["user_id", $params['user_id']];
        $params['account_status'] != -1 && $conditions[] = ["account_status", $params['account_status']];

        $query = Account::query();
        $list = $query->where($conditions)->paginate(10);

        return $this->render('admin/user-fund-account/index', [
            'list' => $list,
            'params' => $params
        ]);
    }
}