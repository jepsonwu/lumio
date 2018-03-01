<?php

namespace Modules\UserFund\Repositories;

use App\Validators\GlobalValidator;
use Modules\UserFund\Models\Fund;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class FundRepositoryEloquent
 * @package namespace Modules\Account\Repositories;
 */
class FundRepositoryEloquent extends BaseRepository
{
    /**
     * @var Fund
     */
    protected $model;

    public function model()
    {
        return Fund::class;
    }

    public function validator()
    {
        return GlobalValidator::class;
    }

    /**
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Model|null|static|Fund
     */
    public function getByUserId($userId)
    {
        $builder = $this->model->newQuery();
        $this->model->whereUserId($builder, $userId);
        return $builder->first();
    }

    public function createFund($userId)
    {
        return $this->create([
            "user_id" => $userId,
            "amount" => 0,
            "locked" => 0,
            "total_earn" => 0,
            "total_pay" => 0,
            "total_withdraw" => 0,
            "total_recharge" => 0,
            "created_at" => time()
        ]);
    }

    public function prepareWithdraw(Fund $fund, $amount)
    {
        return $fund->lockAmount($amount);
    }

    public function withdraw(Fund $fund, $amount)
    {
        return $fund->withdraw($amount);
    }

    public function cancelWithdraw(Fund $fund, $amount)
    {
        return $fund->unlockAmount($amount);
    }

    public function recharge(Fund $fund, $amount)
    {
        return $fund->recharge($amount);
    }

    public function earn(Fund $fund, $amount)
    {
        return $fund->earn($amount);
    }

    public function preparePay(Fund $fund, $amount)
    {
        return $fund->lockAmount($amount);
    }

    public function pay(Fund $fund, $amount)
    {
        return $fund->pay($amount);
    }

    public function cancelPay(Fund $fund, $amount)
    {
        return $fund->unlockAmount($amount);
    }
}
