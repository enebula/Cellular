<?php
/**
 * Cellular Framework
 * 控制器类
 * @copyright Cellular Team
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
