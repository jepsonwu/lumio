<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/2
 * Time: 21:03
 */

return [
    'qiniu' => [
        'access_key' => env('QINIU_ACCESS_KEY', 'I5m6XV-FOs1aUPQnu7v4eF_yLgJjbgoZrrWT3Ghk'),
        'secret_key' => env('QINIU_SECRET_KEY', '8PSIInkjk5cXmDcQFN3Ix1fg34lRVB2QToT0yiri'),
        'bucket' => env('QINIU_BUCKET', 'infashion'),   //存储空间
        'expires' => env('QINIU_EXPIRES', '86400'),     //过期时间 s
        'bucket_private' => env('QINIU_PRIVATE', '')
    ],
    'in_img_domain' => [
        //自己服务器cdn域名
        1 => [
            1 => 'http://u1.jiuyan.info',
            2 => 'http://u2.jiuyan.info',
            3 => 'http://u3.jiuyan.info',
            4 => 'http://i4.jiuyan.info',
            5 => 'http://i5.jiuyan.info',
            6 => 'http://i6.jiuyan.info'
        ],
        //七牛服务器cdn域名
        2 => [
            1 => 'http://inimg01.jiuyan.info',
            2 => 'http://inimg02.jiuyan.info',
            3 => 'http://inimg01.jiuyan.info',
            4 => 'http://inimg02.jiuyan.info',
            5 => 'http://inimg05.jiuyan.info',
        ],
        //Upyun服务器的 图片域名
        3 => [
            1 => 'http://down2.jiuyan.info',
            2 => 'http://down2.jiuyan.info',
            3 => 'http://down2.jiuyan.info',
        ],
        //无锡服务器的 图片域名
        4 => [
            1 => 'http://wd1.jiuyan.info',
            2 => 'http://wd2.jiuyan.info',
            3 => 'http://wd3.jiuyan.info',
            4 => 'http://wd4.jiuyan.info',
            5 => 'http://wd5.jiuyan.info',
            6 => 'http://wd6.jiuyan.info',
        ],
        //自己服务器cdn域名
        4 => [
            1 => 'http://www.local-upload.com',
            2 => 'http://www.local-upload.com',
            3 => 'http://www.local-upload.com',
            4 => 'http://www.local-upload.com',
            5 => 'http://www.local-upload.com',
            6 => 'http://www.local-upload.com',
        ],
        //七牛
        5 => [
            1 => 'http://oacc1xr28.bkt.clouddn.com',
            2 => 'http://oacc1xr28.bkt.clouddn.com',
        ]
    ],
    'encrypt' => [
        'id' => [
            'salt' => 'D@A2(F8*6~',
            'hash_length' => 0,
            'range' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
        ]
    ]
];