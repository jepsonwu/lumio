<?php

namespace Modules\UserFund\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;

class UserFundInternalService extends BaseService
{
    protected $accountService;
    protected $walletService;

    public function __construct(AccountService $accountService, WalletService $walletService)
    {
        $this->accountService = $accountService;
        $this->walletService = $walletService;
    }

    public function isFinishedDeployAccount($userId)
    {
        $this->accountService->checkDeployAccount($userId);
    }

    //todo change name
    public function isFinishedDeployWallet($userId)
    {

    }
}