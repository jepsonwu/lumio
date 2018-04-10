<?php

namespace Jiuyan\Common\Component\InFramework\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Jiuyan\Common\Component\InFramework\Exceptions\BusinessException;
use Prettus\Repository\Eloquent\BaseRepository;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Exception;
use Jiuyan\Common\Component\InFramework\Exceptions\DBException;

trait DBTrait
{
    public function doingTransaction(callable $function, Collection $repositoryCollection, $errorTpl)
    {
        /** @var $repository BaseRepository * */
        $repository = $repositoryCollection->first();

        $repository->beginTransaction($repositoryCollection);
        try {
            $result = call_user_func_array($function, []);

            $repository->commit();
            return $result;
        } catch (Exception $e) {
            $repository->rollBack();
            if ($e instanceof DBException) {
                ExceptionResponseComponent::business($errorTpl);
            } elseif ($e instanceof BusinessException) {
                throw new BusinessException($e->getMessage(), $e->getCode());
            } else {
                Log::error("db doing transaction failed,message:" . $e->getMessage());
                throw new Exception();
            }

            return false;
        }
    }
}