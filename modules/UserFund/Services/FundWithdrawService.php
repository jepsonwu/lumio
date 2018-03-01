<?php

namespace Modules\UserFund\Services;

use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\UserFund\Constants\UserFundErrorConstant;
use Modules\UserFund\Models\FundWithdraw;
use Modules\UserFund\Repositories\FundWithdrawRepositoryEloquent;

class FundWithdrawService extends BaseService
{
    public function __construct(FundWithdrawRepositoryEloquent $fundWithdrawRepositoryEloquent)
    {
        $this->setRepository($fundWithdrawRepositoryEloquent);
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    /**
     * @param $id
     * @return \Illuminate\Support\Collection|mixed|\Prettus\Repository\Database\Eloquent\Model|FundWithdraw
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function isValidWithdraw($id)
    {
        $withdraw = $this->getRepository()->find($id);
        $withdraw ||
        ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_INVALID_WITHDRAW);

        return $withdraw;
    }

    public function isMyValidWithdraw($userId, $withdrawId)
    {
        $withdraw = $this->isValidWithdraw($withdrawId);
        $this->isAllowOperate($userId, $withdraw);
        return $withdraw;
    }

    protected function isAllowOperate($userId, FundWithdraw $fundWithdraw)
    {
        $userId == $fundWithdraw->user_id
        || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_OPERATE_ILLEGAL);
    }

    public function isAllowClose(FundWithdraw $fundWithdraw)
    {
        $fundWithdraw->isWaiting()
        || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_DISALLOW_CLOSE_WITHDRAW);
    }

    public function isAllowVerify(FundWithdraw $fundWithdraw)
    {
        $fundWithdraw->isWaiting()
        || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_DISALLOW_VERIFY_WITHDRAW);
    }


    public function prepareWithdraw($userId, $fundRecordId, $attributes)
    {
        $attributes['user_id'] = $userId;
        $attributes['fund_record_id'] = $fundRecordId;
        $attributes['account_type'] = FundWithdraw::ACCOUNT_TYPE_BACK;
        $attributes['withdraw_status'] = FundWithdraw::WITHDRAW_STATUS_VERIFYING;
        $attributes['withdraw_time'] = time();
        $attributes['created_at'] = time();

        return $this->getRepository()->create($attributes);
    }

    public function pass(FundWithdraw $fundWithdraw, $verifyUserId)
    {
        return $this->getRepository()->pass($fundWithdraw, $verifyUserId);
    }

    public function fail(FundWithdraw $fundWithdraw, $verifyUserId, $reason)
    {
        return $this->getRepository()->fail($fundWithdraw, $verifyUserId, $reason);
    }

    public function close(FundWithdraw $fundWithdraw)
    {
        return $this->getRepository()->close($fundWithdraw);
    }

    public function list($conditions)
    {
        return $this->getRepository()->paginateWithWhere($conditions);
    }

    /**
     * @return mixed|FundWithdrawRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }
}