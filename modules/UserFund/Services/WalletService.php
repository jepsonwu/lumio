<?php

namespace Modules\UserFund\Services;

use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Jiuyan\Common\Component\InFramework\Traits\DBTrait;
use Jiuyan\Common\Component\InFramework\Traits\ExceptionTrait;
use Modules\UserFund\Constants\UserFundErrorConstant;
use Modules\UserFund\Models\FundRecord;

class WalletService extends BaseService
{
    const WITHDRAW_COMMISSION_PERCENT = 10;//万分之几

    use ExceptionTrait;
    use DBTrait;

    protected $_fundService;
    protected $_fundRecordService;

    public function __construct(FundService $fundService, FundRecordService $fundRecordService)
    {
        $this->_fundService = $fundService;
        $this->_fundRecordService = $fundRecordService;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function prepareRecharge($userId, $amount, $orderId)
    {
        $remarks = "订单号：[$orderId]";
        $result = $this->_fundRecordService->prepareRecharge($userId, $amount, $remarks);
        $result || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_RECHARGE_FAILED);
        return true;
    }

    public function passRecharge($id)
    {
        $record = $this->_fundRecordService->isValidRecord($id);

        return $this->doingTransaction(function () use ($record) {
            $this->throwDBExceptionByFalse(
                $this->_fundRecordService->pass($record),
                "审核充值记录失败,record_id:" . $record->id
            );

            $fund = $this->_fundService->getByUserId($record->user_id);
            $this->throwDBExceptionByFalse(
                $this->_fundService->recharge($fund, $this->makeRecordAmount($record)),
                "添加充值金额失败,record_id:" . $record->id
            );
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_VERIFY_RECHARGE_FAILED);
    }

    public function failRecharge($id)
    {
        $record = $this->_fundRecordService->isValidRecord($id);
        $result = $this->_fundRecordService->fail($record);
        $result === false
        && ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_VERIFY_RECHARGE_FAILED);

        return true;
    }

    public function closeRecharge($id)
    {
        $record = $this->_fundRecordService->isValidRecord($id);
        $result = $this->_fundRecordService->close($record);
        $result === false
        && ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_VERIFY_RECHARGE_FAILED);

        return true;
    }

    public function prepareWithdraw($userId, $amount, $captcha)
    {
        //todo is valid captcha

        return $this->doingTransaction(function () use ($userId, $amount) {
            $result = $this->_fundRecordService->prepareWithdraw(
                $userId,
                $amount,
                $this->makeWithdrawCommission($amount),
                ""
            );
            $this->throwDBException($result, "添加提现记录失败,user_id:{$userId},amount:{$amount}");

            $fund = $this->_fundService->getByUserId($userId);
            $this->throwDBExceptionByFalse(
                $this->_fundService->prepareWithdraw($fund, $amount),
                "准备提现失败,user_id:{$userId},amount:{$amount}"
            );
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_WITHDRAW_FAILED);
    }

    public function passWithdraw($id)
    {
        $record = $this->_fundRecordService->isValidRecord($id);

        return $this->doingTransaction(function () use ($record) {
            $this->throwDBExceptionByFalse(
                $this->_fundRecordService->pass($record),
                "审核提现记录失败,record_id:" . $record->id
            );

            $fund = $this->_fundService->getByUserId($record->user_id);
            $this->throwDBExceptionByFalse(
                $this->_fundService->withdraw($fund, $this->makeRecordAmount($record)),
                "添加提现金额失败,record_id:" . $record->id
            );
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_VERIFY_WITHDRAW_FAILED);
    }

    public function failWithdraw($id)
    {
        $record = $this->_fundRecordService->isValidRecord($id);

        return $this->doingTransaction(function () use ($record) {
            $this->throwDBExceptionByFalse(
                $this->_fundRecordService->fail($record),
                "审核提现记录失败,record_id:" . $record->id
            );

            $fund = $this->_fundService->getByUserId($record->user_id);
            $this->throwDBExceptionByFalse(
                $this->_fundService->cancelWithdraw($fund, $this->makeRecordAmount($record)),
                "添加提现金额失败,record_id:" . $record->id
            );
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_VERIFY_WITHDRAW_FAILED);
    }

    public function closeWithdraw($id)
    {
        $record = $this->_fundRecordService->isValidRecord($id);

        return $this->doingTransaction(function () use ($record) {
            $this->throwDBExceptionByFalse(
                $this->_fundRecordService->pass($record),
                "关闭提现记录失败,record_id:" . $record->id
            );

            $fund = $this->_fundService->getByUserId($record->user_id);
            $this->throwDBExceptionByFalse(
                $this->_fundService->cancelWithdraw($fund, $this->makeRecordAmount($record)),
                "添加提现金额失败,record_id:" . $record->id
            );
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_VERIFY_WITHDRAW_FAILED);
    }

    public function earn()
    {

    }

    public function pay()
    {

    }

    public function info($userId)
    {
        return $this->_fundService->getByUserId($userId);
    }

    public function recordList($userId, $conditions = [])
    {
        $conditions[] = ["user_id", $userId];
        return $this->_fundRecordService->list($conditions);
    }

    protected function makeWithdrawCommission($amount)
    {
        return self::WITHDRAW_COMMISSION_PERCENT * $amount / 10000;
    }

    protected function makeRecordAmount(FundRecord $record)
    {
        return $record->amount - $record->commission;
    }
}