<?php

namespace Modules\Admin\Http\Controllers\Auth;


use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Modules\Admin\Constants\AccountBusinessConstant;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Admin\Services\AccountService;
use Modules\Admin\Services\UserService;

class AccountController extends AdminController
{

    protected $_accountService;

    public function __construct(AccountService $accountService)
    {
        $this->_accountService = $accountService;
        parent::__construct();
    }

    public function index()
    {
        return $this->render('admin/auth/index', [
            'list' => [],
            'params' => []
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            "user_name" => "required|string",
            "password" => "required|string",//redirect_url
        ]);

        $params = $this->requestParams->getRegularParams();
        $user = $this->_accountService->login($params['user_name'], $params['password']);

        $this->saveLoginInfo($user);
        return $this->success([
            "redirect_url" => "/admin"
        ]);
    }

    protected function saveLoginInfo($userInfo)
    {
        $this->addCookie(AccountBusinessConstant::ACCOUNT_AUTHORIZED_COOKIE_TOKEN, $userInfo['token'],
            UserService::TOKEN_EXPIRES, env("COOKIE_DOMAIN")
        );
        $this->addCookie(AccountBusinessConstant::ACCOUNT_AUTHORIZED_COOKIE_USER_ID, $userInfo['id'],
            UserService::TOKEN_EXPIRES, env("COOKIE_DOMAIN")
        );
    }

    public function logout()
    {
        $userInfo = AuthHelper::user();

        $this->addCookie(AccountBusinessConstant::ACCOUNT_AUTHORIZED_COOKIE_TOKEN, $userInfo['token'],
            -1, env("COOKIE_DOMAIN")
        );
        $this->addCookie(AccountBusinessConstant::ACCOUNT_AUTHORIZED_COOKIE_USER_ID, $userInfo['id'],
            -1, env("COOKIE_DOMAIN")
        );
        return $this->success([]);
    }
}