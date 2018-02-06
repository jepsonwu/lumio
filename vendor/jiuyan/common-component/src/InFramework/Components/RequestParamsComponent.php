<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/8/2
 * Time: 19:54
 */

namespace Jiuyan\Common\Component\InFramework\Components;

/**
 * Class RequestParamsComponent
 *
 * @property string page
 * @property string cursor
 * @property string version
 * @property string source
 * @property string platform
 * @property string net
 * @property string currentType
 * @property string gps
 * @property string uuid
 * @property string udid
 * @property string wifiMac
 * @property string wifiName
 * @property string resolution
 * @property string token
 * @property string auth
 * @property string genger
 * @property string language
 * @property string photoId
 * @property string childId
 * @property string userId
 * @property string tagId
 * @property string pasterId
 * @property string topPhotoId
 * @property string emojiCommentContent
 * @property string skyPostId
 * @property string brandId
 * @property string authCookieName
 * @property string channel
 * @property string liveId
 * @property string storyId
 * @property string tdId
 * @property string idfa
 * @property string imei
 */
class RequestParamsComponent
{
    public static $commonParamsRules = [
        'page' => 'page',//分页页码
        'cursor' => 'cursor',//分页页码
        'version' => '_v',//客户端版本号
        'source' => '_s',//客户端设备类型：ios, android
        'platform' => '_pf',//客户端设备类型：ios 为: iphone5,2
        'net' => '_n',//客户端网络类型：wifi or others
        'currentType' => '_ct',//用户登录方式：1->weibo,2->qq,3->weixin,4->facebook,8->手机号,9->in号
        'gps' => '_gps', //gps
        'uuid' => '_uuid',  //手机设备号
        'udid' => '_udid', //v2.0+设备号
        'wifiMac' => '_wm',  //wifi mac地址
        'wifiName' => '_wn',  //wifi 名字
        'resolution' => '_res',  //手机屏幕分辨率
        'token' => '_token',  // 用户唯一标识 token
        'auth' => '_at',  // 用户登录权限 auth
        'genger' => '_g',//用户性别：m（男） f（女） n（未知）
        'language' => '_l',//语言
        'photoId' => 'pid',  //照片id
        'childId' => 'pcid',  //照片id
        'userId' => 'uid',  //用户id
        'tagId' => 'tgid',  //标签id
        'pasterId' => 'ptid',  //贴纸id
        'topPhotoId' => 'top_pid',  //置顶图片
        'emojiCommentContent' => 'content',  //含有emoji表情的内容
        'skyPostId' => 'poid',  //上空帖子id
        'brandId' => 'brand_id', //品牌站id
        'authCookieName' => '_aries',
        'channel' => '_ch', // 应用推广来源
        'liveId' => 'lid',  //直播id
        'storyId' => 'sid', //故事集id
        'tdId' => 'tdid', //talking data id
        'idfa' => '_idfa',  //idfa
        'imei' => '_imei',  //imei
    ];

    private static $_requestParams = [];
    private static $_allRequestParams = [];
    private static $_commonParams = [];

    private $_regularParams = [];

    public function __construct($regularParams = [])
    {
        $this->_regularParams = $regularParams;
    }

    /**
     * @param $propName
     * @return bool|string
     */
    public function __get($propName)
    {
        $paramsName = isset(self::$commonParamsRules[$propName]) ? self::$commonParamsRules[$propName] : $propName;
        $paramVal = self::getParam($paramsName);
        return $paramVal ?: false;
    }

    protected static function _init()
    {
        if (!self::$_allRequestParams) {
            self::$_allRequestParams = app('request')->all();
        }
    }

    public static function getAllCommonParams()
    {
        if (!self::$_commonParams) {
            foreach (self::$commonParamsRules as $rule => $key) {
                if ($val = self::getParam($key)) {
                    self::$_commonParams[$rule] = $val;
                }
            }
        }
        return self::$_commonParams;
    }

    public static function getParam($paramName, $defaultVal = '')
    {
        self::_init();
        if (isset(self::$_allRequestParams[$paramName])) {
            if (!isset(self::$_requestParams[$paramName])) {
                self::$_requestParams[$paramName] = self::_formatRequestParams(self::$_allRequestParams[$paramName]);
            }
            return self::$_requestParams[$paramName];
        }
        return $defaultVal;
    }

    public static function __callStatic($funcName, $params)
    {
        return self::getParam($funcName);
    }

    public static function isSecure()
    {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
        || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
    }

    private static function _formatRequestParams($val)
    {
        return $val ? htmlspecialchars_decode(strip_tags(trim($val))) : '';
    }
    public function setRegularParam($paramKey, $paramVal)
    {
        $this->_regularParams[$paramKey] = $paramVal;
    }

    public function getRegularParams()
    {
        return $this->_regularParams;
    }

    public function all()
    {
        return self::$_allRequestParams;
    }

    public static function fullDomain()
    {
        return (self::isSecure() ? 'https://' : 'http://')  . app('request')->getHttpHost();
    }

    public static function origin()
    {
        return isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    }

    public static function domain()
    {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    }

    public static function port()
    {
        return $_SERVER['HTTP_SPORT'] ?? '';
    }

    public static function ip()
    {
        $sRealIp = '';
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $aIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($aIps as $sIp) {
                    $sIp = trim($sIp);
                    if ($sIp != 'unknown') {
                        $sRealIp = $sIp;
                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $sRealIp = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $sRealIp = $_SERVER['REMOTE_ADDR'];
                } else {
                    $sRealIp = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $sRealIp = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $sRealIp = getenv('HTTP_CLIENT_IP');
            } else {
                $sRealIp = getenv('REMOTE_ADDR');
            }
        }

        return $sRealIp;
    }

    public function source()
    {
        $paramsName = isset(self::$commonParamsRules['source']) ? self::$commonParamsRules['source'] : 'source';
        $source = $this->getParam($paramsName);
        if (!$source) {
            $source = app('request')->cookie($paramsName);
            if ($source) {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                if ($userAgent) {
                    if (strpos($userAgent, 'Android') !== false) {
                        $source = 'android';
                    } elseif (strpos($userAgent, 'iPhone') !== false) {
                        $source = 'ios';
                    }
                }
            }
        }
        return $source ?: false;
    }
}