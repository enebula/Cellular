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

 namespace base;

 class init {

    protected $class;

    /**
    * 加载实例类
    */
    protected function loadClass($className)
    {
        if (null === $this->class) $this->class = new \stdClass();
        $name = strtr($className, '.', '_');
        if (!isset($this->class->$name)) {
            $this->class->$name = \Cellular::loadClass($className);
        }
        return $this->class->$name;
    }

 }

?>
