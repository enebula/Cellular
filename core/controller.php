<?php
/**
 * Cellular Framework
 * 控制器类
 * @copyright Cellular Team
 */

namespace core;
use Cellular;


class Controller
{
	protected $class;
	protected $ext;
	protected $model;
	private $viewData;
	private $viewCache;

	function __construct()
	{
		$this->model = new \stdClass();
		$this->class = new \stdClass();
		$this->ext = new \stdClass();
	}

	/**
	 * 加载模型
	 */
	protected function model($name)
	{
        if (isset($this->model->$name)) return $this->model->$name;
		if ($model = Cellular::loadModel($name)) {
			$this->model->$name = $model;
			return $model;
		}
		return false;
	}

	/**
	 * 加载实例类
	 */
	protected function loadClass($className, $param = null)
	{
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
	protected function loadExt($extName)
	{
		$name = strtr(strtolower($extName), '.', '_');
		if (!isset($this->ext->$name)) {
			$this->ext->$name = Cellular::loadExt($extName);
		}
		return $this->ext->$name;
	}

	/**
	 * 载入配置信息
	 */
	protected function config($name)
	{
		return Cellular::config($name);
	}

	/**
	 * 视图赋值
	 */
	protected function assign($name, $value)
	{
		$this->viewData[$name] = $value;
	}

	/**
	 * 渲染视图
	 */
	protected function display($name)
	{
		Cellular::view($name, $this->viewData, $this->viewCache);
	}

	/**
	 * 输出页面缓存
	 */
	protected function getCache()
	{
		return $this->viewCache;
	}

	/**
	 * 错误请求提示页面
	 */
	protected function error($code, $msg)
	{
		$codes = array('404', '400');
		if (in_array($code, $codes)) {
			Cellular::error($code, $msg);
		} else {
			die('Error header code!');
		}
	}
}
?>
