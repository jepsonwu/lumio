<?php
namespace Jiuyan\Common\Component\Response;
/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/9/18
 * Time: 下午5:27
 */
class ApiResponse
{
    protected $data;
    protected $msg;
    protected $code;
    protected $succ;
    public   function __construct($data, $msg = '', $code = 0, $succ = true)
    {
        if (!php_sapi_name() == 'cli') {
            header('Content-Type: application/json');
        }
        $this->data = $data;
        $this->msg = $msg;
        $this->code = $code;
        $this->succ = $succ;
    }
    public function __toString()
    {
        $result =  [
            'succ' => $this->succ,
            'data' => $this->data,
            'msg' => $this->msg,
            'code' => $this->code,

        ];
        return json_encode(
            $result
        );
    }

}