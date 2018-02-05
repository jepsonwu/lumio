<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/9/28
 * Time: 14:04
 */

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Account\Constants\AccountBusinessConstant;
use Modules\Account\Constants\AccountErrorConstant;
use Modules\Account\Constants\AccountResponseCodeConstant;
use Modules\Account\Services\AccountRequestService;
use Auth;

class AuthController extends AuthBaseController
{
    /**
     * @var AccountRequestService
     */
    public $accountService;

    public function __construct(AccountRequestService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function index(Request $request)
    {
        return $this->result(true, [['dt' => 'index'],['at' => 'index']]);

        $this->validate(
            $request,
            [
                'user_id' => 'required|email'
            ]
        );
        $this->accountService->registerUser($this->requestParams->getRegularParams());
        return $this->result(true, ['dt' => 'index']);
    }

    public function getSmsCaptcha(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => 'required', //因为安卓客户端本地没有做mobile格式校验，如果手机号格式有误，需要返回特点的错误信息才能触发toast；因此这里不做系统层面的校验
                'business_type' => 'string'
            ]
        );
        $this->requestParams->setRegularParam('captcha_type', 'sms');
        if ($this->accountService->sendAccountCaptcha($this->requestParams->getRegularParams())) {
            return $this->result(true, ['voice_code_text' => '你将会收到来自in（125909888389）含有语音验证码的电话']);
        }
        return $this->error(AccountErrorConstant::ERR_ACCOUNT_SMS_CODE_SEND_FAILED);
    }

    public function getVoiceCaptcha(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => 'required|mobile',
                'business_type' => 'string'
            ]
        );
        $this->requestParams->setRegularParam('captcha_type', 'voice');
        if ($this->accountService->sendAccountCaptcha($this->requestParams->getRegularParams())) {
            return $this->result(true);
        }
        return $this->error(AccountErrorConstant::ERR_ACCOUNT_VOICE_CODE_SEND_FAILED);
    }

    public function getActiveSmsCaptcha(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => ['bail', 'required', 'regex://']
            ]
        );
    }

    public function checkActiveSmsCaptcha(Request $request)
    {
    }

    public function registerUser(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => ['bail', 'required', 'mobile'],
                'code' => 'bail|required|integer',
                'password' => ['bail', 'required', 'string'],
                'force' => 'sometimes|required|integer',
            ]
        );
        list($responseData, $codeTpl) = $this->accountService->registerUser($this->requestParams->getRegularParams());
        $this->saveLoginInfo($responseData);
        return $this->result(true, $responseData, $codeTpl);
    }

    public function loginCommonAccount(Request $request)
    {
        $this->validate(
            $request,
            [
                'username' => 'required|string',
                'password' => 'required|string'
            ]
        );
        list($loginUser, $responseCodeTpl) = $this->accountService->loginCommonAccount($this->requestParams->getRegularParams());
        if (!$responseCodeTpl) {
            $this->saveLoginInfo($loginUser);
            return $this->success($loginUser);
        }
        return $this->result(false, $loginUser, $responseCodeTpl);
    }

    public function bindWeixin(Request $request)
    {
        $request->offsetSet('type', AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_WEIXIN);
        return $this->bindThirdParty($request);
    }

    public function bindWeibo(Request $request)
    {
        $request->offsetSet('type', AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_WEIBO);
        return $this->bindThirdParty($request);
    }

    public function bindQq(Request $request)
    {
        $request->offsetSet('type', AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_QQ);
        return $this->bindThirdParty($request);
    }

    public function bindThirdParty(Request $request)
    {
        $this->validate(
            $request,
            [
                'access_token' => 'required|string',
                'expires_in' => 'required|integer',
                'open_id' => 'required|string',
                'type' => 'required|integer'
            ]
        );
        $this->requestParams->setRegularParam('currentUser', Auth::user());
        list($registerUserInfo, $responseTpl, $responseStatus) = $this->accountService->bindThirdParty($this->requestParams->getRegularParams());
        return $this->result($responseStatus, $registerUserInfo, $responseTpl);
    }

    public function unbindWeixin(Request $request)
    {
        $request->offsetSet('type', AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_WEIXIN);
        return $this->unbindThirdParty($request);
    }

    public function unbindWeibo(Request $request)
    {
        $request->offsetSet('type', AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_WEIBO);
        return $this->unbindThirdParty($request);
    }

    public function unbindQq(Request $request)
    {
        $request->offsetSet('type', AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_QQ);
        return $this->unbindThirdParty($request);
    }

    public function unbindThirdParty(Request $request)
    {
        $this->validate(
            $request,
            [
                'source_id' => 'required|string',
                'forceUnbind' => 'sometimes|required|integer',
                'type' => 'required|integer'
            ]
        );
        $thirdPartyTypeId = $request->offsetGet('type');
        $currentVersion = $this->requestParams->version;
        $currentUser = Auth::user();
        $this->requestParams->setRegularParam('currentUser', $currentUser);
        $this->requestParams->setRegularParam('thirdPartyTypeId', $thirdPartyTypeId);
        $this->requestParams->setRegularParam('thirdPartyUserId', $request->offsetGet('source_id'));
        $specialVersion = false;
        if (($thirdPartyTypeId == AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_WEIXIN) && version_compare($currentVersion, '2.9.0')) {
            $specialVersion = true;
        }
        $this->requestParams->setRegularParam('specialVersion', $specialVersion);
        $accountInfo = $this->accountService->unbindThirdParty($this->requestParams->getRegularParams());
        return $this->result(true, $accountInfo, AccountResponseCodeConstant::COMMON_ACCOUNT_THIRD_PARTY_UNBIND_SUCCESS);
    }

    public function getAccountSafetyCondition(Request $request)
    {
        $this->validate($request, []);
        $this->requestParams->setRegularParam('currentUser', Auth::user());
        $conditionDetails = $this->accountService->getAccountSafetyCondition($this->requestParams->getRegularParams());
        return $this->success($conditionDetails);
    }

    public function setPassword(Request $request)
    {
        $this->validate($request, ['password' => 'required|string']);
        $this->requestParams->setRegularParam('currentUser', Auth::user());
        $this->accountService->setAccountPassword($this->requestParams->getRegularParams());
        return $this->result(true, [], AccountResponseCodeConstant::COMMON_ACCOUNT_PASSWORD_SET_SUCCESS);
    }

    public function changePassword(Request $request)
    {
        $this->validate(
            $request,
            [
                'password' => 'required|string',
                'new_password' => 'required|string',
            ]
        );
        $this->requestParams->setRegularParam('currentUser', Auth::user());
        $accountChangeInfo = $this->accountService->changeAccountPassword($this->requestParams->getRegularParams());
        $this->saveLoginInfo($accountChangeInfo);
        return $this->result(true, $accountChangeInfo, AccountResponseCodeConstant::COMMON_ACCOUNT_PASSWORD_CHANGE_SUCCESS);
    }

    public function resetPassword(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => 'required|mobile',
                'code' => 'required|integer',
                'password' => 'required|string'
            ]
        );
        if ($this->accountService->resetAccountPassword($this->requestParams->getRegularParams())) {
            return $this->result(true, [], AccountResponseCodeConstant::COMMON_ACCOUNT_PASSWORD_RESET_SUCCESS);
        }
        return $this->error(AccountErrorConstant::ERR_ACCOUNT_PASSWORD_RESET_FAILED);
    }

    public function changeMobile(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => 'required|mobile',
                'code' => 'sometimes|required|integer',
                'needCode' => 'sometimes|required|integer',
                'force' => 'sometimes|required|integer',
                'show_mobile' => 'sometimes|required|integer'
            ]
        );
        $this->requestParams->setRegularParam('currentUser', Auth::user());
        list($changeResult, $statusTpl) = $this->accountService->changeMobile($this->requestParams->getRegularParams());
        return $this->result(true, $changeResult, $statusTpl);
    }

    public function bindMobile(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => 'required|mobile',
                'code' => 'required|integer',
                'password' => 'sometimes|required|string',
                'force' => 'sometimes|required|integer',
                'show_mobile' => 'sometimes|required|integer'
            ]
        );
        $this->requestParams->setRegularParam('currentUser', Auth::user());
        list($bindResult, $statusTpl) = $this->accountService->bindMobile($this->requestParams->getRegularParams());
        return $this->result(true, $bindResult, $statusTpl);
    }

    public function authWeibo(Request $request)
    {
        $this->validate(
            $request,
            [
                'access_token' => 'required|string',
                'expires_in' => 'required|integer',
                'weibo_uid' => 'sometimes|required|string',
                'uid' => 'sometimes|required|string',
                'remind_in' => 'integer'
            ]
        );
        /**
         * IOS Android 对于微博的uid传递方式不一样，这里要做下兼容
         */
        if ('ios' == $this->requestParams->source()) {
            $this->requestParams->setRegularParam('weibo_uid', $this->requestParams->uid);
        }
        $this->requestParams->setRegularParam('thirdPartyFlag', AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_WEIBO);
        $registerUserInfo = $this->accountService->authThirdParty($this->requestParams->getRegularParams());
        return $this->success($registerUserInfo);
    }

    public function authQq(Request $request)
    {
        $this->validate(
            $request,
            [
                'access_token' => 'required|string',
                'expires_in' => 'required|integer',
                'open_id' => 'required|string',
                'user_info' => 'required|json'
            ]
        );
        $this->requestParams->setRegularParam('thirdPartyFlag', AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_QQ);
        $registerUserInfo = $this->accountService->authThirdParty($this->requestParams->getRegularParams());
        return $this->success($registerUserInfo);
    }

    public function authWeixin(Request $request)
    {
        $this->validate(
            $request,
            [
                'access_token' => 'required|string',
                'expires_in' => 'required|integer',
                'open_id' => 'required|string'
            ]
        );
        $this->requestParams->setRegularParam('thirdPartyFlag', AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_WEIXIN);
        $registerUserInfo = $this->accountService->authThirdParty($this->requestParams->getRegularParams());
        return $this->success($registerUserInfo);
    }
}
