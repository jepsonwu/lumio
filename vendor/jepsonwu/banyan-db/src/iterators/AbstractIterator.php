<?php

namespace Jepsonwu\banyanDB\iterators;

use Jepsonwu\banyanDB\InterfaceBanyan;
use Jepsonwu\banyanDB\structures\SetStructure;
use Iterator;
use SeekableIterator;
use Countable;

/**
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/7/11
 * Time: 15:02
 */
abstract class AbstractIterator implements Iterator, SeekableIterator, Countable
{
    const STRUCTURE_HASH = 1;
    const STRUCTURE_SET = 2;

    private $banyan;
    private $structure = 1;

    protected $data;
    protected $limit = 100;
    protected $isPaging = false;

    protected $valid = false;

    protected $retainData = true;//if retain the data after the iteration

    public function __construct(InterfaceBanyan $interfaceBanyan)
    {
        $this->banyan = $interfaceBanyan;
        if ($interfaceBanyan instanceof SetStructure) {
            $this->structure = self::STRUCTURE_SET;
        }
    }

    abstract protected function initial();

    protected function getBanyan()
    {
        return $this->banyan;
    }

    protected function &getData()
    {
        is_null($this->data) && $this->initial();
        return $this->data;
    }

    protected function isSetStructure()
    {
        return $this->structure == self::STRUCTURE_SET;
    }

    public function current()
    {
        return current($this->getData());
    }

    public function key()
    {
        return key($this->getData());
    }

    public function valid()
    {
        return $this->valid;
    }

    public function count()
    {
        return $this->banyan->size();
    }

    public function setLimit($limit)
    {
        $this->limit = (int)$limit;
        return $this;
    }

    public function enablePaging()
    {
        $this->isPaging = true;
        return $this;
    }

    public function disablePaging()
    {
        $this->isPaging = false;
        return $this;
    }

    public function setRetainData($retain = false)
    {
        $this->retainData = $retain;
        return $this;
    }

    public function random($num = 1)
    {
        $result = [];

        $this->rewind();
        $data = $this->getData();
        if (empty($data)) {
            return [];
        }

        $this->randomPage();

        $data = $this->getData();
        $randomInfo = (array)array_rand($data, $num);
        foreach ($randomInfo as $randomKey) {
            $result[$randomKey] = $data[$randomKey];
        }

        return $result;
    }

    protected function randomPage()
    {
        $totalPage = ceil($this->count() / $this->limit);
        $this->seekPage(rand(0, $totalPage));
        return true;
    }

    abstract public function seekNext($position);

    abstract public function seekPage($page);
}
