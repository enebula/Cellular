<?php
/**
 *
 * Cellular Framework
 * 基本类
 *
 * @author mark weixuan.1987@hotmail.com
 * @version 1.0 2015-12-9
 *
 */

namespace core;
use Cellular;

class Base {

    protected $class;

    /**
    * 加载实例类
    */
    protected function loadClass($className, $param = null)
    {
        if (null === $this->class) $this->class = new \stdClass();
        $name = strtr(strtolower($className), '.', '_');
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
