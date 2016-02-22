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

	/**
	 * 加载模型
	 */
	protected function model()
	{
		if (null === $this->model) $this->model = new \stdClass();
		$num = func_num_args();
		$var = func_get_args();
		if ($num == 1) {
			if (isset($this->model->$var[0])) return $this->model->$var[0];
			if ($model = Cellular::loadClass(Cellular::$appStruct['model'].'.'.$var[0])) {
				return $this->model->$var[0] = $model->table($var[0]);
			}
			if ($model = Cellular::loadClass('core.model')) {
				return $this->model->$var[0] = $model->table($var[0]);
			}
		} elseif($num == 2) {
			if (isset($this->model->$var[1])) return $this->model->$var[1];
			if ($model = Cellular::loadClass(Cellular::$appStruct['class'].'.'.$var[0])) {
				return $this->model->$var[1] = $model->table($var[1]);
			}
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

		if ($this->viewData) extract($this->viewData);
		$path = Cellular::$appStruct['view'].DIRECTORY_SEPARATOR.$name.'.php';
		if ($path = Cellular::getFilePath($path)) {
			ob_start(); //开启缓冲区
			include_once($path);
			$this->viewCache = ob_get_contents();
			ob_end_flush(); //关闭缓存并清空
		}
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
