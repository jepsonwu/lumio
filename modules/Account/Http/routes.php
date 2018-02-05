<?php

app()->get('', function () {
    return 'In Lumio~';
});

Route::version('v1', ['prefix' => '/api/account', 'namespace' => 'Modules\Account\Http\Controllers', 'middleware' => 'jiuyan.api.sign'], function() {
    Route::get('index', ['as' => 'account.test.index', 'uses' => 'AuthController@index']);

    Route::post('register', ['as' => 'account.register', 'uses' => 'AuthController@registerUser']);
    Route::post('common/login', ['as' => 'common.login', 'uses' => 'AuthController@loginCommonAccount']);
    Route::post('weibo/auth', ['as' => 'common.auth.weibo', 'uses' => 'AuthController@authWeibo']);
    Route::post('qq/auth', ['as' => 'common.auth.qq', 'uses' => 'AuthController@authQq']);
    Route::post('weixin/auth', ['as' => 'common.auth.weixin', 'uses' => 'AuthController@authWeixin']);
    Route::post('password/reset', ['as' => 'reset.password', 'uses' => 'AuthController@resetPassword']);
    Route::get('sms-captcha', ['as' => 'account.sms.captcha', 'uses' => 'AuthController@getSmsCaptcha']);

    Route::group(['prefix' => 'proxy'], function () {
        Route::any('getsmscode', 'AuthController@getSmsCaptcha');
        Route::any('mobileregister', 'AuthController@registerUser');
        Route::any('login', 'AuthController@loginCommonAccount');
        Route::any('authweibo', 'AuthController@authWeibo');
        Route::any('authqq', 'AuthController@authQq');
        Route::any('authweixin', 'AuthController@authWeixin');
        Route::any('resetpassword', 'AuthController@resetPassword');
    });
});

Route::version('v1', ['prefix' => '/api/account', 'namespace' => 'Modules\Account\Http\Controllers', 'middleware' => ['jiuyan.api.auth', 'jiuyan.api.sign']], function() {

    Route::group(['prefix' => 'proxy'], function () {
        Route::any('bind', 'AuthController@bindThirdParty');
        Route::any('unbind', 'AuthController@unbindThirdParty');
        Route::any('bindinginfo', 'AuthController@getAccountSafetyCondition');

        Route::any('setpassword', 'AuthController@setPassword');
        Route::any('changepassword', 'AuthController@changePassword');

        Route::any('changemobile', 'AuthController@changeMobile');
        Route::any('bindmobile', 'AuthController@bindMobile');
    });

    Route::get('voice-captcha', ['as' => 'account.voice.captcha', 'uses' => 'AuthController@getVoiceCaptcha']);

    Route::get('safety/condition', ['as' => 'safety.condition', 'uses' => 'AuthController@getAccountSafetyCondition']);

    Route::post('password', ['as' => 'set.password', 'uses' => 'AuthController@setPassword']);
    Route::put('password', ['as' => 'change.password', 'uses' => 'AuthController@changePassword']);

    Route::post('mobile', ['as' => 'bind.mobile', 'uses' => 'AuthController@bindMobile']);
    Route::put('mobile', ['as' => 'change.mobile', 'uses' => 'AuthController@changeMobile']);

    Route::post('weibo/bind', ['as' => 'common.bind.weibo', 'uses' => 'AuthController@bindWeibo']);
    Route::post('qq/bind', ['as' => 'common.bind.qq', 'uses' => 'AuthController@bindQq']);
    Route::post('weixin/bind', ['as' => 'common.bind.weixin', 'uses' => 'AuthController@bindWeixin']);
    Route::post('third-party/bind', ['as' => 'common.bind.third-party', 'uses' => 'AuthController@bindThirdParty']);
    Route::post('weibo/unbind', ['as' => 'common.unbind.weibo', 'uses' => 'AuthController@unbindWeibo']);
    Route::post('qq/unbind', ['as' => 'common.unbind.qq', 'uses' => 'AuthController@unbindQq']);
    Route::post('weixin/unbind', ['as' => 'common.unbind.weixin', 'uses' => 'AuthController@unbindWeixin']);
    Route::post('third-party/unbind', ['as' => 'common.unbind.third-party', 'uses' => 'AuthController@unbindThirdParty']);

    Route::post('{partnerFlag}/auth/weixin', ['as' => 'partner.auth.weixin', 'uses' => 'PartnerAuthController@authWeixin']);
    Route::post('{partnerFlag}/auth/in', ['as' => 'partner.auth.in', 'uses' => 'PartnerAuthController@authIn']);
    Route::post('{partnerFlag}/common/login', ['as' => 'partner.common.login', 'uses' => 'PartnerAuthController@commonLogin']);
});
