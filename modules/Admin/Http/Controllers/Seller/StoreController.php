<?php

namespace Modules\Admin\Http\Controllers\Seller;

use App\Constants\GlobalDBConstant;
use Illuminate\Http\Request;
use Modules\Account\Services\UserInternalService;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Seller\Models\Store;
use Modules\Seller\Services\SellerInternalService;

class StoreController extends AdminController
{

    protected $title = '店铺管理';

    protected $sellerInternalService;

    public function __construct(SellerInternalService $sellerInternalService)
    {
        $this->sellerInternalService = $sellerInternalService;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $this->subTitle = '店铺列表';

        $this->validate($request, $rules = [
            "user_id" => ["integer"],
            "store_name" => ["string"],
            "store_type" => ["in:-1,1,2"],
            "verify_status" => ["in:-1,0,1,2"],
        ]);

        $conditions = [
            ["store_status", GlobalDBConstant::DB_TRUE]
        ];

        $params = $request->only(array_keys($rules));
        $params['store_type'] == "" && $params['store_type'] = '-1';
        $params['verify_status'] == "" && $params['verify_status'] = '-1';//给默认值

        $params['user_id'] && $conditions[] = ["user_id", $params['user_id']];
        $params['store_type'] != -1 && $conditions[] = ["store_type", $params['store_type']];
        $params['verify_status'] != -1 && $conditions[] = ["verify_status", $params['verify_status']];
        $params['store_name'] && $conditions[] = ["store_name", 'like', "%{$params['store_name']}%"];

        //todo 放到合适的位置 todo 有个分库分表的bug
        $query = Store::query();
        $list = $query->where($conditions)->paginate(2);
        //return $this->success($list);
        return $this->render('admin/seller-store/index', [
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

        //todo auth
        $params = $this->requestParams->getRegularParams();
        $this->sellerInternalService->verifyFailStore($params['id'], $params['reason'], 1);

        return $this->success([]);
    }

    public function verifyPass(Request $request)
    {
        $this->validate($request, [
            "id" => "required|integer"
        ]);

        $params = $this->requestParams->getRegularParams();
        $this->sellerInternalService->verifyPassStore($params['id'], 1);

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