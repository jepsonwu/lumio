<?php
namespace Jthrift\Services;
/**
 * Autogenerated by Thrift Compiler (0.9.3)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
use Thrift\Base\TBase;
use Thrift\Type\TType;
use Thrift\Type\TMessageType;
use Thrift\Exception\TException;
use Thrift\Exception\TProtocolException;
use Thrift\Protocol\TProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Exception\TApplicationException;


interface ApiServiceIf {
  /**
   * @param string $service_name
   * @param string $method
   * @param string $params
   * @param string $request_info
   * @return string
   */
  public function call($service_name, $method, $params, $request_info);
}

class ApiServiceClient implements \Jthrift\Services\ApiServiceIf {
  protected $input_ = null;
  protected $output_ = null;

  protected $seqid_ = 0;

  public function __construct($input, $output=null) {
    $this->input_ = $input;
    $this->output_ = $output ? $output : $input;
  }

  public function call($service_name, $method, $params, $request_info)
  {
    $this->send_call($service_name, $method, $params, $request_info);
    return $this->recv_call();
  }

  public function send_call($service_name, $method, $params, $request_info)
  {
    $args = new \Jthrift\Services\ApiService_call_args();
    $args->service_name = $service_name;
    $args->method = $method;
    $args->params = $params;
    $args->request_info = $request_info;
    $bin_accel = ($this->output_ instanceof TBinaryProtocolAccelerated) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'call', TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('call', TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_call()
  {
    $bin_accel = ($this->input_ instanceof TBinaryProtocolAccelerated) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Jthrift\Services\ApiService_call_result', $this->input_->isStrictRead());
    else
    {
      $rseqid = 0;
      $fname = null;
      $mtype = 0;

      $this->input_->readMessageBegin($fname, $mtype, $rseqid);
      if ($mtype == TMessageType::EXCEPTION) {
        $x = new TApplicationException();
        $x->read($this->input_);
        $this->input_->readMessageEnd();
        throw $x;
      }
      $result = new \Jthrift\Services\ApiService_call_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    if ($result->success !== null) {
      return $result->success;
    }
    throw new \Exception("call failed: unknown result");
  }

}

// HELPER FUNCTIONS AND STRUCTURES

class ApiService_call_args {
  static $_TSPEC;

  /**
   * @var string
   */
  public $service_name = null;
  /**
   * @var string
   */
  public $method = null;
  /**
   * @var string
   */
  public $params = "";
  /**
   * @var string
   */
  public $request_info = "";

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'service_name',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'method',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'params',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'request_info',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['service_name'])) {
        $this->service_name = $vals['service_name'];
      }
      if (isset($vals['method'])) {
        $this->method = $vals['method'];
      }
      if (isset($vals['params'])) {
        $this->params = $vals['params'];
      }
      if (isset($vals['request_info'])) {
        $this->request_info = $vals['request_info'];
      }
    }
  }

  public function getName() {
    return 'ApiService_call_args';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->service_name);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->method);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->params);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->request_info);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('ApiService_call_args');
    if ($this->service_name !== null) {
      $xfer += $output->writeFieldBegin('service_name', TType::STRING, 1);
      $xfer += $output->writeString($this->service_name);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->method !== null) {
      $xfer += $output->writeFieldBegin('method', TType::STRING, 2);
      $xfer += $output->writeString($this->method);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->params !== null) {
      $xfer += $output->writeFieldBegin('params', TType::STRING, 3);
      $xfer += $output->writeString($this->params);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->request_info !== null) {
      $xfer += $output->writeFieldBegin('request_info', TType::STRING, 4);
      $xfer += $output->writeString($this->request_info);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class ApiService_call_result {
  static $_TSPEC;

  /**
   * @var string
   */
  public $success = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        0 => array(
          'var' => 'success',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['success'])) {
        $this->success = $vals['success'];
      }
    }
  }

  public function getName() {
    return 'ApiService_call_result';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 0:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->success);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('ApiService_call_result');
    if ($this->success !== null) {
      $xfer += $output->writeFieldBegin('success', TType::STRING, 0);
      $xfer += $output->writeString($this->success);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class ApiServiceProcessor {
  protected $handler_ = null;
  public function __construct($handler) {
    $this->handler_ = $handler;
  }

  public function process($input, $output) {
    $rseqid = 0;
    $fname = null;
    $mtype = 0;

    $input->readMessageBegin($fname, $mtype, $rseqid);
    $methodname = 'process_'.$fname;
    if (!method_exists($this, $methodname)) {
      $input->skip(TType::STRUCT);
      $input->readMessageEnd();
      $x = new TApplicationException('Function '.$fname.' not implemented.', TApplicationException::UNKNOWN_METHOD);
      $output->writeMessageBegin($fname, TMessageType::EXCEPTION, $rseqid);
      $x->write($output);
      $output->writeMessageEnd();
      $output->getTransport()->flush();
      return;
    }
    $this->$methodname($rseqid, $input, $output);
    return true;
  }

  protected function process_call($seqid, $input, $output) {
    $args = new \Jthrift\Services\ApiService_call_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Jthrift\Services\ApiService_call_result();
    $result->success = $this->handler_->call($args->service_name, $args->method, $args->params, $args->request_info);
    $bin_accel = ($output instanceof TBinaryProtocolAccelerated) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'call', TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('call', TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->writeMessageEnd();
      $output->getTransport()->flush();
    }
  }
}

