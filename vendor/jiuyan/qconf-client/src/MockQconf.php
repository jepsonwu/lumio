<?php
namespace Jiuyan\Qconf\Client;

use Dingo\Api\Http\Response\Format\Json;
use Illuminate\Support\Arr;
use Jiuyan\Qconf\Client\Contract\QConf as BaseQConf;
use Exception;

class MockQconf extends BaseQConf
{
    private $config = null;
    protected $idc = null;
    protected $mockRoot;
    protected $nodeDl;

    public function __construct($idc = '', array $config = [])
    {
        $this->nodeDl = new Node();
    }

    /**
     * @return array|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array|null $config
     */
    public function setConfig($config)
    {
        if (!file_exists($config)) {
            throw new Exception("if u use mocking , please set mock data!");
        }
        $data = json_decode(file_get_contents($config), 1);
        $this->config = $data;
    }

    public function setMockPath($path, $level = 'test', $writeRoot = 'config')
    {
        $this->mockRoot = $writeRoot;
        $result = $this->readAllFiles($path);
        $files = $this->getLevelPHP($result['files'], $level);
        $data = eval($this->executePhpScript($files, $path, $this->mockRoot));
        $this->config = $this->nodeDl->makeNodeOnly($this->mockRoot, '');

        $json = json_encode($data, JSON_PRETTY_PRINT);
        $obj = json_decode($json);
//        var_dump($obj);exit;
        $jb = JSONObject::Factory($obj);
        $this->nodeDl->setQconf($jb);
        $this->config = $this->nodeDl->run($this->mockRoot);
//        var_dump($this->config);exit;
    }

    public function setMockEnv($path, $writeRoot)
    {
        $data = file($path);
        $conf = [];
        foreach ($data as $line) {
            $trim = trim($line);
            if ($trim) {
                $line = $trim;
                $config = explode('=', $line);
                $count = count($config);
                if ($config && $count >= 2) {
                    $substr = substr($line, strlen($config[0]) + 1);
                    $conf[trim($config[0])] = trim($substr);
                }
            }
        }
        $seg = explode('/', trim($writeRoot, '/'));
        $last = null;
        $level = null;
        $tmp = [];
        while ($pop = array_shift($seg)) {
            if ($level === null) {
                $tmp[$pop] = [];
                $level = &$tmp[$pop];
            } else {
                $level[$pop] = [];
                $level = &$level[$pop];
            }
        }
        $level = $conf;
        $json = json_encode($tmp, JSON_PRETTY_PRINT);
        $obj = json_decode($json);
        $jb = JSONObject::Factory($obj);
        $this->nodeDl->setQconf($jb);
        $this->config = $this->nodeDl->run($this->mockRoot);
    }

    protected function executePhpScript($files, $project_path, $root = '')
    {
        $rootP = explode('/', trim($root, '/'));
        $code = "\r\n";
        foreach ($files as $k => $file) {
            $code .= "\$data_$k = require '$file';\r\n";
            $path = substr(basename($file), 0, -4);
            $key = str_replace($project_path, '', $file);
            $key = str_replace('', '', dirname($key));
            $key = trim($key, '/');
            $code .= "\$confItem['$key']['$path'] = \$data_$k;\r\n";
        }
        $code .= "\$conf";
        foreach ($rootP as $p) {
            $code .= "['$p']";
        }
        $code .= "= \$confItem ;\r\n";
        $code .= "return \$conf; ";
        file_put_contents("/tmp/compile.php", $code);
        return $code;
    }

    protected function getLevelPHP($files, $level)
    {
        $r = [];
        foreach ($files as $file) {
            $strrpos1 = substr($file, -4);
            if ($strrpos1 === '.php') {
                $strrpos = strrpos($file, '/' . $level . '.php');
                if ($strrpos) {
                    $r[] = $file;
                }
            }
        }
        return $r;

    }

    protected function readAllFiles($root = '.')
    {
        $files = array('files' => array(), 'dirs' => array());
        $directories = array();
        $last_letter = $root[strlen($root) - 1];
        $root = ($last_letter == '\\' || $last_letter == '/') ? $root : $root . DIRECTORY_SEPARATOR;

        $directories[] = $root;

        while (sizeof($directories)) {
            $dir = array_pop($directories);
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == '.' || $file == '..' || $file[0] == '.') {
                        continue;
                    }
                    $file = $dir . $file;
                    if (is_dir($file)) {
                        $directory_path = $file . DIRECTORY_SEPARATOR;
                        array_push($directories, $directory_path);
                        $files['dirs'][] = $directory_path;
                    } elseif (is_file($file)) {
                        $files['files'][] = $file;
                    }
                }
                closedir($handle);
            }
        }

        return $files;
    }

    /**
     * @return null
     */
    public function getIdc()
    {
        return $this->idc;
    }

    /**
     * @param null $idc
     */
    public function setIdc($idc)
    {
        $this->idc = $idc;
    }

    protected function getNodeValue($path, $tree)
    {
        $p = array_shift($path);
        if (!$tree) {
            $tree = $this->config;
        }
        if (!isset($tree[$p]['value']))  return [];
        if (count($path) == 0) {
            return isset($tree[$p]['value']) ? $tree[$p]['value'] : '';
        }

        return $this->getNodeValue($path, $tree[$p]['nodes']);
    }

    protected function getNodeKeys($path, $tree)
    {
        $p = array_shift($path);
        if (!$tree) {
            $tree = $this->config;
        }
        if (!isset($tree[$p]['nodes'])) {
            return [];
        }
        if (count($path) == 0) {
            return array_keys(isset($tree[$p]['nodes']) ? $tree[$p]['nodes'] : []);
        }
        return $this->getNodeKeys($path, $tree[$p]['nodes']);
    }

    public function getConf($path, $default = null, $flag = null)
    {
        $path = explode('/', ltrim($path, '/'));
//        var_dump($path,$this->config);exit;
        return $this->getNodeValue($path, $this->config);
    }

    public function getBatchKeys($path, $default, $flag = null)
    {
        $path = explode('/', ltrim($path, '/'));
        return $this->getNodeKeys($path, $this->config);
    }

    protected function getNodeKeysValue($path, $tree)
    {
        $p = array_shift($path);
        if (!$tree) {
            $tree = $this->config;
        }
        if (count($path) == 0) {
            $result = [];
            if (isset($tree[$p]['nodes']) && is_array($tree[$p]['nodes'])) {
                foreach ($tree[$p]['nodes'] as $key => $children) {
                    $result[$key] = $children['value'];
                }
            }
            return $result;
        }
        return $this->getNodeKeysValue($path, $tree[$p]['nodes']);
    }

    public function getBatchConf($path, $default, $flag = null)
    {
        $path = explode('/', ltrim($path, '/'));
        return $this->getNodeKeysValue($path, $this->config);
    }

    public function getAllHost($path, $default, $flag = null)
    {
        return null;
    }

    public function getHost($path, $default, $flag = null)
    {

        return null;

    }
}
