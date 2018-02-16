<?php

namespace Jiuyan\Common\Component\InFramework\Traits;

use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Exception;

trait DBTrait
{
    public function doingTransaction(callable $function, Collection $repositoryCollection, $errorTpl)
    {
        /** @var $repository BaseRepository * */
        $repository = $repositoryCollection->take(0);

        $repository->beginTransaction($repositoryCollection);
        try {
            $result = call_user_func_array($function, []);

            $repository->commit();
        } catch (Exception $e) {
            $repository->rollBack();
            if ($e instanceof DBException) {
                ExceptionResponseComponent::business($errorTpl);
            } else {
                throw new Exception();
            }

            //todo log
        }

        return $result;
    }
}