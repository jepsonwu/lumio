<?php

namespace Jiuyan\Common\Component\InFramework\Traits;

use Prettus\Repository\Eloquent\BaseRepository;

trait ServiceRepositoryDependenceTrait
{
    protected $_repository;
    
    public function getRepository()
    {
        return $this->_repository;
    }
    
    public function setRepository($repository)
    {
        is_null($this->_repository) && $this->_repository = $repository;
    }
}