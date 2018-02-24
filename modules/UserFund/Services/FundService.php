<?php

namespace Modules\UserFund\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Jiuyan\Common\Component\InFramework\Traits\ExceptionTrait;
use Modules\UserFund\Models\Fund;
use Modules\UserFund\Repositories\FundRepositoryEloquent;

class FundService extends BaseService
{
    public function __construct(FundRepositoryEloquent $fundRepositoryEloquent)
    {
        $this->setRepository($fundRepositoryEloquent);
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    /**
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Model|mixed|Fund|null|static|Fund
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\DBException
     */
    public function getByUserId($userId, $forUpdate = false)
    {
        //todo select for update
        $fund = $this->getRepository()->getByUserId($userId);
        $fund || $fund = $this->getRepository()->createFund($userId);
        $this->throwDBException($fund, "创建资金账户失败,user_id:{$userId}");

        return $fund;
    }

    public function recharge(Fund $fund, $amount)
    {
        return $this->getRepository()->recharge($fund, $amount);
    }

    public function prepareWithdraw(Fund $fund, $amount)
    {
        return $this->getRepository()->prepareWithdraw($fund, $amount);
    }

    public function withdraw(Fund $fund, $amount)
    {
        return $this->getRepository()->withdraw($fund, $amount);
    }

    public function cancelWithdraw(Fund $fund, $amount)
    {
        return $this->getRepository()->cancelWithdraw($fund, $amount);
    }

    public function checkBalance(Fund $fund, $amount)
    {
        return $fund->amount >= $amount;
    }

    public function checkLocked(Fund $fund, $amount)
    {
        return $fund->locked >= $amount;
    }

    public function earn(Fund $fund, $amount)
    {
        return $this->getRepository()->earn($fund, $amount);
    }

    public function pay(Fund $fund, $amount)
    {
        return $this->getRepository()->pay($fund, $amount);
    }


    /**
     * @return FundRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }
}