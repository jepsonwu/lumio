<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/16
 * Time: 18:41
 */

namespace Jiuyan\Common\Component\InFramework\Providers;

use Illuminate\Support\ServiceProvider;
use Jiuyan\Common\Component\InFramework\Components\JthriftServiceAgencyComponent;
use Jiuyan\Cuckoo\ThriftClient\ClientFactory;
use Jiuyan\Cuckoo\ThriftClient\Manager;
use Jiuyan\Cuckoo\ThriftClient\ThriftDao;
use Log;
use Exception;

class JThriftBaseServiceProvider extends ServiceProvider
{
    protected $_thriftManager = null;
    protected $_thriftInitialList = [];

    /**
     * thrift client update
     * @param $serverName
     * @return JthriftServiceAgencyComponent
     * @throws \Exception
     */
    protected function getThriftDao($serverName)
    {
        $manager = $this->getThriftClientManger($serverName);
        $thriftDao = new ThriftDao();
        $thriftDao->setManager($manager);
        $attacher = $thriftDao->service($serverName);
        //return 代理者
        return new JthriftServiceAgencyComponent($attacher);
    }

    protected function getThriftClientManger($serverName)
    {
        if (($this->_thriftManager == null) || !isset($this->_thriftInitialList[$serverName])) {
            $thriftConfig = config('thrift');
            if (!isset($thriftConfig[$serverName])) {
                throw new Exception('Without the service configuration', -1);
            }
            $thriftConfig[$serverName] = array_merge($thriftConfig['common'], $thriftConfig[$serverName]);
            $thriftConfig[$serverName]['client'] = $thriftConfig['common']['client_namespace'] . $thriftConfig[$serverName]['client'];
            $this->_thriftInitialList[$serverName] = $serverName;
            $this->_thriftManager = new Manager(new ClientFactory(), $thriftConfig);
            $this->_thriftManager->setLogger(Log::withName(Log::getName()));
        }
        return $this->_thriftManager;
    }
}