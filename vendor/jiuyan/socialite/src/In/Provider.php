<?php
/**
 * Created by IntelliJ IDEA.
 * User: topone4tvs
 * Date: 2017/4/6
 * Time: 16:52
 */

namespace Jiuyan\Socialite\In;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'IN';

    const AUTH_DOMAIN = 'http://open.in66.com';

    /**
     * {@inheritdoc}.
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->redirectUrl . '/oauth2/authorize', $state);
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenUrl()
    {
        return $this->redirectUrl . '/oauth2/access_token';
    }

    /**
     * {@inheritdoc}.
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->redirectUrl, [
        'query' => [
        'access_token' => $token
        ],
        ]);

        return json_decode($this->removeCallback($response->getBody()->getContents()), true);
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenFields($code)
    {
        $tokenFields = parent::getTokenFields($code);
        unset($tokenFields['redirect_uri']);
        return $tokenFields;
    }

    /**
     * {@inheritdoc}.
     */
    public function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
        'query' => $this->getTokenFields($code),
        ]);

        $this->credentialsResponseBody = json_decode($response->getBody(), true);

        return $this->parseAccessToken($response->getBody());
    }

    /**
     * @param mixed $response
     *
     * @return string
     */
    protected function removeCallback($response)
    {
        if (strpos($response, 'callback') !== false) {
            $lpos = strpos($response, '(');
            $rpos = strrpos($response, ')');
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }

        return $response;
    }

    /**
     * Get the access token response for the given code.
     *
     * @param  string  $code
     * @return array
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            'query' => $this->getTokenFields($code)
        ]);
        $responseData = json_decode($response->getBody(), true);
        if ($responseData && $responseData['succ'] && isset($responseData['data'])) {
            return $responseData['data'];
        }
        return false;
    }

    /**
     * {@inheritdoc}.
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['idstr'],
            'nickname' => $user['name'],
            'avatar' => $user['profile_image_url'],
            'name' => $user['screen_name'],
            'email' => null,
            'real_name' => $user['name'],
            'sina_email' => '',
            'gender' => $user['gender'],
            'followers_count' => $user['followers_count'],
            'friends_count' => $user['friends_count'],
            'statuses_count' => $user['statuses_count'],
            'favourites_count' => $user['favourites_count'],
            'verified' => intval($user['verified']),
            'verified_type' => $user['verified_type'],
            'verified_reason' => $user['verified_reason'],
            'province' => $user['province'],
            'city' => $user['city'],
            'address' => $user['location'],
            'desc' => $user['description']
        ]);
    }

    /**
     * Get a Social User instance from a known access token.
     *
     * TODO:: 由于目前没有提供完整的一套oauth流程，所以，获取用户信息的流程暂时不做处理
     * @param  string  $token
     * @return \Laravel\Socialite\Two\User
     */
    public function userFromToken($token)
    {
        return []; 
        $user = $this->mapUserToObject($this->getUserByToken($token));
        $user->setToken($token);
        return [
            'name' => $user->nickname,
            'real_name' => $user->nickname,
            'gender' => $user->gender,
            'avatar' => $user->avatar,
            'province' => 0,
            'city' => 0,
            'address' => $user->address,
            'desc' => '',
            //'source' => BusinessConstant::ACCOUNT_SOCIALITE_SOURCE_IN,
            //'source_id' => $this->openId,
            'email' => '',
            'publish_status' => 'enable',
            'comment_status' => 'enable',
            'authed' => 0,
            'mobile' => 0,
            'task_status' => 0,
        ];
    }
}
