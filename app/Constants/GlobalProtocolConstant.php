<?php

namespace App\Constants;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/22
 * Time: 上午10:38
 */
class GlobalProtocolConstant
{
    /************************user center***************************/
    const USER_CENTER_EDIT_PROFILE = "usercenter/editprofile";

    //todo optimize
    const USER_CENTER_ACCOUNT_SECURE_BIND_WEIBO = "usercenter/accountsecure?action=bind&type=weibo&refresh=1";
    const USER_CENTER_ACCOUNT_SECURE_BIND_WEIXIN = "usercenter/accountsecure?action=bind&type=weixin&refresh=1";
    const USER_CENTER_ACCOUNT_SECURE_BIND_QQ = "usercenter/accountsecure?action=bind&type=qq&refresh=1";
    const USER_CENTER_CONTACT_FRIEND = 'usercenter/contactfriend';
    const USER_CENTER_WEIBO_FRIEND = 'usercenter/weibofriend';
    const USER_CENTER_ADD_FRIEND = "usercenter/addfriend";

    /**************************in********************************/
    const IN_CENTER = "in";
    const IN_CENTER_AUTH = "in?tovc=101&h5=1&refresh=1";//todo optimize
    const IN_CENTER_UPLOAD_CONTRACT = "in?tovc=100&h5=1";

    /**************************camera********************************/
    const CAMERA_CENTER = 'camera';
    const CAMERA_ALBUM = "camera/album";


    const ONE_KEY_USE = "pastermall/pasterclonelist";


    const TAG_CENTER = "myTag";

    const MAIN_CENTER_FRIEND = "main/friend";
    const MAIN_DISCOVER_WORLD = "main/discover/world";

    const DIARY_OTHER = "diary/other";
}