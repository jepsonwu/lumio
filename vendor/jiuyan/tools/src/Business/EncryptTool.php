<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/1
 * Time: 16:07
 */

namespace Jiuyan\Tools\Business;


use Hashids\Hashids;
use Jiuyan\Tools\ConfigAutoload;

class EncryptTool
{
    protected static $_config;

    protected static $_instances;

    protected static function _init()
    {
        ConfigAutoload::register();
        if (!self::$_config) {
            self::$_config = config('tools')['encrypt'];
        }
    }

    /**
     * @return Hashids
     */
    protected static function _getHashidInstance()
    {
        if (!self::$_instances['hash_id']) {
            self::$_instances['hash_id'] = new Hashids(self::$_config['id']['salt'], self::$_config['id']['hash_length'], self::$_config['id']['range']);
        }
        return self::$_instances['hash_id'];
    }

    public static function encryptId($id)
    {
        self::_init();
        if (function_exists('hashid_encode')) {
            $encryptRes = hashid_encode($id, self::$_config['id']['salt'], self::$_config['id']['hash_length'], self::$_config['id']['range']);
        } else {
            $encryptRes = self::_getHashidInstance()->encode($id);
        }
        return $encryptRes ? '1' . $encryptRes : '';
    }

    public static function decryptId($str)
    {
        self::_init();
        if (is_numeric($str)) {
            return $str;
        }
        $str = substr($str, 1);
        if (function_exists('hashid_decode')) {
            return hashid_decode($str, self::$_config['id']['salt'], self::$_config['id']['hash_length'], self::$_config['id']['range']);
        } else {
            $res = self::_getHashidInstance()->decode($str);
            return $res ? $res[0] : '';
        }
    }
}