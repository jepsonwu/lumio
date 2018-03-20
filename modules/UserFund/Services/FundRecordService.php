<?php

namespace Modules\UserFund\Services;

use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\UserFund\Constants\UserFundErrorConstant;
use Modules\UserFund\Models\FundRecord;
use Modules\UserFund\Repositories\FundRecordRepositoryEloquent;

class FundRecordService extends BaseService
{
    public function __construct(FundRecordRepositoryEloquent $fundRecordRepositoryEloquent)
    {
        $this->setRepository($fundRecordRepositoryEloquent);
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    /**
     * @param $userId
     * @param $amount
     * @param $remarks
     * @return mixed|FundRecord
     */
    public function prepareRecharge($userId, $amount, $remarks)
    {
        return $this->getRepository()->prepareRecharge($userId, $amount, $remarks);
    }

    /**
     * @param $userId
     * @param $amount
     * @param $commission
     * @param $remarks
     * @return mixed|FundRecord
     */
    public function prepareWithdraw($userId, $amount, $commission, $remarks)
    {
        return $this->getRepository()->prepareWithdraw($userId, $amount, $commission, $remarks);
    }

    /**
     * @param $id
     * @return \Illuminate\Support\Collection|mixed|\Prettus\Repository\Database\Eloquent\Model|FundRecord
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function isValidRecord($id)
    {
        $record = $this->getRepository()->find($id);
        $record || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_INVALID_RECORD);

        return $record;
    }

    public function pay($userId, $amount, $commission, $remarks)
    {
        return $this->getRepository()->pay($userId, $amount, $commission, $remarks);
    }

    public function earn($userId, $amount, $remarks)
    {
        return $this->getRepository()->earn($userId, $amount, $remarks);
    }

    public function pass(FundRecord $record)
    {
        return $this->getRepository()->pass($record);
    }

    public function fail(FundRecord $record)
    {
        return $this->getRepository()->fail($record);
    }

    public function close(FundRecord $record)
    {
        return $this->getRepository()->close($record);
    }

    public function list($conditions)
    {
        return $this->getRepository()->paginateWithWhere($conditions);
    }

    /**
     * @return mixed|FundRecordRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }
}