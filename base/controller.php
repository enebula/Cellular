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

namespace base;

class Controller extends Init {

	protected $model;
	protected $input;
	protected $http;
	private $viewData;
	private $viewCache;

	public function __construct()
	{
		$this->input = $this->loadClass('base.Input');
		$this->http = $this->loadClass('base.Http');
	}

	/**
	 * 加载模型
	 */
	protected function model($name)
	{
		if (null === $this->model) $this->model = new \stdClass();
		if (!isset($this->model->$name)) {
			$class = \Cellular::getAppStruct('model'). '.' .$name;
			$this->model->$name = \Cellular::loadClass($class);
		}
	}

	/**
	 * 视图赋值
	 */
	protected function assign($variable, $value)
	{
		$this->viewData[$variable] = $value;
	}

	/**
	 * 渲染视图
	 */
	protected function display($name)
	{
		if ($this->viewData) extract($this->viewData);
		$file = \Cellular::getAppStruct('view'). '.' .$name;
		$path = \Cellular::loadFile($file, true);
		ob_start(); //开启缓冲区
		include_once($path);
		$this->viewCache = ob_get_contents();
		ob_end_flush(); //关闭缓存并清空
	}

	/**
	 * 输出页面缓存
	 */
	protected function getCache()
	{
		return $this->viewCache;
	}

}

?>
