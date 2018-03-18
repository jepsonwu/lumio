<?php

namespace Modules\Seller\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use App\Constants\GlobalDBConstant;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Seller\Models\Goods;
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
     * @apiName goods-list
     *
     * @apiParam {int} [store_id] 店铺ID
     * @apiParam {string} [goods_name] 商品名称
     * @apiParam {int} page
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"current_page":"1","data":[{"id":"1","user_id":"10","store_id":"1","goods_name":"demo","goods_url":"ddd","goods_image":"","goods_price":"0","goods_keywords":"dd|aa","goods_status":"1","created_at":"1519739026","updated_at":"2018-02-27 21:43:46"}],"from":"1","last_page":"2","next_page_url":"http:\/\/test.lumio.com\/api\/seller\/goods\/v1?page=2","path":"http:\/\/test.lumio.com\/api\/seller\/goods\/v1","per_page":"1","prev_page_url":"","to":"1","total":"2"},"code":"0","msg":"","time":"1519743720"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        $this->validate($request, [
            'store_id' => ['int'],
            'goods_name' => ['string', 'between:1,200'],
        ]);

        //todo package and optimize
        $params = $this->requestParams->getRegularParams();
        $params = array_filter($params, function ($val) {
            return $val != "";
        });

        $conditions = [
            "user_id" => AuthHelper::user()->id,
            "goods_status" => GlobalDBConstant::DB_TRUE
        ];
        isset($params['store_id']) && $conditions['store_id'] = $params['store_id'];
        isset($params['goods_name'])
        && $conditions['goods_name'] = ['goods_name', 'like', "%{$params['goods_name']}%"];

        $result = $this->goodsService->list($conditions);
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
     * @apiName create-goods
     *
     * @apiParam {int} store_id 店铺ID
     * @apiParam {string} goods_name 商品名称
     * @apiParam {string} goods_image 商品主图
     * @apiParam {int} goods_price 商品价格 分
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
            //'goods_url' => ['bail', 'string', 'required', 'between:1,500'],
            'goods_image' => ['bail', 'string', 'required', 'between:1,150'],
            'goods_price' => ['bail', 'integer', 'required', 'min:1'],
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
     * @apiName update-goods
     *
     * @apiParam {string} goods_name 商品名称
     * @apiParam {string} goods_image 商品主图
     * @apiParam {int} goods_price 商品价格 分
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
            'goods_image' => ['bail', 'string', 'required', 'between:1,150'],
            'goods_price' => ['bail', 'integer', 'required', 'min:1'],
            //'goods_url' => ['bail', 'string', 'required', 'between:1,500'],
            'goods_keywords' => ['bail', 'string', 'required', 'between:1,500'],
        ]);

        $store = $this->goodsService->update(AuthHelper::user()->id, $id, $this->requestParams->getRegularParams());
        return $this->success($store);
    }

    /**
     *
     *
     *
     * @api {GET} /api/seller/goods/v1/{id} 商品详情
     * @apiSampleRequest /api/seller/goods/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup seller
     * @apiName goods-detail
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"store_id":"1","goods_name":"demo","goods_url":"ddd","goods_keywords":"dd|aa","user_id":"10","goods_image":"","goods_price":"0","created_at":"1519739224","id":"3"},"code":"0","msg":"","time":"1519739224"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detail(Request $request, $id)
    {
        $store = $this->goodsService->detail(AuthHelper::user()->id, $id);
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