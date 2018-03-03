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
     * @apiGroup user-fund-wallet
     * @apiName detail
     *
     *
     * @apiSuccess {int} user_id
     * @apiSuccess {int} amount 余额
     * @apiSuccess {int} locked 被锁
     * @apiSuccess {int} total_earn 总收入
     * @apiSuccess {int} total_pay 总支出
     * @apiSuccess {int} total_withdraw 总提现
     * @apiSuccess {int} total_recharge 总充值
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"user_id":"10","amount":"990","locked":"10","total_earn":"1","total_pay":"1","total_withdraw":"0","total_recharge":"0","created_at":"1519821575"},"code":"0","msg":"","time":"1520045351"}
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
     * @api {GET} /api/user-fund/wallet/v1/fund-record 资金记录列表
     * @apiSampleRequest /api/user-fund/wallet/v1/fund-record
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund-wallet
     * @apiName fund-record-list
     *
     * @apiParam {int} [record_type] 类型：1-withdraw，2-recharge，3-pay，4-earn
     * @apiParam {int} [record_status] 状态：0-verifying，1-done，2-failed，3-close
     *
     * @apiError  20113
     *
     * @apiSuccess {int} amount
     * @apiSuccess {int} actual_amount
     * @apiSuccess {int} commission 佣金
     * @apiSuccess {int} record_type
     * @apiSuccess {int} record_status
     * @apiSuccess {string} remarks 备注
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"current_page":"1","data":[{"id":"1","user_id":"10","amount":"1","actual_amount":"1","commission":"0","record_type":"3","record_status":"1","remarks":"","created_at":"1519827643"},{"id":"2","user_id":"10","amount":"1","actual_amount":"1","commission":"0","record_type":"4","record_status":"1","remarks":"","created_at":"1519827643"},{"id":"3","user_id":"10","amount":"100","actual_amount":"100","commission":"0","record_type":"2","record_status":"0","remarks":"\u63d0\u73b0","created_at":"1520045510"}],"from":"1","last_page":"1","next_page_url":"","path":"http:\/\/test.lumio.com\/api\/user-fund\/wallet\/v1\/fund-record","per_page":"3","prev_page_url":"","to":"3","total":"3"},"code":"0","msg":"","time":"1520046720"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fundRecordList(Request $request)
    {
        $this->validate($request, [
            'record_type' => ['in:1,2,3,4'],
            'record_status' => ['in:0,1,2,3'],
        ]);

        $params = $this->requestParams->getRegularParams();
        $params = array_filter($params, function ($val) {
            return $val != "";
        });

        $conditions = [
            "user_id" => AuthHelper::user()->id
        ];
        isset($params['record_type']) && $conditions['record_type'] = $params['record_type'];
        isset($params['record_status']) && $conditions['record_status'] = $params['record_status'];

        $result = $this->_walletService->fundRecordList($conditions);
        return $this->success($result);
    }

    /**
     *
     *
     * @api {GET} /api/user-fund/v1/recharge 充值列表
     * @apiSampleRequest /api/user-fund/v1/recharge
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund-wallet
     * @apiName recharge-list
     *
     * @apiParam {int} [recharge_status] 充值状态
     * @apiParam {int} [source_account_type] 来源账户类型
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"current_page":"1","data":[{"id":"1","user_id":"10","fund_record_id":"3","amount":"100","source_account_type":"1","source_account_id":"4","destination_account_id":"1","destination_account_type":"1","recharge_time":"1520045510","recharge_status":"0","verify_time":"1","verify_remark":"","verify_user_id":"0","created_at":"1520045510"}],"from":"1","last_page":"1","next_page_url":"","path":"http:\/\/test.lumio.com\/api\/user-fund\/wallet\/v1\/recharge","per_page":"10","prev_page_url":"","to":"1","total":"1"},"code":"0","msg":"","time":"1520046479"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rechargeList(Request $request)
    {
        $this->validate($request, [
            'recharge_status' => ['in:0,1,2,3'],
            'destination_account_type' => ['in:1,2,3'],
        ]);

        $params = $this->requestParams->getRegularParams();
        $params = array_filter($params, function ($val) {
            return $val != "";
        });

        $conditions = [
            "user_id" => AuthHelper::user()->id
        ];
        isset($params['recharge_status']) && $conditions['recharge_status'] = $params['recharge_status'];
        isset($params['destination_account_type']) && $conditions['destination_account_type'] = $params['destination_account_type'];

        $result = $this->_walletService->rechargeList($conditions);
        return $this->success($result);
    }

    /**
     *
     *
     * @api {POST} /api/user-fund/wallet/v1/recharge 充值
     * @apiSampleRequest /api/user-fund/wallet/v1/recharge
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund-wallet
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
     * @apiSuccess {int} amount
     * @apiSuccess {int} source_account_id
     * @apiSuccess {int} source_account_type
     * @apiSuccess {int} destination_account_id
     * @apiSuccess {int} destination_account_type 目标账号类型：1-bank，2-alipay，3-wechat
     * @apiSuccess {int} user_id
     * @apiSuccess {int} fund_record_id
     * @apiSuccess {int} recharge_status 充值状态：0-waiting，1-passed，2-failed，3-close
     * @apiSuccess {int} recharge_time
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"amount":"100","source_account_id":"4","source_account_type":"1","destination_account_id":"1","destination_account_type":"1","user_id":"10","fund_record_id":"3","recharge_status":"0","recharge_time":"1520045510","created_at":"1520045510","id":"1"},"code":"0","msg":"","time":"1520045510"}
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
     * @apiGroup user-fund-wallet
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

    /**
     *
     *
     * @api {GET} /api/user-fund/v1/withdraw 提现列表
     * @apiSampleRequest /api/user-fund/v1/withdraw
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund-wallet
     * @apiName withdraw-list
     *
     * @apiParam {int} [withdraw_status] 充值状态
     * @apiParam {int} [account_type] 来源账户类型
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"current_page":"1","data":[{"id":"1","user_id":"10","fund_record_id":"4","amount":"100","account_id":"4","account_type":"1","withdraw_status":"0","withdraw_time":"1520046897","verify_remark":"","verify_time":"0","verify_user_id":"0","created_at":"1520046897"}],"from":"1","last_page":"1","next_page_url":"","path":"http:\/\/test.lumio.com\/api\/user-fund\/wallet\/v1\/withdraw","per_page":"10","prev_page_url":"","to":"1","total":"1"},"code":"0","msg":"","time":"1520047068"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withdrawList(Request $request)
    {
        $this->validate($request, [
            'withdraw_status' => ['in:0,1,2,3'],
            'account_type' => ['in:1,2,3'],
        ]);

        $params = $this->requestParams->getRegularParams();
        $params = array_filter($params, function ($val) {
            return $val != "";
        });

        $conditions = [
            "user_id" => AuthHelper::user()->id
        ];
        isset($params['withdraw_status']) && $conditions['withdraw_status'] = $params['withdraw_status'];
        isset($params['account_type']) && $conditions['account_type'] = $params['account_type'];

        $result = $this->_walletService->withdrawList($conditions);
        return $this->success($result);
    }

    /**
     *
     *
     * @api {POST} /api/user-fund/wallet/v1/withdraw 提现
     * @apiSampleRequest /api/user-fund/wallet/v1/withdraw
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund-wallet
     * @apiName withdraw
     *
     *
     * @apiParam {int} amount 金额
     * @apiParam {int} account_id 账户ID
     * @apiParam {int} captcha 验证码
     *
     * @apiError  20113
     *
     * @apiSuccess {int} amount
     * @apiSuccess {int} account_id
     * @apiSuccess {int} account_type 账号类型：1-bank，2-alipay，3-wechat
     * @apiSuccess {int} user_id
     * @apiSuccess {int} fund_record_id
     * @apiSuccess {int} withdraw_status 提现状态：0-waiting，1-passed，2-failed，3-close
     * @apiSuccess {int} withdraw_time
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"amount":"100","account_id":"4","user_id":"10","fund_record_id":"4","account_type":"1","withdraw_status":"0","withdraw_time":"1520046897","created_at":"1520046897","id":"1"},"code":"0","msg":"","time":"1520046897"}
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
     * @apiGroup user-fund-wallet
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