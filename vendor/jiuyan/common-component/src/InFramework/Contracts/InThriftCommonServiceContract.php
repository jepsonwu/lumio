<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/19
 * Time: 15:29
 */

namespace Jiuyan\Common\Component\InFramework\Contracts;

interface InThriftCommonServiceContract
{
    public function initCommonParams($commonParams);

    public function getAllRequestCommonParams();

    public function appendRequestCommonParam($key, $val);

    public function getRequestCommonParam($paramKey);
}