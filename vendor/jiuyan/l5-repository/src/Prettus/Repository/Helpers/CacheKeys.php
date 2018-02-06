<?php

namespace Prettus\Repository\Helpers;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Class CacheKeys
 * @package Prettus\Repository\Helpers
 */
class CacheKeys
{


    /**
     * @var array
     */
    protected static $keys = null;

    protected static $cacheRepository;


    /**
     * Return instance of Cache Repository
     *
     * @return CacheRepository
     */
    public static function getCacheRepository()
    {
        if (is_null(self::$cacheRepository)) {
            self::$cacheRepository = app(config('repository.cache.repository', 'cache'));
        }

        return self::$cacheRepository;
    }

    /**
     * @param $group
     * @param $key
     *
     * @return void
     */
    public static function putKey($group, $key)
    {
        self::loadKeys($group);

        self::$keys[$group] = self::getKeys($group);

        if (!in_array($key, self::$keys[$group])) {
            self::$keys[$group][] = $key;
        }

        self::storeKeys();
    }

    /**
     * @return array|mixed
     */
    public static function loadKeys($group)
    {
        if (isset(self::$keys[$group]) && !is_null(self::$keys[$group]) && is_array(self::$keys[$group])) {
            return self::$keys[$group];
        }

        $result = self::getCacheRepository()->get($group);
        $result = !empty($result) ? $result : '';

        self::$keys[$group] = json_decode($result, true);
        return self::$keys[$group];
    }


    /**
     * @return int
     */
    public static function storeKeys()
    {
        self::$keys = is_null(self::$keys) ? [] : self::$keys;

        foreach (self::$keys as $group => $value) {
            $content = empty($value) ? [] : $value;
            self::getCacheRepository()->forever($group, json_encode($content));
        }
    }

    /**
     * @param $group
     *
     * @return array|mixed
     */
    public static function getKeys($group)
    {
        self::loadKeys($group);
        self::$keys[$group] = isset(self::$keys[$group]) ? self::$keys[$group] : [];

        return self::$keys[$group];
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new static;

        return call_user_func_array([
            $instance,
            $method
        ], $parameters);
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $instance = new static;

        return call_user_func_array([
            $instance,
            $method
        ], $parameters);
    }
}
