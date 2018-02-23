<?php

namespace Jiuyan\Common\Component\InFramework\Contracts;

interface ServiceRepositoryDependenceContract
{
    //todo public 方法 对外暴露了 有何意义 
    public function getRepository();

    public function setRepository($repository);
}