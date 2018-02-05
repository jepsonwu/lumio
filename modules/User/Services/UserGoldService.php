<?php

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/6
 * Time: 上午10:53
 */

namespace Modules\User\Services;

use Modules\User\Repositories\UserGoldRepository;

class UserGoldService
{
    protected $userGoldRepository;

    public function __construct(UserGoldRepository $userGoldRepository)
    {
        $this->userGoldRepository = $userGoldRepository;
    }

    public function pay($userId, $gold, $operation, $remark = '')
    {
        $gold = (int)$gold;

        if ($this->userGoldRepository->getGold($userId) < $gold) {
            return false;
        }

        $this->userGoldRepository->incGold($userId, -$gold);

        return $this->userGoldRepository->addPayLog($userId, $operation, $gold, $remark);
    }

    public function earn($userId, $gold, $operation, $remark = '')
    {
        $gold = (int)$gold;

        if ($this->userGoldRepository->getLastDayOfEarnGold($userId) != strtotime(date('Y-m-d'))) {
            $this->userGoldRepository->setLastDayOfEarnGold($userId);
            $this->userGoldRepository->resetTodayGold($userId, $gold);
        } else {
            $this->userGoldRepository->incTodayGold($userId, $gold);
        }

        $this->userGoldRepository->incGold($userId, $gold);

        return $this->userGoldRepository->addEarnLog($userId, $operation, $gold, $remark);
    }

    public function getGoldInfo($userId)
    {
        return [
            'gold' => $this->userGoldRepository->getGold($userId),
            'today_gold' => $this->userGoldRepository->getTodayGold($userId),
            'gold_inc_date' => $this->userGoldRepository->getLastDayOfEarnGold($userId)
        ];
    }
}