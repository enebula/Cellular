<?php
/**
 * Cellular Framework
 * Memcached 驱动
 * @copyright Cellular Team
 */

namespace core;
use Cellular;

class Memcached
{
    public function __construct($config)
    {
        $this->connect();
    }

    /**
     * 连接数据库
     */
    private function connect()
    {
        $config = Cellular::config('memcached');
        $mc = new Memcached();
        $mc->addServer($config['host'], $config['port']);
        return $mc;
    }
}
?>
