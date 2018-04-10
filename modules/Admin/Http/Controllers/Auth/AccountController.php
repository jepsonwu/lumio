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

    public function index(Request $request)
    {
//        if (AuthHelper::user()) {
//            return $this->render('admin/index', []);
//        }

        return $this->render('admin/auth/index', [
            'list' => [],
            'params' => [
                "redirect_url" => $request->input("callback", "")
            ]
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            "user_name" => "required|string",
            "password" => "required|string",
            "redirect_url" => "string"
        ]);

        $params = $this->requestParams->getRegularParams();
        $user = $this->_accountService->login($params['user_name'], $params['password']);

        $this->saveLoginInfo($user->token, $user->id);
        $redirectUrl = array_get($params, "redirect_url", "");
        return $this->success([
            "redirect_url" => $redirectUrl ? $redirectUrl : env("APP_DOMAIN") . "/admin"
        ]);
    }

    protected function saveLoginInfo($token, $userId, $logout = false)
    {
        $this->addCookie(AccountBusinessConstant::ACCOUNT_AUTHORIZED_COOKIE_TOKEN, $token,
            $logout ? -1 : UserService::TOKEN_EXPIRES, env("COOKIE_DOMAIN")
        );
        $this->addCookie(AccountBusinessConstant::ACCOUNT_AUTHORIZED_COOKIE_USER_ID, $userId,
            $logout ? -1 : UserService::TOKEN_EXPIRES, env("COOKIE_DOMAIN")
        );
    }

    public function logout()
    {
        $user = AuthHelper::user();
        $this->saveLoginInfo($user->token, $user->id, true);
        $response = redirect(env("APP_DOMAIN") . '/admin/auth/login');
        $response = $this->withCookie($response);

        return $response;
    }
}