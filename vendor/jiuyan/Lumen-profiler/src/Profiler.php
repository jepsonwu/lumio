<?php
namespace Jiuyan\Profiler;

use XHProfRuns_Default;

class Profiler
{
    /**
     * determiner started
     * @var boolean
     */
    private $started = false;

    const XHP = 'xhp';

    const TYPE = '1';

    const DIR = '/var/log/xhprof';

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function start()
    {
        if (! function_exists('xhprof_enable') || ! $this->config["enabled"]) {
            return false;
        }

        if (! isset($_REQUEST[self::XHP]) || $_REQUEST[self::XHP] != '1') {
            return false;
        }

        xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);

        $this->started = true;

        return true;
    }

    public function getType()
    {
        return isset($this->config['type']) ? $this->config['type'] : self::TYPE;
    }

    public function getDir()
    {
        return isset($this->config['dir']) ? $this->config['dir'] : self::DIR;
    }

    public function getNamespace()
    {
        $namespace = '';
        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $urlInfo = parse_url($_SERVER['REQUEST_URI']);
            $namespace = str_replace('/','-', trim($urlInfo['path'], '/')) . '-' . date('YmdHis');
        }
        return $namespace;
    }


    public function save()
    {
        if (! $this->started) {
            return false;
        }
        $data =  xhprof_disable();
        $type = $this->getType();
        $dir = $this->getDir();
        $namespace = $this->getNamespace();
        $xhprof = new XHProfRuns_Default($dir);
        $xhprof->save_run($data, $type, $namespace);

        return true;
    }
}
