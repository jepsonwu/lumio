<?php

namespace Modules\UserFund\Repositories;

use App\Validators\GlobalValidator;
use Modules\UserFund\Models\FundRecord;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class FundRecordRepositoryEloquent
 * @package namespace Modules\Account\Repositories;
 */
class FundRecordRepositoryEloquent extends BaseRepository
{
    /**
     * @var FundRecord
     */
    protected $model;

    public function model()
    {
        return FundRecord::class;
    }

    public function validator()
    {
        return GlobalValidator::class;
    }

    public function prepareRecharge($userId, $amount, $remarks)
    {
        return $this->create([
            "user_id" => $userId,
            "amount" => $amount,
            "actual_amount" => $amount,
            "commission" => 0,
            "record_type" => FundRecord::RECORD_TYPE_RECHARGE,
            "record_status" => FundRecord::RECORD_STATUS_VERIFYING,
            "remarks" => $remarks,
            "created_at" => time()
        ]);
    }

    public function prepareWithdraw($userId, $amount, $commission, $remarks)
    {
        return $this->create([
            "user_id" => $userId,
            "amount" => $amount,
            "actual_amount" => $amount - $commission,
            "commission" => $commission,
            "record_type" => FundRecord::RECORD_TYPE_WITHDRAW,
            "record_status" => FundRecord::RECORD_STATUS_VERIFYING,
            "remarks" => $remarks,
            "created_at" => time()
        ]);
    }


    public function pay($userId, $amount, $remarks)
    {
        return $this->create([
            "user_id" => $userId,
            "amount" => $amount,
            "actual_amount" => $amount,
            "commission" => 0,
            "record_type" => FundRecord::RECORD_TYPE_PAY,
            "record_status" => FundRecord::RECORD_STATUS_DONE,
            "remarks" => $remarks,
            "created_at" => time()
        ]);
    }

    public function earn($userId, $amount, $commission, $remarks)
    {
        return $this->create([
            "user_id" => $userId,
            "amount" => $amount,
            "actual_amount" => $amount - $commission,
            "commission" => $commission,
            "record_type" => FundRecord::RECORD_TYPE_EARN,
            "record_status" => FundRecord::RECORD_STATUS_DONE,
            "remarks" => $remarks,
            "created_at" => time()
        ]);
    }

    public function pass(FundRecord $record)
    {
        return $record->pass();
    }

    public function fail(FundRecord $record)
    {
        return $record->fail();
    }

    public function close(FundRecord $record)
    {
        return $record->close();
    }
}
