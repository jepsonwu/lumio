<?php

namespace Modules\UserFund\Services;

use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\UserFund\Constants\UserFundErrorConstant;
use Modules\UserFund\Models\Fund;
use Modules\UserFund\Models\FundRecord;

class WalletService extends BaseService
{
    const WITHDRAW_COMMISSION_PERCENT = 10;//万分之几

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
            //todo check balance select for update


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

    public function checkBalance($userId, $amount, $forUpdate = false)
    {
        $fund = $this->_fundService->getByUserId($userId, $forUpdate);
        $this->_fundService->checkBalance($fund, $amount)
        || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_INSUFFICIENT_BALANCE);

        return $fund;
    }

    public function checkLocked($userId, $amount, $forUpdate = false)
    {
        $fund = $this->_fundService->getByUserId($userId, $forUpdate);
        $this->_fundService->checkLocked($fund, $amount)
        || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_INSUFFICIENT_LOCKED);

        return $fund;
    }

    public function lock(Fund $fund, $amount)
    {
        return $fund->lockAmount($amount);
    }

    public function unlock(Fund $fund, $amount)
    {
        return $fund->unlockAmount($amount);
    }

    public function earn($userId, $amount, $commission, $remarks)
    {
        $fund = $this->_fundService->getByUserId($userId, true);
        $this->throwDBException(
            $this->_fundRecordService->earn($userId, $amount, $commission, $remarks),
            "创建赚取记录失败"
        );

        $this->throwDBException(
            $this->_fundService->earn($fund, $amount),
            "赚取资金失败"
        );

        return true;
    }

    public function pay($userId, $amount, $remarks)
    {
        $fund = $this->_fundService->getByUserId($userId, true);

        $this->throwDBException(
            $this->_fundRecordService->pay($userId, $amount, $remarks),
            "创建支付记录失败"
        );

        $this->throwDBException(
            $this->_fundService->pay($fund, $amount),
            "支付资金失败"
        );

        return true;
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

    public function getRepository()
    {
        return [$this->_fundService->getRepository(), $this->_fundRecordService->getRepository()];
    }
}