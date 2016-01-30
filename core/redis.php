<?php
/**
 *
 * Cellular Framework
 * redis封装
 *
 * @author mark weixuan.1987@hotmail.com
 * @version 1.0 2015-12-9
 *
 */

namespace core;

class redis {

    private $r;
    private $host;
    private $port;
    private $db;

    public function __construct($config)
    {
        $this->host = '127.0.0.1';
        $this->port = '6379';
        $this->db = 0;
        $r = new \Redis();
    }

    public function conent()
    {
        $r->pconnect($this->host, $this->port);
    }

}
?>
