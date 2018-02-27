<?php

namespace App\Components\Factories;

use Modules\Account\Services\UserInternalService;
use Modules\Seller\Services\SellerInternalService;
use Modules\Task\Services\TaskInternalService;
use Modules\UserFund\Services\UserFundInternalService;

class InternalServiceFactory
{
    /**
     * @return TaskInternalService
     */
    public static function getTaskInternalService()
    {
        return app(TaskInternalService::class);
    }

    /**
     * @return UserFundInternalService
     */
    public static function getUserFundInternalService()
    {
        return app(UserFundInternalService::class);
    }

    /**
     * @return SellerInternalService
     */
    public static function getSellerInternalService()
    {
        return app(SellerInternalService::class);
    }

    /**
     * @return UserInternalService
     */
    public static function getUserInternalService()
    {
        return app(UserInternalService::class);
    }
}