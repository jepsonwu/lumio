<?php

namespace Modules\UserFund\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Jiuyan\Common\Component\InFramework\Traits\ExceptionTrait;
use Modules\UserFund\Models\Fund;
use Modules\UserFund\Repositories\FundRepositoryEloquent;

class FundService extends BaseService
{
    use ExceptionTrait;

    protected $_fundRepository;

    public function __construct(FundRepositoryEloquent $fundRepositoryEloquent)
    {
        $this->_fundRepository = $fundRepositoryEloquent;
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
        $fund = $this->_fundRepository->getByUserId($userId);
        $fund || $fund = $this->_fundRepository->createFund($userId);
        $this->throwDBException($fund, "创建资金账户失败,user_id:{$userId}");

        return $fund;
    }

    public function recharge(Fund $fund, $amount)
    {
        return $this->_fundRepository->recharge($fund, $amount);
    }

    public function prepareWithdraw(Fund $fund, $amount)
    {
        return $this->_fundRepository->prepareWithdraw($fund, $amount);
    }

    public function withdraw(Fund $fund, $amount)
    {
        return $this->_fundRepository->withdraw($fund, $amount);
    }

    public function cancelWithdraw(Fund $fund, $amount)
    {
        return $this->_fundRepository->cancelWithdraw($fund, $amount);
    }

    public function checkBalance(Fund $fund, $amount)
    {
        return $fund->amount >= $amount;
    }

    public function checkLocked(Fund $fund, $amount)
    {
        return $fund->locked >= $amount;
    }

    /**
     * @return FundRepositoryEloquent
     */
    public function getRepository()
    {
        return $this->_fundRepository;
    }
}