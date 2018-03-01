<?php

namespace Modules\UserFund\Services;

use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\UserFund\Constants\UserFundErrorConstant;
use Modules\UserFund\Models\FundRecharge;
use Modules\UserFund\Repositories\FundRechargeRepositoryEloquent;

class FundRechargeService extends BaseService
{
    public function __construct(FundRechargeRepositoryEloquent $fundRechargeRepositoryEloquent)
    {
        $this->setRepository($fundRechargeRepositoryEloquent);
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    /**
     * @param $id
     * @return \Illuminate\Support\Collection|mixed|\Prettus\Repository\Database\Eloquent\Model|FundRecharge
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function isValidRecharge($id)
    {
        $recharge = $this->getRepository()->find($id);
        $recharge ||
        ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_INVALID_RECHARGE);

        return $recharge;
    }

    public function isMyValidRecharge($userId, $rechargeId)
    {
        $recharge = $this->isValidRecharge($rechargeId);
        $this->isAllowOperate($userId, $recharge);
        return $recharge;
    }

    protected function isAllowOperate($userId, FundRecharge $fundRecharge)
    {
        $userId == $fundRecharge->user_id
        || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_OPERATE_ILLEGAL);
    }

    public function isAllowClose(FundRecharge $fundRecharge)
    {
        $fundRecharge->isWaiting()
        || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_DISALLOW_CLOSE_RECHARGE);
    }

    public function isAllowVerify(FundRecharge $fundRecharge)
    {
        $fundRecharge->isWaiting()
        || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_DISALLOW_VERIFY_RECHARGE);
    }


    public function prepareRecharge($userId, $fundRecordId, $attributes)
    {
        $attributes['user_id'] = $userId;
        $attributes['fund_record_id'] = $fundRecordId;
        $attributes['source_account_type'] = FundRecharge::ACCOUNT_TYPE_BACK;
        $attributes['destination_account_type'] = FundRecharge::ACCOUNT_TYPE_BACK;
        $attributes['recharge_status'] = FundRecharge::RECHARGE_STATUS_VERIFYING;
        $attributes['recharge_time'] = time();
        $attributes['created_at'] = time();

        return $this->getRepository()->create($attributes);
    }

    public function pass(FundRecharge $fundRecharge, $verifyUserId)
    {
        return $this->getRepository()->pass($fundRecharge, $verifyUserId);
    }

    public function fail(FundRecharge $fundRecharge, $verifyUserId, $reason)
    {
        return $this->getRepository()->fail($fundRecharge, $verifyUserId, $reason);
    }

    public function close(FundRecharge $fundRecharge)
    {
        return $this->getRepository()->close($fundRecharge);
    }

    public function list($conditions)
    {
        return $this->getRepository()->paginateWithWhere($conditions);
    }

    /**
     * @return mixed|FundRechargeRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }
}