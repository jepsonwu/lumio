<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/29
 * Time: 20:50
 */

namespace Modules\Account\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Account\Repositories\AccountRepository;

class AccountService extends BaseService
{
    /**
     * @var AccountRepository
     */
    protected $_repository;

    /**
     * @var UserService
     */
    protected $_userService;

    public function __construct(AccountRepository $repository, UserService $userService)
    {
        $this->_repository = $repository;
        $this->_userService = $userService;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }
}