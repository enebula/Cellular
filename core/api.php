<?php
/**
 * Cellular Framework
 * Memcached Class
 * @copyright Cellular Team
 */

namespace core;
use Cellular;

class API
{
    protected $class;

    /**
     * 加载实例类
     */
    protected function loadClass($className, $param = null)
    {
        if (null === $this->class) $this->class = new \stdClass();
        $name = strtr(strtolower(str_replace('core.', '', $className)), '.', '_');
        if (!isset($this->class->$name)) {
            $this->class->$name = Cellular::loadClass($className, $param);
        }
        return $this->class->$name;
    }

    /**
     * 载入配置信息
     */
    protected function config($name)
    {
        return Cellular::config($name);
    }
}

?>
