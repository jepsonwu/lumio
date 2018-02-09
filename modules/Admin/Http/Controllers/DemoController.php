<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Account\Models\User;
use Modules\Account\Services\UserInternalService;

class DemoController extends AdminController
{

    protected $title = '贴纸管理';

    public function index(Request $request)
    {
        $this->subTitle = '贴纸列表';

        $query = User::query();
        $list = $query->where("id", 6)->paginate(10);

        return $this->render('admin/demo/index', [
            'list' => $list,
        ]);
    }


    public function edit($id, UserInternalService $internalService)
    {
        $this->subTitle = "编辑贴纸{$id}";

        $this->breads[] = [
            '/admin/demo',
            '贴纸列表',
        ];

        $model = $internalService->getUserById($id);

        return $this->render('admin/demo/form', [
            'model' => $model,
        ]);
    }


    public function show($id, UserInternalService $internalService)
    {
        $this->subTitle = "贴纸{$id}";

        $this->breads[] = [
            '/admin/demo',
            '贴纸列表',
        ];

        if (!$model = $internalService->getUserById($id)) {
            throw new \RuntimeException("paster not found", [$id]);
        }

        return $this->render('admin/demo/show', [
            'model' => $model,
        ]);
    }


    public function store(Request $request)
    {

        $model = new User();
        $model->id = 6;

//        $params = $request->only([
//            'username',
//        ]);
//
//        array_map(function ($key, $one) use ($model) {
//            $model->setAttribute($key, $one);
//        }, array_keys($params), $params);
//
//        try {
//            if (!$model->save()) {
//                return $this->render('admin/demo/form', [
//                    'model' => $model,
//                ]);
//            }
//        } catch (\Exception $e) {
//            dd('报错！请联系程序猿gg', [$e->getMessage()]);
//        }
        return redirect('/admin/demo/edit/' . $model->id);
    }


    public function create()
    {
        $this->subTitle = "创建新贴纸配置";

        $this->breads[] = [
            '/admin/demo',
            '贴纸列表',
        ];

        $model = new User();

        return $this->render('admin/demo/form', [
            'model' => $model,
        ]);
    }


    public function destroy($id, UserInternalService $internalService)
    {
        $model = $internalService->getUserById($id);
//        if (!$model->delete()) {
//            return $this->renderJson(false, [], $model->getErrorsAsString());
//        } else {
//            return $this->renderJson(true);
//        }

        return $this->renderJson(true);
    }

}