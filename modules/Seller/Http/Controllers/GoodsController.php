<?php

namespace Modules\Seller\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Seller\Services\GoodsService;

class GoodsController extends ApiBaseController
{
    protected $goodsService;

    public function __construct(GoodsService $goodsService)
    {
        $this->goodsService = $goodsService;
    }

    /**
     *
     *
     * @api {GET} /api/seller/goods/v1 商品列表
     * @apiSampleRequest /api/seller/goods/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName list
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
        $result = $this->goodsService->list(AuthHelper::user()->id);
        return $this->success($result);
    }

    /**
     *
     *
     * @api {POST} /api/seller/goods/v1 创建商品
     * @apiSampleRequest /api/seller/goods/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName create
     *
     * @apiParam {int} store_id 店铺ID
     * @apiParam {string} goods_name 商品名称
     * @apiParam {string} goods_url 商品url
     * @apiParam {string} goods_keywords 商品关键字，多个用【|】分割
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"store_id":"1","goods_name":"demo","goods_url":"ddd","goods_keywords":"dd|aa","user_id":"10","goods_image":"","goods_price":"0","created_at":"1519739224","id":"3"},"code":"0","msg":"","time":"1519739224"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->getValidateForCreate());

        $store = $this->goodsService->create(AuthHelper::user()->id, $this->requestParams->getRegularParams());
        return $this->success($store);
    }

    protected function getValidateForCreate()
    {
        return [
            'store_id' => ['bail', 'required', 'int'],
            'goods_name' => ['bail', 'string', 'required', 'between:1,200'],
            'goods_url' => ['bail', 'string', 'required', 'between:1,500'],
            'goods_keywords' => ['bail', 'string', 'required', 'between:1,500'],
        ];
    }

    /**
     *
     * 1.不允许修改 store_id|goods_name
     *
     * @api {PUT} /api/seller/goods/v1/{id} 修改商品
     * @apiSampleRequest /api/seller/goods/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName update
     *
     * @apiParam {string} goods_name 商品名称
     * @apiParam {string} goods_url 商品url
     * @apiParam {string} goods_keywords 商品关键字，多个用【|】分割
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"store_id":"1","goods_name":"demo","goods_url":"ddd","goods_keywords":"dd|aa","user_id":"10","goods_image":"","goods_price":"0","created_at":"1519739224","id":"3"},"code":"0","msg":"","time":"1519739224"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'goods_url' => ['bail', 'string', 'required', 'between:1,500'],
            'goods_keywords' => ['bail', 'string', 'required', 'between:1,500'],
        ]);

        $store = $this->goodsService->update(AuthHelper::user()->id, $id, $this->requestParams->getRegularParams());
        return $this->success($store);
    }

    /**
     *1.只允许删除没有活跃任务的商品
     *2.todo 可以考虑删除有活跃任务，但是当前没人做任务的商品 需要同步关闭任务
     *
     * @api {DELETE} /api/seller/goods/v1/{id} 删除商品
     * @apiSampleRequest /api/seller/goods/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName delete
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[],"code":"0","msg":"","time":"1519739901"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, $id)
    {
        $this->goodsService->delete(AuthHelper::user()->id, $id);

        return $this->success([]);
    }
}