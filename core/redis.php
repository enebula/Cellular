<?php
/**
 * Cellular Framework
 * Redis 驱动器
 * @copyright Cellular Team
 */

namespace core;

class Redis extends Base
{
    private $redis;
    private $config;
    private $db;

    public function __construct()
    {
        $this->config = $this->config('redis');
        $this->db = 0;
        $this->redis = new Redis();
        $this->redis->connect($this->config['host'], $this->config['port']);
    }

    /**
     * 保存字符串类型
     */
    public function save($key, $value, $time = null)
    {
        if (null == $time) {
            return $this->redis->set($key, $value);
        } else {
            return $this->redis->setex($key, $time, $value);
        }
    }

    /**
     * 删除字符串类型
     */
    public function remove($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * 查询字符串类型
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /**
     * 返回当前的库编号
     */
    public function getDB()
    {
        return $this->db;
    }

    /**
     * 返回当前的库编号
     */
    public function setDB($id)
    {
        $this->redis->select($id);
    }

    /**
     * 清除库
     */
    public function clear($db = null)
    {
        if (is_null($db)) {
            $this->redis->flushdb();
        } else {
            $this->redis->select($db);
            $this->redis->flushdb();
            $this->redis->select($this->db);
        }
    }

    /**
     * 清除库
     */
    public function clearAll()
    {
        $this->redis->flushall();
    }
}
?>
