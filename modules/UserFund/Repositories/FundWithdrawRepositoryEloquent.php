<?php

namespace Modules\UserFund\Repositories;

use App\Validators\GlobalValidator;
use Modules\UserFund\Models\FundWithdraw;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class FundWithdrawRepositoryEloquent
 * @package namespace Modules\Account\Repositories;
 */
class FundWithdrawRepositoryEloquent extends BaseRepository
{
    /**
     * @var FundWithdraw
     */
    protected $model;

    public function model()
    {
        return FundWithdraw::class;
    }

    public function validator()
    {
        return GlobalValidator::class;
    }

    public function pass(FundWithdraw $fundWithdraw, $verifyUserId)
    {
        return $fundWithdraw->pass($verifyUserId);
    }

    public function fail(FundWithdraw $fundWithdraw, $verifyUserId, $reason)
    {
        return $fundWithdraw->fail($verifyUserId, $reason);
    }

    public function close(FundWithdraw $fundWithdraw)
    {
        return $fundWithdraw->close();
    }
}
