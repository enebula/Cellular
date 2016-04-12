<?php
/**
 * Cellular Framework
 * 基础类
 * @copyright Cellular Team
 */

namespace core;
use Cellular;


class Base {
    protected $class;
    protected $ext;

    /**
    * 加载实例类
    */
    public function loadClass($className, $param = null)
    {
        if (null === $this->class) $this->class = new \stdClass();
        $name = strtr(strtolower(str_replace('core.', '', $className)), '.', '_');
        if (!isset($this->class->$name)) {
            $this->class->$name = Cellular::loadClass($className, $param);
        }
        return $this->class->$name;
    }

    /**
     * 加载扩展组件功能的封装
     * @param $extName 组件包名
     * @return mixed
     */
    public function loadExt($extName)
    {
        if (null === $this->ext) $this->ext = new \stdClass();
        $name = strtr(strtolower(str_replace('core.', '', $extName)), '.', '_');
        if (!isset($this->ext->$name)) {
            $this->ext->$name = Cellular::loadExt($extName);
        }
        return $this->ext->$name;
    }

    /**
	 * 载入配置信息
	 */
    public function config($name)
	{
		return Cellular::config($name);
	}
}

?>
