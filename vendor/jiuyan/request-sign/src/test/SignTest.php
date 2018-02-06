<?php
/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/8/8
 * Time: 上午10:18
 */
use Jiuyan\Request\Tool\Impl\SignMD5;

class SignTest extends PHPUnit_Framework_TestCase
{
    public function testSign()
    {
        $md5 = (new \Jiuyan\Request\Tool\SignFactory())->make(SignMD5::class);
        echo $string = $md5->generateStringForSign(['age' => 1, 'name' => 3, 'page' => 3, 'offset' => 4], false);
        $this->assertTrue(is_string($string));
    }

    public function testMake()
    {
        $md5 = (new \Jiuyan\Request\Tool\SignFactory())->make(SignMD5::class);
        $string = $md5->generateStringForSign(['age' => 1, 'name' => 3, 'page' => 3, 'offset' => 4], false);
        echo $md5->makeSign($string, 180);
        $this->assertTrue(is_string($string));
    }

    public function testCheck()
    {
        $md5 = (new \Jiuyan\Request\Tool\SignFactory())->make(SignMD5::class);
        $string = $md5->generateStringForSign(['age' => 1, 'name' => 3, 'page' => 3, 'offset' => 4], false);
        $sign = $md5->makeSign($string, 180);

        $result = $md5->checkSign($string, $sign, 180);

        $this->assertTrue($result);
        $this->assertTrue(is_bool($result));
    }
}
