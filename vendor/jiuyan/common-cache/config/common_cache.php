<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/13
 * Time: 10:22
 */
return [
    'handle' => [
        'banyandb' => 'Jiuyan\CommonCache\Handles\BanyanDBHandle::getHandle'
    ],
    'servers' => [
        'banyandb' => [
            'hosts' => [],
            'max_reconnect_tries' => 2
        ]
    ]
];
