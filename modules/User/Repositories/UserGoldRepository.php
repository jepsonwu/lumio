<?php

namespace Modules\User\Repositories;

use Modules\User\Constants\UserBanyanDBConstant;
use Modules\User\Models\UserGoldLog;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/6
 * Time: 上午11:05
 */
class UserGoldRepository extends BaseRepository
{
    use CacheableRepository;

    public function model()
    {
        return UserGoldLog::class;
    }

    public function addPayLog($userId, $operation, $gold, $remark)
    {
        return $this->create([
            'user_id' => $userId,
            'operation' => $operation,
            'out_gold' => $gold,
            'extra' => $remark
        ]);
    }

    public function addEarnLog($userId, $operation, $gold, $remark)
    {
        return $this->create([
            'user_id' => $userId,
            'operation' => $operation,
            'in_gold' => $gold,
            'extra' => $remark
        ]);
    }

    public function getGold($userId)
    {
        return (int)$this->getBanyanStorage($userId)->get('gold');
    }

    public function getTodayGold($userId)
    {
        return (int)$this->getBanyanStorage($userId)->get('today_gold');
    }

    public function getLastDayOfEarnGold($userId)
    {
        return (int)$this->getBanyanStorage($userId)->get('gold_inc_date');
    }

    public function incGold($userId, $number)
    {
        return $this->getBanyanStorage($userId)->inc('gold', $number);
    }

    public function resetTodayGold($userId, $number)
    {
        return $this->getBanyanStorage($userId)->set('today_gold', $number);
    }

    public function incTodayGold($userId, $number)
    {
        return $this->getBanyanStorage($userId)->inc('today_gold', $number);
    }

    public function setLastDayOfEarnGold($userId)
    {
        return $this->getBanyanStorage($userId)->set('gold_inc_date', strtotime(date('Y-m-d')));
    }

    protected function getBanyanStorage($userId)
    {
        return UserBanyanDBConstant::userCounterGoldInfo($userId);
    }
}