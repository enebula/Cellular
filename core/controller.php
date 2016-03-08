<?php
/**
 *
 * Cellular Framework
 * 控制器基础类
 *
 * @author mark weixuan.1987@hotmail.com
 * @version 1.0 2015-12-9
 *
 */

namespace core;
use Cellular;

class Controller extends Base {
	protected $model;
	protected $input;
	protected $http;
	private $viewData;
	private $viewCache;

	function __construct()
	{
		$this->model = new \stdClass();
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
		/*
		if ($this->viewData) extract($this->viewData);
		$struct = Cellular::appStruct();
		$path = $struct['view'] . DIRECTORY_SEPARATOR . $name . '.php';
		if ($path = Cellular::getFilePath($path)) {
			ob_start(); //开启缓冲区
			include($path);
			$this->viewCache = ob_get_contents();
			ob_end_flush(); //关闭缓存并清空
		}
		*/
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
