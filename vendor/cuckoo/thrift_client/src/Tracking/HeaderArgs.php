<?php

namespace Jiuyan\Cuckoo\ThriftClient\Tracking;

use Thrift\Base\TBase;
use Thrift\Type\TType;

class HeaderArgs extends TBase
{
  static $_TSPEC;

  public $header = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'header',
          'type' => TType::STRUCT,
          'class' => RequestHeader::class,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['header'])) {
        $this->header = $vals['header'];
      }
    }
  }

  public function getName() {
    return 'HeaderService_header_args';
  }

  public function read($input)
  {
    return $this->_read("HeaderService_header_args", self::$_TSPEC, $input);
  }

  public function write($output)
  {
    return $this->_write("HeaderService_header_args", self::$_TSPEC, $output);
  }

}


