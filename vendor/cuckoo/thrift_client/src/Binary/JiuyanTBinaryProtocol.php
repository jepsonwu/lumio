<?php

namespace Jiuyan\Cuckoo\ThriftClient\Binary;

use Thrift\Protocol\TBinaryProtocol;

// 九言科技自定义的协议
class JiuyanTBinaryProtocol extends TBinaryProtocol
{
    private $serviceName;
    private $commonParamArr;
    private $version = "1.0";
    private $language = "php";

    public function __construct($trans, $serviceName, $commonParamArr = array())
    {
        parent::__construct($trans);
        $this->commonParamArr = $commonParamArr;
        $this->serviceName = $serviceName;
    }

    public function conStrFromArr($name, $service, $commonParamArr)
    {
        if (empty($service) || empty($name)) {
            throw new \Exception("invalid protocol");
        }
        $commonParamArr['service'] = $service;
        $commonParamArr['name'] = $name;
        $commonParamArr['version'] = $this->version;
        $commonParamArr['language'] = $this->language;
        ksort($commonParamArr);
        $resultArr = array();
        foreach ($commonParamArr as $key => $value) {
            array_push($resultArr, $key . "|" . $value);
        }
        return implode("|", $resultArr);
    }

    public function writeMessageBegin($name, $type, $seqid)
    {
        $name = self::conStrFromArr($name, $this->serviceName, $this->commonParamArr);
        parent::writeMessageBegin($name, $type, $seqid);
    }
}
