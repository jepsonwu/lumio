<?php

namespace Jepsonwu\banyanDB;

use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/10/27
 * Time: 上午11:58
 */
class BanyanTest extends TestCase
{
    public function testHash()
    {
        $banyan = BanyanTestFactory::demoHash();

        $banyan->clear();
        sleep(1);
        $banyan->set("100", 1);
        sleep(1);

        $this->assertTrue($banyan->get(100) == 1, "get function is failure of hash structure");
        $this->assertTrue($banyan->size() == 1, "size function is failure of hash structure");

        $banyan->inc(100);
        sleep(1);
        $this->assertTrue($banyan->get(100) == 2, "inc function is failure of hash structure");

        $banyan->del(100);
        sleep(1);
        $this->assertTrue(is_null($banyan->get(100)), "del function is failure of hash structure");

        $banyan->set(101, 1);
        $banyan->set(102, 2);
        sleep(1);
        $result = array_values($banyan->scan("", "", 2));
        $this->assertTrue($result[0] == 1 && $result[1] == 2, "scan function is failure of hash structure");

        $result = array_values($banyan->rScan("", "", 2));
        $this->assertTrue($result[1] == 1 && $result[0] == 2, "rScan function is failure of hash structure");

        $result = array_values($banyan->hgetall());
        $this->assertTrue($result[0] == 1 && $result[1] == 2, "hgetall function is failure of hash structure");
    }

    public function testScanIterator()
    {
        $banyan = BanyanTestFactory::demoHash();
        $iterator = BanyanFactory::getScanIterator($banyan);

        $this->assertTrue(count($iterator) == 2, "scan iterator is failure");

        $i = 0;
        foreach ($iterator as $key => $value) {
            $isTrue = false;
            switch ($i) {
                case 0:
                    ($key == 101 && $value == 1) && $isTrue = true;
                    break;
                case 1:
                    ($key == 102 && $value == 2) && $isTrue = true;
                    break;
            }

            $this->assertTrue($isTrue, "scan iterator is failure");
            $i++;
        }

        $this->assertTrue(!empty($iterator->random()), "scan iterator random is failure");

        $result = $iterator->setLimit(2)->enablePaging()->seekPage(1);
        $this->assertTrue(count($result) == 2, "scan iterator page limit is failure");
    }

    public function testRScanIterator()
    {
        $banyan = BanyanTestFactory::demoHash();
        $iterator = BanyanFactory::getRScanIterator($banyan);

        $this->assertTrue(count($iterator) == 2, "rScan iterator is failure");

        $i = 0;
        foreach ($iterator as $key => $value) {
            $isTrue = false;
            switch ($i) {
                case 1:
                    ($key == 101 && $value == 1) && $isTrue = true;
                    break;
                case 0:
                    ($key == 102 && $value == 2) && $isTrue = true;
                    break;
            }

            $this->assertTrue($isTrue, "rScan iterator is failure");
            $i++;
        }
    }

    public function testSet()
    {
        $banyan = BanyanTestFactory::demoSet();

        $banyan->clear();
        sleep(1);
        $banyan->set("100", 1);
        sleep(1);

        $this->assertTrue($banyan->get(100) == 1, "get function is failure of set structure");
        $this->assertTrue($banyan->size() == 1, "size function is failure of set structure");

        $banyan->inc(100);
        sleep(1);
        $this->assertTrue($banyan->get(100) == 2, "inc function is failure of set structure");

        $banyan->del(100);
        sleep(1);
        $this->assertTrue(is_null($banyan->get(100)), "del function is failure of set structure");

        $banyan->set(101, 1);
        $banyan->set(102, 2);
        sleep(1);
        $result = array_values($banyan->scan("", "", 2));
        $this->assertTrue($result[0] == 1 && $result[1] == 2, "scan function is failure of set structure");

        $result = array_values($banyan->rScan("", "", 2));
        $this->assertTrue($result[1] == 1 && $result[0] == 2, "rScan function is failure of set structure");
    }

    public function testKey()
    {
        $banyan = BanyanTestFactory::common();
        $banyan->set(BanyanTestFactory::NAME_DEMO_KEY, 1, 4);
        sleep(1);

        $this->assertTrue(
            $banyan->get(BanyanTestFactory::NAME_DEMO_KEY) == 1,
            "get function is failure of key structure"
        );

        $banyan->inc(BanyanTestFactory::NAME_DEMO_KEY);
        sleep(1);
        $this->assertTrue(
            $banyan->get(BanyanTestFactory::NAME_DEMO_KEY) == 2,
            "inc function is failure of key structure"
        );

        sleep(3);

        $this->assertTrue(
            is_null($banyan->get(BanyanTestFactory::NAME_DEMO_KEY)),
            "expire function is failure of key structure"
        );
    }

    public function testSpecialKey()
    {
        $banyan = BanyanTestFactory::demoSpecialKey();
        $banyan->set(1, 4);
        sleep(1);

        $this->assertTrue(
            $banyan->get() == 1,
            "get function is failure of special key structure"
        );

        $banyan->inc();
        sleep(1);
        $this->assertTrue(
            $banyan->get() == 2,
            "inc function is failure of special key structure"
        );

        sleep(3);

        $this->assertTrue(is_null($banyan->get()), "expire function is failure of special key structure");
    }

    public function testQueue()
    {
        $banyan = BanyanTestFactory::demoQueue();
        $banyan->clear();
        sleep(1);
        $banyan->set(1);
        sleep(1);

//        $this->assertTrue(
//            $banyan->get() == 1,
//            "get function is failure of queue structure"
//        );

        $banyan->clear();
        sleep(1);
        $this->assertTrue($banyan->size() == 0, "clear function is failure of queue structure");
    }
}
