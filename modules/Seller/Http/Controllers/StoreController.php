<?php

namespace Modules\Seller\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Seller\Services\StoreService;

class StoreController extends ApiBaseController
{
    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * 1.返回所有的店铺
     *
     * @api {GET} /api/seller/store/v1 店铺列表|不分页
     * @apiSampleRequest /api/seller/store/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName store-list
     *
     *
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[{"id":"1","user_id":"10","store_type":"2","store_url":"11","store_name":"jd","store_account":"22","verify_remark":"\u9519\u8bef","verify_user_id":"0","verify_status":"1","created_at":"1519653780"}],"code":"0","msg":"","time":"1521374483"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        $result = $this->storeService->list(AuthHelper::user()->id);

        return $this->success($result);
    }

    /**
     *
     *
     * @api {POST} /api/seller/store/v1 创建店铺
     * @apiSampleRequest /api/seller/store/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName create-store
     *
     * @apiParam {int} store_type 店铺类型：1-淘宝，2-京东
     * @apiParam {string} store_url 店铺url
     * @apiParam {string} store_account 店铺账号
     * @apiParam {string} store_name 店铺名称
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"store_type":"1","store_url":"aaa","store_account":"ddd","store_name":"\u6dd8\u5b9d","user_id":"10","created_at":"1521374800","verify_status":"0","verify_remark":"","verify_user_id":"0","id":"2"},"code":"0","msg":"","time":"1521374800"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->getValidateForCreate());

        $store = $this->storeService->create(AuthHelper::user()->id, $this->requestParams->getRegularParams());
        return $this->success($store);
    }

    /**
     *
     *
     * @api {POST} /api/seller/store/v1/{id} 店铺详情
     * @apiSampleRequest /api/seller/store/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName store-detail
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"store_type":"1","store_url":"aaa","store_account":"ddd","store_name":"\u6dd8\u5b9d","user_id":"10","created_at":"1521374800","verify_status":"0","verify_remark":"","verify_user_id":"0","id":"2"},"code":"0","msg":"","time":"1521374800"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detail(Request $request,$id)
    {
        $store = $this->storeService->detail(AuthHelper::user()->id, $id);
        return $this->success($store);
    }

    protected function getValidateForCreate()
    {
        return [
            'store_type' => ['bail', 'required', 'in:1,2'],
            'store_url' => ['bail', 'string', 'required', 'between:1,500'],
            'store_account' => ['bail', 'string', 'required', 'between:1,100'],
            'store_name' => ['bail', 'string', 'required', 'between:1,200'],
        ];
    }

    /**
     *
     *
     * @api {PUT} /api/seller/store/v1/{id} 修改店铺
     * @apiSampleRequest /api/seller/store/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName update-store
     *
     * @apiParam {int} store_type 店铺类型：1-淘宝，2-京东
     * @apiParam {string} store_url 店铺url
     * @apiParam {string} store_account 店铺账号
     * @apiParam {string} store_name 店铺名称
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"store_type":"1","store_url":"aaa","store_account":"ddd","store_name":"\u6dd8\u5b9d","user_id":"10","created_at":"1519653780","verify_status":"0","id":"1"},"code":"0","msg":"","time":"1519653780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, $this->getValidateForCreate());

        $store = $this->storeService->update(AuthHelper::user()->id, $id, $this->requestParams->getRegularParams());
        return $this->success($store);
    }

    /**
     *
     *1.只允许删除没有活跃任务的店铺
     *2.todo 可以考虑删除有活跃任务，但是当前没人做任务的店铺 需要同步关闭任务
     *
     * @api {DELETE} /api/seller/store/v1/{id} 删除店铺
     * @apiSampleRequest /api/seller/store/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName delete-store
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[],"code":"0","msg":"","time":"1518425196"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, $id)
    {
        $this->storeService->delete(AuthHelper::user()->id, $id);

        return $this->success([]);
    }
}