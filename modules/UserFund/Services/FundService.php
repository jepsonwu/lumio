<?php

namespace Modules\UserFund\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\UserFund\Repositories\FundRepositoryEloquent;

class FundService extends BaseService
{
    protected $_fundRepository;

    public function __construct(FundRepositoryEloquent $fundRepositoryEloquent)
    {
        $this->_fundRepository = $fundRepositoryEloquent;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }
}