<?php

namespace Modules\UserFund\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;

class WalletService extends BaseService
{
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

    }

    public function passRecharge($id)
    {

    }

    public function failRecharge($id)
    {

    }

    public function closeRecharge($id)
    {

    }

    public function prepareWithdraw($userId, $amount, $captcha)
    {

    }

    public function passWithdraw($id)
    {

    }

    public function failWithdraw($id)
    {

    }

    public function closeWithdraw($id)
    {

    }

    public function earn()
    {

    }

    public function pay()
    {

    }

    public function recordList()
    {

    }
}