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
     *
     *
     * @api {GET} /api/seller/store/v1 店铺列表
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
     * {"succ":true,"data":[{"id":"4","user_id":"10","real_name":"\u5434\u5065\u5e73","id_card":"3602221991078362","bank_card":"234234343413134","bank":"\u4e2d\u56fd\u94f6\u884c","bankfiliale":"\u676d\u5dde\u4e5d\u5821\u652f\u884c","account_status":"1","created_at":"1518760738","updated_at":"2018-02-16 13:58:58"}],"code":"0","msg":"","time":"1518760783"}
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
     * {"succ":true,"data":{"store_type":"1","store_url":"aaa","store_account":"ddd","store_name":"\u6dd8\u5b9d","user_id":"10","created_at":"1519653780","verify_status":"0","id":"1"},"code":"0","msg":"","time":"1519653780"}
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
     *
     * @api {DELETE} /api/seller/store/v1/{id} 删除店铺
     * @apiSampleRequest /api/seller/store/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName create-store
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