<?php

namespace Modules\UserFund\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\UserFund\Services\WalletService;

class WalletController extends ApiBaseController
{
    protected $_walletService;

    public function __construct(WalletService $walletService)
    {
        $this->_walletService = $walletService;
    }

    /**
     *
     *
     * @api {GET} /api/user-fund/wallet/v1 钱包详情
     * @apiSampleRequest /api/user-fund/wallet/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund
     * @apiName detail
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[],"code":"0","msg":"","time":"1517818507"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detail(Request $request)
    {
        $info = $this->_walletService->info(AuthHelper::user()->id);
        return $this->success($info);
    }

    /**
     *
     *
     * @api {GET} /api/user-fund/v1 我的资金账户
     * @apiSampleRequest /api/user-fund/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund
     * @apiName list
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
    public function fundRecordList(Request $request)
    {
        $result = $this->_walletService->fundRecordList([]);
        return $this->success($result);
    }

    public function rechargeList()
    {

    }

    /**
     *
     *
     * @api {POST} /api/user-fund/wallet/v1/recharge 充值
     * @apiSampleRequest /api/user-fund/wallet/v1/recharge
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund
     * @apiName recharge
     *
     *
     * @apiParam {int} amount 金额
     * @apiParam {int} source_account_id 来源账户ID
     * @apiParam {int} source_account_type 来源账户类型 1-bank，2-alipay，3-wechat
     * @apiParam {int} destination_account_id 目标账户ID
     * @apiParam {int} destination_account_type 目标账户类型 1-bank，2-alipay，3-wechat
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1517818507"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recharge(Request $request)
    {
        $this->validate($request, [
            'amount' => ['bail', 'int', 'required'],
            'source_account_id' => ['bail', 'int', 'required'],
            'source_account_type' => ['bail', 'int', 'required', 'in:1,2,3'],
            'destination_account_id' => ['bail', 'int', 'required'],
            'destination_account_type' => ['bail', 'int', 'required', 'in:1,2,3'],
        ]);

        $recharge = $this->_walletService->prepareRecharge(AuthHelper::user()->id, $this->requestParams->getRegularParams());
        return $this->success($recharge);
    }

    /**
     *
     *
     * @api {DELETE} /api/user-fund/wallet/v1/recharge 关闭充值
     * @apiSampleRequest /api/user-fund/wallet/v1/recharge
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund
     * @apiName close-recharge
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[],"code":"0","msg":"","time":"1517818507"}
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function closeRecharge(Request $request, $id)
    {
        $this->_walletService->closeRecharge(AuthHelper::user()->id, $id);
        return $this->success([]);
    }

    public function withdrawList()
    {

    }

    /**
     *
     *
     * @api {POST} /api/user-fund/wallet/v1/withdraw 提现
     * @apiSampleRequest /api/user-fund/wallet/v1/withdraw
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund
     * @apiName withdraw
     *
     *
     * @apiParam {int} amount 金额
     * @apiParam {int} account_id 账户ID
     * @apiParam {int} captcha 验证码
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1517818507"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withdraw(Request $request)
    {
        $this->validate($request, [
            'amount' => ['bail', 'int', 'required'],
            'account_id' => ['bail', 'int', 'required'],
            'captcha' => 'bail|required|integer',
        ]);

        $withdraw = $this->_walletService->prepareWithdraw(AuthHelper::user()->id, $this->requestParams->getRegularParams());
        return $this->success($withdraw);
    }

    /**
     *
     *
     * @api {DELETE} /api/user-fund/wallet/v1/withdraw 关闭提现
     * @apiSampleRequest /api/user-fund/wallet/v1/withdraw
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund
     * @apiName close-withdraw
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[],"code":"0","msg":"","time":"1517818507"}
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function closeWithdraw(Request $request, $id)
    {
        $this->_walletService->closeWithdraw(AuthHelper::user()->id, $id);
        return $this->success([]);
    }
}