<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/1
 * Time: 16:07
 */

namespace Jiuyan\Tools\Business;

use Exception;

class JsonTool
{
    const SUPPORT_TYPE_JY_FUNCTION = 1;
    const SUPPORT_TYPE_JY_OPTIONS = 2;

    public static function JyJsonEncode($var, $options = 0)
    {
        try {
            $supportType = self::isSupportJyJson();
            if ($supportType == self::SUPPORT_TYPE_JY_FUNCTION) {
                return jyjson_encode($var);
            }
            if ($supportType == self::SUPPORT_TYPE_JY_OPTIONS) {
                return json_encode($var, $options | JSON_LONG_TO_STR | JSON_NULL_TO_STR);
            }
        } catch (Exception $e) {
        }
        return false;
    }

    public static function isSupportJyJson()
    {
        if (function_exists('jyjson_encode')) { //@一章 写的php扩展
            return self::SUPPORT_TYPE_JY_FUNCTION;
        }
        if (PHP_MAJOR_VERSION >= 7 && defined('JSON_LONG_TO_STR') && defined('JSON_NULL_TO_STR')) {
            return self::SUPPORT_TYPE_JY_OPTIONS;
        }
        return 0;
    }

    public static function encode($var, $options = 0)
    {
        if ($result = self::JyJsonEncode($var, $options)) {
            return $result;
        }

        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false';
            case 'NULL':
                return '""'; //把null也输出成""
            case 'integer':
                //return (int) $var; //和它爸爸唯一的差别，就是为了把数字都用string类型输出
                return '"' . $var . '"';
            case 'double':
            case 'float':
                // locale-independent representation
                return str_replace(',', '.', (float)$var);
            case 'string':
                return json_encode($var);
            case 'array':
                // treat as a JSON object
                if (is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
                    return '{' . join(',', array_map('self::nameValue', array_keys($var), array_values($var))) . '}';
                }

                // treat it like a regular array
                return '[' . join(',', array_map('self::encode', $var)) . ']';

            case 'object':
                if ($var instanceof Traversable) {
                    $vars = [];
                    foreach ($var as $k => $v) {
                        $vars[$k] = $v;
                    }
                } else {
                    $vars = get_object_vars($var);
                }
                return '{' . join(',', array_map('self::nameValue', array_keys($vars), array_values($vars))) . '}';

            default:
                return '';
        }
    }

    protected static function nameValue($name, $value)
    {
        return self::encode(strval($name)) . ':' . self::encode($value);
    }
}