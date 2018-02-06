<?php

namespace Jepsonwu\banyanDB\iterators;

/**
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/7/11
 * Time: 15:26
 */
class ScanIterator extends AbstractIterator
{
    protected $scan_start = "";
    protected $key_start = "";
    protected $isSeek = false;

    protected function initial()
    {
        $this->data = $this->scan();
    }

    protected function scan()
    {
        return $this->getBanyan()->scan($this->scan_start, "", $this->limit, $this->key_start);
    }

    public function rewind()
    {
        if (!$this->isSeek) {
            $this->scan_start = "";
            $this->key_start = "";
            $this->initial();
        }
        $this->getData() && $this->valid = true;

        return current($this->getData());
    }

    public function next()
    {
        $currentKey = key($this->getData());
        (!$this->retainData && $currentKey) && $this->getBanyan()->del($currentKey);

        $next = next($this->getData());
        $this->valid = (false !== $next);

        if (!$this->isPaging && !$this->valid && $this->getData()) {//next chunk
            end($this->getData());
            if ($this->isSetStructure()) {
                $this->scan_start = current($this->getData());
                $this->key_start = key($this->getData());
            } else {
                $this->scan_start = key($this->getData());
            }

            $this->initial();
            empty($this->getData()) || $this->valid = true;
            return current($this->getData());
        }

        return $next;
    }

    public function seek($position)
    {
        if ($this->isSetStructure()) {
            $this->key_start = $position;
        } else {
            $this->scan_start = $position;
        }

        $this->initial();
        return current($this->getData());
    }

    public function seekNext($position)
    {
        $newPosition = false;

        $this->seek($position) !== false && $newPosition = $this->key();
        if (empty($newPosition)) {
            $this->isSeek = false;
            $this->rewind() !== false && $newPosition = $this->key();
        } else {
            $this->isSeek = true;
        }

        return $newPosition;
    }

    public function seekPage($page)
    {
        $page = (int)$page;
        $totalPage = ceil($this->count() / $this->limit);
        if (!$totalPage || !$page || !$this->getData()) {
            return $this;
        }

        if ($page > $totalPage) {
            $this->data = [];
            $this->isSeek = true;
            return $this;
        }

        $this->isSeek = false;
        $this->rewind();

        if ($page == 1 && $this->scan_start == "") {
            return $this;
        }

        for ($i = 2; $i <= $page; $i++) {
            end($this->getData());
            if ($this->isSetStructure()) {
                $this->scan_start = current($this->getData());
                $this->key_start = key($this->getData());
            } else {
                $this->scan_start = key($this->getData());
            }
            $this->initial();
        }
        $this->isSeek = true;

        return $this;
    }
}
