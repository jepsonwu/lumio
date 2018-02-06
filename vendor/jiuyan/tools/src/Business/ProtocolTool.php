<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/1
 * Time: 上午11:33
 */

namespace Jiuyan\Tools\Business;

class ProtocolTool
{
    /**
     * 协议前缀
     * @return string
     */
    public static function getPrefix()
    {
        return "in://";
    }

    /**
     * webview协议名称
     * @return string
     */
    public static function getWebviewName()
    {
        return "webview";
    }

    /**
     * 构建协议
     * @param $name
     * @param array $params
     * @return string
     */
    public static function build($name, array $params = [])
    {
        $params = $params ? "?" . http_build_query($params) : "";
        return self::getPrefix() . $name . $params;
    }

    /**
     * 构建webview协议
     * @param $url
     * @param array $params
     * @return string
     */
    public static function webview($url, array $params = [])
    {
        $params['url'] = $url;
        return self::build(self::getWebviewName(), $params);
    }

    /**
     * h5兼容协议
     * @param $name
     * @param array $params
     * @return string
     */
    public static function h5Compatibility($name, array $params = [])
    {
        $protocol = self::build($name, $params);
        $protocol = "{'iosMessage':'" . $protocol . "','androidMessage':'" . $protocol . "'}";

        return $protocol;
    }

    public static function h5WebviewCompatibility($url, array $params = [])
    {
        $params['url'] = $url;
        return self::h5Compatibility(self::getWebviewName(), $params);
    }
}
