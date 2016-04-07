<?php
/**
 * Cellular Faremwork
 * Memcached 驱动
 * @copyright Cellular Team
 */

namespace core;

class Memcached extends Base
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
        $config = $this->config('memcached');
        $mc = new Memcached();
        $mc->addServer($config['host'], $config['port']);
        return $mc;
    }
}
?>
