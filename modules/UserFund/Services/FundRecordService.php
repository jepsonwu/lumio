<?php

namespace Modules\UserFund\Services;

use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\UserFund\Constants\UserFundErrorConstant;
use Modules\UserFund\Models\FundRecord;
use Modules\UserFund\Repositories\FundRecordRepositoryEloquent;

class FundRecordService extends BaseService
{
    protected $_fundRecordRepository;

    public function __construct(FundRecordRepositoryEloquent $fundRecordRepositoryEloquent)
    {
        $this->_fundRecordRepository = $fundRecordRepositoryEloquent;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function prepareRecharge($userId, $amount, $remarks)
    {
        return $this->_fundRecordRepository->prepareRecharge($userId, $amount, $remarks);
    }

    public function prepareWithdraw($userId, $amount, $commission, $remarks)
    {
        return $this->_fundRecordRepository->prepareWithdraw($userId, $amount, $commission, $remarks);
    }

    /**
     * @param $id
     * @return \Illuminate\Support\Collection|mixed|\Prettus\Repository\Database\Eloquent\Model|FundRecord
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function isValidRecord($id)
    {
        $record = $this->_fundRecordRepository->find($id);
        $record || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_WALLET_INVALID_RECORD);

        return $record;
    }

    public function pass(FundRecord $record)
    {
        return $this->_fundRecordRepository->pass($record);
    }

    public function fail(FundRecord $record)
    {
        return $this->_fundRecordRepository->fail($record);
    }

    public function close(FundRecord $record)
    {
        return $this->_fundRecordRepository->close($record);
    }

    public function list($conditions)
    {
        return $this->_fundRecordRepository->paginateWithWhere($conditions);
    }

    /**
     * @return FundRecordRepositoryEloquent
     */
    public function getRepository()
    {
        return $this->_fundRecordRepository;
    }
}