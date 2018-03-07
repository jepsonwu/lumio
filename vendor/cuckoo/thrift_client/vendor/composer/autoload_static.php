<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit00150dc0443e91d00bc0082d9e703102
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'Jiuyan\\Cuckoo\\ThriftClient\\' => 27,
        ),
        'D' => 
        array (
            'Domnikl\\Statsd\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Jiuyan\\Cuckoo\\ThriftClient\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Domnikl\\Statsd\\' => 
        array (
            0 => __DIR__ . '/..' . '/Jiuyan/statsd/lib',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Thrift\\' => 
            array (
                0 => __DIR__ . '/..' . '/packaged/thrift/src',
            ),
        ),
    );

    public static $classMap = array (
        'Test\\BanyandbServiceClient' => __DIR__ . '/../..' . '/thrift/packages/Test/BanyandbService.php',
        'Test\\BanyandbServiceIf' => __DIR__ . '/../..' . '/thrift/packages/Test/BanyandbService.php',
        'Test\\BanyandbService_ping_args' => __DIR__ . '/../..' . '/thrift/packages/Test/BanyandbService.php',
        'Test\\BanyandbService_ping_result' => __DIR__ . '/../..' . '/thrift/packages/Test/BanyandbService.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit00150dc0443e91d00bc0082d9e703102::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit00150dc0443e91d00bc0082d9e703102::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit00150dc0443e91d00bc0082d9e703102::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit00150dc0443e91d00bc0082d9e703102::$classMap;

        }, null, ClassLoader::class);
    }
}
