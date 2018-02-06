<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/2
 * Time: 21:06
 */

namespace Jiuyan\Tools\Business;

use Jiuyan\Common\Component\InFramework\Components\RequestParamsComponent;
use Jiuyan\Common\Component\InFramework\Services\RequestCommonParamsService;
use Jiuyan\Tools\ConfigAutoload;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Log;
use Exception;

class ImageTools
{
    /**
     * @var Auth
     */
    protected static $_authHandle;
    protected static $_config;
    private static $FORMAT_TYPE_RULES = ['default', 'origin', 'itugo', 'square', 'w640', 'w208', 'w100', 'w720', 'w480', 'w360', 'h360', 'w240', 'w180', 'w50', 'w220', 'w150', 'w130','w200','w320','ww640','w160','w290','w436','w600'];

    protected static function _init()
    {
        ConfigAutoload::register();
        if (!self::$_config) {
            self::$_config = config('tools');
        }
        if (!self::$_authHandle) {
            self::$_authHandle = new Auth(self::$_config['qiniu']['access_key'], self::$_config['qiniu']['secret_key']);
        }
    }

    public static function upload($filePath)
    {
        self::_init();
        $tempDomain = 'https://inimg03.jiuyan.info/';
        $imgFileName = 'in/' . date('Y/m/d/') . md5(microtime(true) . uniqid()) . '.jpg';
        $uploadToken = self::$_authHandle->uploadToken(self::$_config['qiniu']['bucket'], $imgFileName);
        list($ret, $error) = (new UploadManager())->putFile($uploadToken, $imgFileName, $filePath);
        if ($error || !isset($ret['key'])) {
            Log::error('system img upload error err:' . json_encode($error, JSON_UNESCAPED_UNICODE));
            return false;
        }
        return $tempDomain . $ret['key'];
    }

    public static function formatImg($imgServer, $imgUrl, $formatType = 'default', $imgExt = 'jpg')
    {
        self::_init();
        if (!$imgUrl || !in_array($formatType, self::$FORMAT_TYPE_RULES) || !array_key_exists($imgServer, self::$_config['in_img_domain'])) {
            Log::error('url: ' . $imgUrl . ' server:' . $imgServer . ' type:' . $formatType);
            throw new Exception('params err');
        }

        if (substr($imgUrl, 0, 4) == 'http') {
            return $imgUrl;
        }
        $imgUrl = '/' . ltrim($imgUrl, '/');

        //处理server，新图片走无锡，老图片走嘉兴
        $imgUrlCutParts = explode('/', $imgUrl);
        if ($imgServer == 1 && $imgUrlCutParts['1'] == 'in' && $imgUrlCutParts[2] . '-' . $imgUrlCutParts[3] . '-' . $imgUrlCutParts[4] >= '2014-11-24') {
            $imgServer = 4;
        } elseif ($imgServer == 4 && $imgUrlCutParts['1'] == 'in' && $imgUrlCutParts[2] . '-' . $imgUrlCutParts[3] . '-' . $imgUrlCutParts[4] < '2014-11-24'){
            $imgServer = 1;
        }elseif($imgServer == 4 && $formatType == 'w130' && $imgUrlCutParts[2] . '-' . $imgUrlCutParts[3] . '-' . $imgUrlCutParts[4] < '2015-01-22'){
            $formatType = 'default';
        }

        //处理域名，固定域名，避免客户端对同一张图片因为域名变化而重复加载
        $domainConfig = array_values(self::$_config['in_img_domain'][$imgServer]);
        $totalCount = count($domainConfig);
        $hash_id = base_convert(substr(md5($imgUrl), -2), 16, 10); //md5后取最后2位然后转换成十进制

        if ($imgUrlCutParts['1'] == 'in' && $imgUrlCutParts[2] . '-' . $imgUrlCutParts[3] . '-' . $imgUrlCutParts[4] == '2015-10-14') {
            $hash_id += 1;//七牛图片缓存出了问题，所以加个1，把域名错一位，来清除缓存
        }
        $index = $hash_id % $totalCount;
        $domain = $domainConfig[$index];
        $domain = RequestParamsComponent::isSecure() ? str_replace('http://', 'https://', $domain) : $domain;
        //非七牛图片的360尺寸图，默认不支持以高为基获取压缩图的方法
        $formatType = $formatType == 'h360' && $imgServer != 2 ? 'w360' : $formatType;

        $finalImg = '';
        switch ($imgServer) {
            case 1:
            case 4:
                if ($formatType == 'origin') {
                    $finalImg = $domain . "/origin" . $imgUrl;
                } elseif ($formatType == 'default') {
                    $finalImg = $domain . $imgUrl;
                } else {
                    $pathInfo = pathinfo($imgUrl);
                    $path = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.';
                    $postfix = $pathInfo['extension'];
                    if ($formatType == 'square') {
                        $finalImg = $domain . $path . 's.' . $postfix;
                    } else {
                        if ($formatType == 'w240') { //老图没有其他尺寸的，做一下兼容
                            $formatType = 'w208';
                        } elseif ($formatType == 'w320' || $formatType == 'w360' || $formatType == 'w720') {
                            $formatType = 'w640';
                        }
                        $finalImg = $domain . $path . $formatType . '.' . $postfix;
                    }
                }
                break;
            case 2:
            case 5:
            case 6:
                //客户端网络类型
                $net = RequestParamsComponent::net();
                if ($formatType == 'default' || $formatType == 'origin' || $formatType == 'itugo' || $formatType == 'square') {
                    $finalImg = $domain . $imgUrl;
                } else {
                    if ($formatType == 'w640') $formatType = 'w720'; //把640的图片改成720的，让android的清晰
                    if ($formatType == 'ww640') $formatType = 'w640';//和上面的区分 后台设置的展示尺寸为640
//                $qiniu_type = '?imageView2/2/w/' . str_replace('w', '', $type);
                    $imgExt = $imgExt ? $imgExt : 'png';

                    //根据type前缀判断是以宽为基还是以高为基
                    $size = substr($formatType, 1);
                    $compress = '';
                    if($formatType[0] == 'w'){
                        $compress = $size . 'x%3E';
                    }elseif($formatType[0] == 'h'){
                        $compress = 'x' . $size . '%3E';
                    }
                    $qiniu_type = '?imageMogr2/format/'.$imgExt.'/thumbnail/'. $compress. '/quality/' . ($net == 'wifi' ? 90 : 80) . '!'; //wifi下用质量90，其他用80
                    $finalImg = $domain . $imgUrl . $qiniu_type;
                }
                break;
            case 3:
                if ($formatType == 'default') {
                    $finalImg = $domain . $imgUrl;
                } else {
                    if ($formatType == 'w640') $formatType = 'w720';
                    $upyun_type = '!' . $formatType . '.jpg';
                    $finalImg = $domain . $imgUrl . $upyun_type;
                }
                break;
        }
        return $finalImg;
    }
}