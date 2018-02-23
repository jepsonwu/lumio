<?php

namespace Modules\UserFund\Services;

use App\Constants\GlobalDBConstant;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\UserFund\Constants\UserFundBanyanDBConstant;
use Modules\UserFund\Constants\UserFundErrorConstant;
use Modules\UserFund\Models\Account;
use Modules\UserFund\Repositories\AccountRepositoryEloquent;

class AccountService extends BaseService
{
    const BANYAN_USER_FUND_STAT_ACCOUNT_NUMBER_KEY = "account_number";

    public function __construct(AccountRepositoryEloquent $accountRepositoryEloquent)
    {
        $this->setRepository($accountRepositoryEloquent);
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

        $account = $this->getRepository()->create($attributes);
        $account || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_CREATE_FAILED);

        $this->incUserAccountNumber($userId);
        //todo 权限判断

        return $account;
    }

    protected function getUserAccountNumber($userId)
    {
        return (int)UserFundBanyanDBConstant::commonUserFundStat($userId)->get(self::BANYAN_USER_FUND_STAT_ACCOUNT_NUMBER_KEY);
    }

    protected function incUserAccountNumber($userId)
    {
        UserFundBanyanDBConstant::commonUserFundStat($userId)->inc(self::BANYAN_USER_FUND_STAT_ACCOUNT_NUMBER_KEY);
    }

    protected function decUserAccountNumber($userId)
    {
        UserFundBanyanDBConstant::commonUserFundStat($userId)->inc(self::BANYAN_USER_FUND_STAT_ACCOUNT_NUMBER_KEY, -1);
    }

    public function checkDeployAccount($userId)
    {
        $this->getUserAccountNumber($userId) < 1
        && ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_NO_DEPLOY);
    }

    protected function isAllowCreateBankCard($userId)
    {
        $this->getRepository()->getByUserId($userId)->count()
        && ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_IS_NOT_ALLOW_CREATE);
    }

    protected function isExistsBankCard($bankCard)
    {
        $this->getRepository()->getByBankCard($bankCard)
        && ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_HAS_BEEN_EXISTS);
    }

    public function list($userId)
    {
        return $this->getRepository()->getByUserId($userId);
    }

    public function update($userId, $accountId, $attributes)
    {
        $account = $this->isValidAccount($accountId);
        $this->isAllowUpdate($userId, $account);
        $account->bank_card == $attributes['bank_card']
        || $this->isExistsBankCard($attributes['bank_card']);

        $result = $this->getRepository()->updateAccount($account, $attributes);
        $result || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_UPDATE_FAILED);

        return $account;
    }

    protected function isAllowUpdate($userId, Account $account)
    {
        $this->isAllowOperate($userId, $account);
    }

    //todo package
    protected function isAllowOperate($userId, Account $account)
    {
        $userId == $account->user_id
        || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_OPERATE_ILLEGAL);
    }

    public function delete($userId, $accountId)
    {
        $account = $this->isValidAccount($accountId);
        $this->isAllowDelete($userId, $account);

        $result = $this->getRepository()->deleteAccount($account);
        $result || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_DELETE_FAILED);

        $this->decUserAccountNumber($userId);
        //todo 权限

        return true;
    }

    protected function isAllowDelete($userId, Account $account)
    {
        $this->isAllowOperate($userId, $account);
    }

    /**
     * @param $id
     * @return \Illuminate\Support\Collection|mixed|\Prettus\Repository\Database\Eloquent\Model|Account
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function isValidAccount($id)
    {
        $account = $this->getRepository()->find($id);
        $account || ExceptionResponseComponent::business(UserFundErrorConstant::ERR_ACCOUNT_INVALID);

        return $account;
    }

    /**
     * @return mixed|AccountRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }
}