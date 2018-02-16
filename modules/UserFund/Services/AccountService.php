<?php

namespace Modules\UserFund\Services;

use App\Constants\GlobalDBConstant;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\UserFund\Constants\UserFundErrorConstant;
use Modules\UserFund\Models\Account;
use Modules\UserFund\Repositories\AccountRepositoryEloquent;

class AccountService extends BaseService
{
    protected $_accountRepository;

    public function __construct(AccountRepositoryEloquent $accountRepositoryEloquent)
    {
        $this->_accountRepository = $accountRepositoryEloquent;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    /**
     * @param $userId
     * @param $attributes
     * @return mixed|Account
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create($userId, $attributes)
    {
        $this->isAllowCreateBankCard($userId);
        $this->isExistsBankCard($attributes['bank_card']);

        $attributes['user_id'] = $userId;//todo optimize 放在哪里合适
        $attributes['created_at'] = time();
        $attributes['account_status'] = GlobalDBConstant::DB_TRUE;

        $account = $this->_accountRepository->create($attributes);
        $account || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_CREATE_FAILED);

        //todo 权限判断

        return $account;
    }

    protected function isAllowCreateBankCard($userId)
    {
        $this->_accountRepository->getByUserId($userId)
        && ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_IS_NOT_ALLOW_CREATE);
    }

    protected function isExistsBankCard($bankCard)
    {
        $this->_accountRepository->getByBankCard($bankCard)
        && ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_HAS_BEEN_EXISTS);
    }

    public function list($userId)
    {
        return $this->_accountRepository->getByUserId($userId);
    }
}