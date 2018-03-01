<?php

namespace Modules\UserFund\Repositories;

use App\Validators\GlobalValidator;
use Modules\UserFund\Models\FundRecharge;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class FundRechargeRepositoryEloquent
 * @package namespace Modules\Account\Repositories;
 */
class FundRechargeRepositoryEloquent extends BaseRepository
{
    /**
     * @var FundRecharge
     */
    protected $model;

    public function model()
    {
        return FundRecharge::class;
    }

    public function validator()
    {
        return GlobalValidator::class;
    }

    public function pass(FundRecharge $fundRecharge, $verifyUserId)
    {
        return $fundRecharge->pass($verifyUserId);
    }

    public function fail(FundRecharge $fundRecharge, $verifyUserId, $reason)
    {
        return $fundRecharge->fail($verifyUserId, $reason);
    }

    public function close(FundRecharge $fundRecharge)
    {
        return $fundRecharge->close();
    }
}
