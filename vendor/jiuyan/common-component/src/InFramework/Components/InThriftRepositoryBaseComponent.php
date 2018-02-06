<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/24
 * Time: 17:09
 */

namespace Jiuyan\Common\Component\InFramework\Components;

use Jthrift\Services\ApiServiceIf;

class InThriftRepositoryBaseComponent
{
    /**
     * @var ApiServiceIf
     */
    public $modelHandle = null;

    public static function formatThriftResponse($result)
    {
        $finalData = $result ? json_decode($result, true) : [];
        return $finalData;
    }
}