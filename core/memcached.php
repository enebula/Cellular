<?php
/**
 *
 * Cellular Framework
 * Memcached 封装
 *
 * @author Cloud 66999882@qq.com
 * @version 1.0 2016-02-03
 *
 */
namespace core;

class Memcached {

    public function __construct($config) {
        $this->connect();
    }

    /**
     * 连接数据库
     */
    private function connect() {
      $config = Cellular::loadFile('config/memcached.php');
      $mc = new Memcached();
      $mc->addServer($config['host'], $config['port']);
  		return $mc;
    }

}
?>
