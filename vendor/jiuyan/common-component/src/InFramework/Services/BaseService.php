<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/9/26
 * Time: 15:45
 */

namespace Jiuyan\Common\Component\InFramework\Services;

use Jiuyan\Common\Component\InFramework\Components\RequestParamsComponent;
use Jiuyan\Common\Component\InFramework\Events\BaseEvent;
use Jiuyan\Common\Component\InFramework\Exceptions\ServiceException;

class BaseService
{
    /**
     * @var RequestParamsComponent
     */
    protected $_requestParamsComponent;

    public function getServiceResponse($responseData, $responseTpl, $responseStatus = true)
    {
        return [$responseData, $responseTpl, $responseStatus];
    }

    public function triggerEvent($eventHandleName, &$requestParams = [])
    {
        if (!class_exists($eventHandleName)) {
            throw new ServiceException('event not exist');
        }
        /**
         * @var $eventHandle BaseEvent
         */
        $eventHandle = new $eventHandleName();
        $eventHandle->setRequestCommonParams(RequestParamsComponent::getAllCommonParams());
        $eventHandle->setRequestGeneralParams($requestParams);
        event($eventHandle);
    } 
}
