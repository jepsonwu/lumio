<?php

namespace Jiuyan\Cuckoo\ThriftClient\Tracking;

use Thrift\Base\TBase;
use Thrift\Type\TType;

class RequestHeader extends TBase {
  static $_TSPEC;

  public $request_id = null;
  public $seq_id = null;
  public $app_id = null;
  public $meta = null;

  public function __construct($vals=null)
  {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'trace_id',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'span_id',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'parent_span_id',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'sampled',
          'type' => TType::BOOL,
          ),
        5 => array(
          'var' => 'seq_id',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'flags',
          'type' => TType::I64,
          ),
        7 => array(
          'var' => 'meta',
          'type' => TType::MAP,
          'ktype' => TType::STRING,
          'vtype' => TType::STRING,
          'key' => array(
            'type' => TType::STRING,
          ),
          'val' => array(
            'type' => TType::STRING,
            ),
          ),
        );
    }
    if (is_array($vals)) {
        parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName()
  {
    return 'RequestHeader';
  }

  public function read($input)
  {
      return $this->_read("RequestHeader", self::$_TSPEC, $input);
  }


  public function write($input)
  {
      return $this->_write("RequestHeader", self::$_TSPEC, $input);
  }

}
