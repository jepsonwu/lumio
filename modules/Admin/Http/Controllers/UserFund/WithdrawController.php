<?php

namespace Modules\Admin\Http\Controllers\UserFund;

use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\UserFund\Models\FundWithdraw;
use Modules\UserFund\Services\UserFundInternalService;

class WithdrawController extends AdminController
{

    protected $title = '提现管理';

    protected $userFundInternalService;

    public function __construct(UserFundInternalService $userFundInternalService)
    {
        $this->userFundInternalService = $userFundInternalService;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $this->subTitle = '提现列表';

        $this->validate($request, $rules = [
            "user_id" => ["integer"],
            "withdraw_status" => ["in:-1,0,1,2,3"],
        ]);

        $conditions = [
        ];

        $params = $request->only(array_keys($rules));
        $params['withdraw_status'] == "" && $params['withdraw_status'] = '-1';//给默认值

        $params['user_id'] && $conditions[] = ["user_id", $params['user_id']];
        $params['withdraw_status'] != -1 && $conditions[] = ["withdraw_status", $params['withdraw_status']];

        $query = FundWithdraw::query();
        $list = $query->where($conditions)->paginate(10);

        return $this->render('admin/user-fund-withdraw/index', [
            'list' => $list,
            'params' => $params
        ]);
    }

    public function verifyFail(Request $request)
    {
        $this->validate($request, [
            "reason" => "required|string|between:1,100",
            "id" => "required|integer"
        ]);

        $params = $this->requestParams->getRegularParams();
        $this->userFundInternalService->failWithdraw($params['id'], 1, $params['reason']);

        return $this->success([]);
    }

    public function verifyPass(Request $request)
    {
        $this->validate($request, [
            "id" => "required|integer"
        ]);

        $params = $this->requestParams->getRegularParams();
        $this->userFundInternalService->passWithdraw($params['id'], 1);

        return $this->success([]);
    }

//    public function edit($id, UserInternalService $internalService)
//    {
//        $this->subTitle = "编辑贴纸{$id}";
//
//        $this->breads[] = [
//            '/admin/demo',
//            '贴纸列表',
//        ];
//
//        $model = $internalService->getUserById($id);
//
//        return $this->render('admin/demo/form', [
//            'model' => $model,
//        ]);
//    }


//    public function show($id, UserInternalService $internalService)
//    {
//        $this->subTitle = "店铺{$id}";
//
//        $this->breads[] = [
//            '/admin/demo',
//            '贴纸列表',
//        ];
//
//        if (!$model = $internalService->getUserById($id)) {
//            throw new \RuntimeException("paster not found", [$id]);
//        }
//
//        return $this->render('admin/demo/show', [
//            'model' => $model,
//        ]);
//    }


//    public function store(Request $request)
//    {
//
//        $model = new User();
//        $model->id = 6;
//
////        $params = $request->only([
////            'username',
////        ]);
////
////        array_map(function ($key, $one) use ($model) {
////            $model->setAttribute($key, $one);
////        }, array_keys($params), $params);
////
////        try {
////            if (!$model->save()) {
////                return $this->render('admin/demo/form', [
////                    'model' => $model,
////                ]);
////            }
////        } catch (\Exception $e) {
////            dd('报错！请联系程序猿gg', [$e->getMessage()]);
////        }
//        return redirect('/admin/demo/edit/' . $model->id);
//    }


//    public function create()
//    {
//        $this->subTitle = "创建新贴纸配置";
//
//        $this->breads[] = [
//            '/admin/demo',
//            '贴纸列表',
//        ];
//
//        $model = new User();
//
//        return $this->render('admin/demo/form', [
//            'model' => $model,
//        ]);
//    }


//    public function destroy($id, UserInternalService $internalService)
//    {
//        $model = $internalService->getUserById($id);
////        if (!$model->delete()) {
////            return $this->renderJson(false, [], $model->getErrorsAsString());
////        } else {
////            return $this->renderJson(true);
////        }
//
//        return $this->renderJson(true);
//    }

}