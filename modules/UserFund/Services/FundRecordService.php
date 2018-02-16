<?php

namespace Modules\UserFund\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\UserFund\Repositories\FundRecordRepositoryEloquent;

class FundRecordService extends BaseService
{
    protected $_fundRecordRepository;

    public function __construct(FundRecordRepositoryEloquent $fundRecordRepositoryEloquent)
    {
        $this->_fundRecordRepository = $fundRecordRepositoryEloquent;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }
}