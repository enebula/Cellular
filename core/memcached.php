<?php
/**
 * Cellular Framework
 * Memcached 类
 * @copyright Cellular Team
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
