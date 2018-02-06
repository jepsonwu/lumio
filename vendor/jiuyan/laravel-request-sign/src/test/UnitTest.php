<?php
/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/8/8
 * Time: 上午10:18
 */
use Jiuyan\Request\Tool\Impl\SignMD5;

class UnitTest extends PHPUnit_Framework_TestCase
{
    public function testTest()
    {
        $args = ["name" => "x", "p" => 3, "x" => "z"];
        app("md5")->setSignLength(39);
        $sign = app("md5")->makeSign(app("md5")->generateStringForSign($args), 1800, 1);
        $service = Mockery::mock('request');
        $service->shouldReceive('all')
            ->andReturn($args)
        ->shouldReceive('input')->with(env('REQUEST_SIGN_FLAG', '_sign'))->andReturn($sign);

        $s = new \Jiuyan\Request\Tool\SignMiddleware();
        $s->handle($service, function ($h) {

        });

    }
}
