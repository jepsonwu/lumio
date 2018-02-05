<?php
namespace Tests;

use \Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function assertStringContains($haystack, $needle){
        $true = strpos($haystack, $needle) !== false;
        $this->assertTrue($true);
    }

    protected function _checkKeyExists($needle, $haystack)
    {
        if (isset($haystack[$needle])) {
            return true;
        }
        $this->_recordError('current key dose not exist k:' . $needle);
        return false;
    }

    /**
     * @param $typeFlag
     *      enum:1|2|3
     *      integer
     *      string
     *      float
     * @param $resource
     * @return bool
     */
    protected function _checkDataType($typeFlag, $resource)
    {
        $ruleList = [];
        if (strpos($typeFlag, ':') !== false) {
            $typeRules = explode(':', $typeFlag);
            $ruleList = explode('|', $typeRules[1]);
            if (!$ruleList) {
                $this->_recordError('enum rules is invalid rule:' . $typeFlag);
            }
            $typeFlag = $typeRules[0];
        }
        switch ($typeFlag) {
            case 'integer':
                $resource = intval($resource);
                break;
            case 'float':
                $resource = floatval($resource);
                break;
            case 'enum':
                $this->assertContains($resource, $ruleList);
                return true;
        }
        $this->assertInternalType($typeFlag, $resource);
    }

    protected function _testJsonResult($rules, $result)
    {
        foreach ($rules as $k => $item) {
            if ($this->_checkKeyExists($k, $result)) {
                if (is_array($item)) {
                    $this->_testJsonResult($item, $result[$k]);
                } else {
                    $this->_checkDataType($item, $result[$k]);
                }
            }
        }
    }

    protected function _recordNotice($msg)
    {
        $this->_echoLog($msg, 'notice');
    }

    protected function _recordError($msg)
    {
        $this->_echoLog($msg, 'error');
    }

    private function _echoLog($msg, $type = 'notice')
    {
        $msgBorderItem = '=';
        $msgLength = strlen($msg);
        $msgBorder = str_repeat($msgBorderItem, $msgLength + 4);
        $msgBorderLength = strlen($msgBorder);
        $msgFlag = ' ' . strtoupper($type) . ' ';
        $msgSubTopBorder = str_repeat($msgBorderItem, ($msgBorderLength - strlen($msgFlag))/2 + 1);
        $msgTopBorder = substr(($msgSubTopBorder . $msgFlag . $msgSubTopBorder), 0, $msgBorderLength);
        echo $msgTopBorder . PHP_EOL;
        echo "{$msgBorderItem} {$msg} {$msgBorderItem}" . PHP_EOL;
        echo $msgBorder . PHP_EOL;
    }
}
