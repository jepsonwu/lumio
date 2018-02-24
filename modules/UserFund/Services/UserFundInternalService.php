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

    //todo 分模块 需要处理code码
    public function checkBalance($userId, $amount)
    {
        return $this->walletService->checkBalance($userId, $amount);
    }

    //完全分模块中 所有对外的方法 不能传递对象
    public function lock($userId, $amount)
    {
        $fund = $this->walletService->checkBalance($userId, $amount, true);
        return $this->walletService->lock($fund, $amount);
    }

    public function unlock($userId, $amount)
    {
        $fund = $this->walletService->checkLocked($userId, $amount, true);
        return $this->walletService->unlock($fund, $amount);
    }

    public function earn($userId, $amount, $commission, $remarks)
    {
        return $this->walletService->earn($userId, $amount, $commission, $remarks);
    }

    public function pay($userId, $amount, $remarks)
    {
        return $this->walletService->pay($userId, $amount, $remarks);
    }


    public function getRepository()
    {
        return [$this->accountService->getRepository(), $this->walletService->getRepository()];
    }
}