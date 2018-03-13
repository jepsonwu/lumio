<?php

namespace Modules\Admin\Http\Controllers\Account;

use Illuminate\Http\Request;
use Modules\Account\Models\User;
use Modules\Admin\Http\Controllers\AdminController;

class UserController extends AdminController
{

    protected $title = '用户管理';

    public function index(Request $request)
    {
        $this->subTitle = '用户列表';

        $this->validate($request, $rules = [
            "mobile" => ["integer"],
            "role" => ["in:-1,0,1,2"],
        ]);

        $conditions = [
        ];

        $params = $request->only(array_keys($rules));
        $params['role'] == "" && $params['role'] = '-1';//给默认值

        $params['mobile'] && $conditions[] = ["mobile", $params['mobile']];
        $params['role'] != -1 && $conditions[] = ["role", $params['role']];

        $query = User::query();
        $list = $query->where($conditions)->paginate(10);

        return $this->render('admin/account-user/index', [
            'list' => $list,
            'params' => $params
        ]);
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