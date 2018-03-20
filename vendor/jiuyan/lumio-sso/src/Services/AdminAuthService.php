<?php
/**
 * Created by IntelliJ IDEA.
 * User: topone4tvs
 * Date: 2017/3/16
 * Time: 12:24
 */

namespace Jiuyan\LumioSSO\Services;

use GuzzleHttp\Client;
use Jiuyan\LumioSSO\Contracts\AuthenticateAdminContract;

class AdminAuthService implements AuthenticateAdminContract
{
    const COOKIE_SSO_TICKET = 'ticket';
    const COOKIE_SSO_SESSION_KEY = 'sessionKey';
    const SSO_SERVER_DOMAIN = 'http://ssoadmin.itugo.com';

    public function getLoginUser($authKey = '')
    {
        $authKey = $this->_getAuthKey($authKey);
        $user = $this->_getUser($authKey);
        $this->_saveAuth($authKey, $user);
        return $user;
    }

    public function getLoginUrl($uri)
    {
        return self::SSO_SERVER_DOMAIN . '/user/login?callback=' . urlencode($uri);
    }

    public function setMock($mock)
    {
        // TODO: Implement setMock() method.
    }

    private function _getAuthKey($authKey = '')
    {
        if (!$authKey) {
            $authKey = $_COOKIE[self::COOKIE_SSO_TICKET] ?? false;
        }
        return $authKey;
    }

    private function _getUser($authKey)
    {
        $cookieStr = "";
        foreach ($_COOKIE as $k => $v) {
            $cookieStr .= "$k=" . $v . ';';
        }
        $httpClient = new Client();
        $requestUrl = self::SSO_SERVER_DOMAIN . '/user/valid?ticket=' . $authKey;
        $response = $httpClient->post(
            $requestUrl,
            [
                'headers' => [
                    'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                    'cookies' => $cookieStr
                ]
            ]
        );
        if ($response->getStatusCode() == '200') {
            $responseStr = $response->getBody()->getContents();
            if (($user = json_decode($responseStr, true)) && array_get($user, 'data', [])) {
                return array_get($user, 'data', []);
            }
        }
        return false;
    }

    /**
     * 将一些信息存到cookie中，供下次请求sso服务器时提供过去
     * @param $authKey
     * @param $user
     */
    private function _saveAuth($authKey, $user)
    {
        $ssoBaseDomain = 'itugo.com';
        setcookie(self::COOKIE_SSO_TICKET, $authKey, null, '/', $ssoBaseDomain);
        if (isset($user['session_key'])) {
            setcookie(self::COOKIE_SSO_SESSION_KEY, $user['session_key'], null, '/', $ssoBaseDomain);
        }
    }
}
