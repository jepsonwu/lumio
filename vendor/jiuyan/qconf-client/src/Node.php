<?php
/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/7/17
 * Time: 下午6:23
 */

namespace Jiuyan\Qconf\Client;


use Jiuyan\Qconf\Client\Contract\QConf;

class Node
{
    protected $value;
    protected $tree;
    protected $qi;

    public function makeNode($key, $value, $nodes = [])
    {
        return ["$key" => [
            'value' => $value,
            "nodes" => $nodes
        ]];
    }

    public function makeNodeOnly($key, $value, $nodes = [])
    {
        return ['value' => $value,
            "nodes" => $nodes];
    }

    public function buildPath($level)
    {
        $path = '/' . implode('/', $level);
        return $path;
    }

    public function makeTree(&$tree, $level, QConf $qi)
    {
        $path = $this->buildPath($level);
        $keys = $qi->getBatchKeys($path, '', 0);
        if (!$keys) {
            return;
        }

        foreach ($keys as $key) {
//            $tree['value'] = $qi->getConf($path, '', 0);
            $value = $qi->getConf($path . '/' . $key, '', 0);
            $tree[$key] = $this->makeNodeOnly($key, $value);
            array_push($level, $key);
            $this->makeTree($tree[$key]['nodes'], $level, $qi);
            array_pop($level);

        }
    }

    public function setQconf(QConf $i)
    {
        $this->qi = $i;
    }

    public function run($root)
    {

        $level = [];
//        $roots = explode('/',trim($root,'/'));
//        if ($roots) {
//            while($p = array_pop($roots)){
//                array_unshift($level, $p);
//            }
//        }
//        $output['root'] = 'php';
//
//        $buildPath = $this->buildPath([ $output['root']]);
//        $value = $this->qi->getConf($buildPath);
//        $this->tree = $this->makeNode( $output['root'], $value, []);
//        var_dump($this->tree);exit;
//        $level = [];
        $this->makeTree($this->tree, $level, $this->qi);
        return  $this->tree;
    }
}