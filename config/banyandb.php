<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/1
 * Time: 下午5:15
 */
return [
    'handle' => 'Jiuyan\Lumio\BanyanDB\Handles\BanyanDBHandle::getClient',
    'servers' => [
        'hosts' => [
            env('BANYANDB_HOSTS_1'),
            env('BANYANDB_HOSTS_2'),
            env('BANYANDB_HOSTS_3'),
            env('BANYANDB_HOSTS_4'),
            env('BANYANDB_HOSTS_5'),
            env('BANYANDB_HOSTS_6'),
            env('BANYANDB_HOSTS_7'),
            env('BANYANDB_HOSTS_8'),
        ],
        'max_reconnect_tries' => 2
    ]
];