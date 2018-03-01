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
    protected $_fundWithdrawService;
    protected $_fundRechargeService;
    protected $_accountService;

    public function __construct(
        FundService $fundService,
        FundRecordService $fundRecordService,
        FundWithdrawService $fundWithdrawService,
        FundRechargeService $fundRechargeService,
        AccountService $accountService
    )
    {
        $this->_fundService = $fundService;
        $this->_fundRecordService = $fundRecordService;
        $this->_fundWithdrawService = $fundWithdrawService;
        $this->_fundRechargeService = $fundRechargeService;
        $this->_accountService = $accountService;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function info($userId)
    {
        return $this->_fundService->getByUserId($userId);
    }

    public function fundRecordList($conditions)
    {
        return $this->_fundRecordService->list($conditions);
    }

    public function rechargeList()
    {

    }

    public function checkRechargePermission($userId)
    {
        $this->_accountService->checkDeployAccount($userId);
        return true;
    }

    public function prepareRecharge($userId, $attributes)
    {
        $this->_accountService->isMyValidAccount($userId, $attributes['source_account_id']);
        //todo check system account

        return $this->doingTransaction(function () use ($userId, $attributes) {
            $amount = $attributes['amount'];

            $errorMessage = ",user_id:{$userId},attributes:" . json_encode($attributes);

            $fundRecord = $this->_fundRecordService->prepareRecharge(
                $userId,
                $amount,
                "提现"
            );
            $this->throwDBException(
                $fundRecord,
                "fund record prepare recharge failed{$errorMessage}"
            );

            $recharge = $this->_fundRechargeService->prepareRecharge($userId, $fundRecord->id, $attributes);

            $this->throwDBException(
                $recharge,
                "fund recharge prepare failed{$errorMessage}"
            );

            return $recharge;
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundRechargeService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_RECHARGE_FAILED);
    }

    public function passRecharge($rechargeId, $verifyUserId)
    {
        $recharge = $this->_fundRechargeService->isValidRecharge($rechargeId);
        $record = $this->_fundRecordService->isValidRecord($recharge->fund_record_id);
        $this->_fundRechargeService->isAllowVerify($recharge);

        return $this->doingTransaction(function () use ($recharge, $record, $verifyUserId) {
            $fund = $this->_fundService->getByUserId($record->user_id, true);

            $errorMessage = "recharge_id:{$recharge->id},verify_user_id:{$verifyUserId}";
            $this->throwDBException(
                $this->_fundRechargeService->pass($recharge, $verifyUserId),
                "fund recharge pass failed{$errorMessage}"
            );

            $this->throwDBException(
                $this->_fundRecordService->pass($record),
                "fund record pass failed{$errorMessage}"
            );

            $this->throwDBException(
                $this->_fundService->recharge($fund, $this->makeRecordAmount($record)),
                "fund recharge failed{$errorMessage}"
            );

            return true;
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository(),
            $this->_fundWithdrawService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_VERIFY_RECHARGE_FAILED);
    }

    public function failRecharge($rechargeId, $verifyUserId, $reason)
    {
        $recharge = $this->_fundRechargeService->isValidRecharge($rechargeId);
        $record = $this->_fundRecordService->isValidRecord($recharge->fund_record_id);
        $this->_fundRechargeService->isAllowVerify($recharge);

        return $this->doingTransaction(function () use ($recharge, $record, $verifyUserId, $reason) {
            $errorMessage = "recharge_id:{$recharge->id},verify_user_id:{$verifyUserId},reason:{$reason}";
            $this->throwDBException(
                $this->_fundRechargeService->fail($recharge, $verifyUserId, $reason),
                "fund recharge fail failed{$errorMessage}"
            );

            $this->throwDBException(
                $this->_fundRecordService->fail($record),
                "fund record fail failed{$errorMessage}"
            );

            return true;
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundWithdrawService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_VERIFY_RECHARGE_FAILED);
    }

    public function closeRecharge($userId, $rechargeId)
    {
        $recharge = $this->_fundRechargeService->isValidRecharge($rechargeId);
        $record = $this->_fundRecordService->isValidRecord($recharge->fund_record_id);
        $this->_fundRechargeService->isAllowVerify($recharge);

        return $this->doingTransaction(function () use ($recharge, $record, $userId) {
            $errorMessage = ",user_id:{$userId},recharge_id:{$recharge->id}";
            $this->throwDBException(
                $this->_fundRechargeService->close($recharge),
                "fund recharge close failed{$errorMessage}"
            );

            $this->throwDBException(
                $this->_fundRecordService->close($record),
                "fund record close failed{$errorMessage}"
            );

            return true;
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository(),
            $this->_fundWithdrawService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_VERIFY_RECHARGE_FAILED);
    }

    public function withdrawList()
    {

    }

    public function checkWithdrawPermission($userId)
    {
        $this->_accountService->checkDeployAccount($userId);
        return true;
    }

    public function prepareWithdraw($userId, $attributes)
    {
        $this->checkWithdrawCaptcha($attributes['captcha']);
        $this->_accountService->isMyValidAccount($userId, $attributes['account_id']);

        return $this->doingTransaction(function () use ($userId, $attributes) {
            $amount = $attributes['amount'];
            $fund = $this->checkBalance($userId, $amount, true);

            $errorMessage = ",user_id:{$userId},attributes:" . json_encode($attributes);

            $fundRecord = $this->_fundRecordService->prepareWithdraw(
                $userId,
                $amount,
                $this->makeWithdrawCommission($amount),
                "充值"
            );
            $this->throwDBException(
                $fundRecord,
                "fund record prepare withdraw failed{$errorMessage}"
            );

            $withdraw = $this->_fundWithdrawService->prepareWithdraw($userId, $fundRecord->id, $attributes);

            $this->throwDBException(
                $withdraw,
                "fund withdraw prepare failed{$errorMessage}"
            );

            $this->throwDBException(
                $this->_fundService->prepareWithdraw($fund, $amount),
                "fund prepare withdraw failed{$errorMessage}"
            );

            return $withdraw;
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository(),
            $this->_fundWithdrawService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_WITHDRAW_FAILED);
    }

    //todo check captcha
    protected function checkWithdrawCaptcha($captcha)
    {
        $result = true;
        $result
        || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_INVALID_CAPTCHA);
    }

    public function passWithdraw($withdrawId, $verifyUserId)
    {
        $withdraw = $this->_fundWithdrawService->isValidWithdraw($withdrawId);
        $record = $this->_fundRecordService->isValidRecord($withdraw->fund_record_id);
        $this->_fundWithdrawService->isAllowVerify($withdraw);

        return $this->doingTransaction(function () use ($withdraw, $record, $verifyUserId) {
            $fund = $this->_fundService->getByUserId($record->user_id, true);

            $errorMessage = "withdraw_id:{$withdraw->id},verify_user_id:{$verifyUserId}";
            $this->throwDBException(
                $this->_fundWithdrawService->pass($withdraw, $verifyUserId),
                "fund withdraw pass failed{$errorMessage}"
            );

            $this->throwDBException(
                $this->_fundRecordService->pass($record),
                "fund record pass failed{$errorMessage}"
            );

            $this->throwDBException(
                $this->_fundService->withdraw($fund, $this->makeRecordAmount($record)),
                "fund withdraw failed{$errorMessage}"
            );

            return true;
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository(),
            $this->_fundWithdrawService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_VERIFY_WITHDRAW_FAILED);
    }

    public function failWithdraw($withdrawId, $verifyUserId, $reason)
    {
        $withdraw = $this->_fundWithdrawService->isValidWithdraw($withdrawId);
        $record = $this->_fundRecordService->isValidRecord($withdraw->fund_record_id);
        $this->_fundWithdrawService->isAllowVerify($withdraw);

        return $this->doingTransaction(function () use ($withdraw, $record, $verifyUserId, $reason) {
            $fund = $this->_fundService->getByUserId($record->user_id, true);

            $errorMessage = "withdraw_id:{$withdraw->id},verify_user_id:{$verifyUserId},reason:{$reason}";
            $this->throwDBException(
                $this->_fundWithdrawService->fail($withdraw, $verifyUserId, $reason),
                "fund withdraw fail failed{$errorMessage}"
            );

            $this->throwDBException(
                $this->_fundRecordService->fail($record),
                "fund record fail failed{$errorMessage}"
            );

            $this->throwDBException(
                $this->_fundService->cancelWithdraw($fund, $this->makeRecordAmount($record)),
                "fund cancel withdraw failed{$errorMessage}"
            );

            return true;
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository(),
            $this->_fundWithdrawService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_VERIFY_WITHDRAW_FAILED);
    }

    public function closeWithdraw($userId, $withdrawId)
    {
        $withdraw = $this->_fundWithdrawService->isMyValidWithdraw($userId, $withdrawId);
        $record = $this->_fundRecordService->isValidRecord($withdraw->fund_record_id);
        $this->_fundWithdrawService->isAllowClose($withdraw);

        return $this->doingTransaction(function () use ($withdraw, $record, $userId) {
            $fund = $this->_fundService->getByUserId($record->user_id, true);

            $errorMessage = ",user_id:{$userId},withdraw_id:{$withdraw->id}";
            $this->throwDBException(
                $this->_fundWithdrawService->close($withdraw),
                "fund withdraw close failed{$errorMessage}"
            );

            $this->throwDBException(
                $this->_fundRecordService->close($record),
                "fund record close failed{$errorMessage}"
            );


            $this->throwDBException(
                $this->_fundService->cancelWithdraw($fund, $this->makeRecordAmount($record)),
                "fund cancel withdraw failed:{$errorMessage}"
            );

            return true;
        }, new Collection([
            $this->_fundRecordService->getRepository(),
            $this->_fundService->getRepository(),
            $this->_fundWithdrawService->getRepository()
        ]), UserFundErrorConstant::ERR_WALLET_CLOSE_WITHDRAW_FAILED);
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
            "fund record earn failed"
        );

        $this->throwDBException(
            $this->_fundService->earn($fund, $amount),
            "fund earn failed"
        );

        return true;
    }

    public function pay($userId, $amount, $remarks)
    {
        $fund = $this->_fundService->getByUserId($userId, true);

        $this->throwDBException(
            $this->_fundRecordService->pay($userId, $amount, $remarks),
            "fund record pay failed"
        );

        $this->throwDBException(
            $this->_fundService->pay($fund, $amount),
            "fund pay failed"
        );

        return true;
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