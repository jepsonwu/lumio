<?php

namespace Modules\Admin\Http\Controllers\UserFund;

use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\UserFund\Models\FundRecord;

class RecordController extends AdminController
{

    protected $title = '资金记录管理';

    public function index(Request $request)
    {
        $this->subTitle = '资金记录列表';

        $this->validate($request, $rules = [
            "user_id" => ["integer"],
            "record_status" => ["in:-1,0,1,2,3"],
            "record_type" => ["in:-1,1,2,3,4"],
        ]);

        $conditions = [

        ];

        $params = $request->only(array_keys($rules));
        $params['record_status'] == "" && $params['record_status'] = '-1';//给默认值
        $params['record_type'] == "" && $params['record_type'] = '-1';//给默认值

        $params['user_id'] && $conditions[] = ["user_id", $params['user_id']];
        $params['record_status'] != -1 && $conditions[] = ["record_status", $params['record_status']];
        $params['record_type'] != -1 && $conditions[] = ["record_type", $params['record_type']];

        $query = FundRecord::query();
        $list = $query->where($conditions)->paginate(10);

        return $this->render('admin/user-fund-record/index', [
            'list' => $list,
            'params' => $params
        ]);
    }
}