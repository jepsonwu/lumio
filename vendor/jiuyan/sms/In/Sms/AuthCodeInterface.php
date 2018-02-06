<?php
/**
 * Created by PhpStorm.
 * User: Ziliang
 * Date: 16/5/4
 * Time: 下午3:41
 */
namespace In\Sms;

interface AuthCodeInterface{

    /**
     * @param $mobile
     * @return $field   ['number'=>$mobile,'code'=>$code,'created_at'=>$created_at]
     */

    function update($mobile, array $field);
    function add(array $field);
    function find($mobile);

}

