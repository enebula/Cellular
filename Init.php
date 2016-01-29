<?php
/**
 *
 * Cellular Framework
 *
 * @author mark weixuan.1987@hotmail.com
 * @version 1.0 2015-12-9
 *
 */

class Cellular {

	private static $frameworkPath; //框架根目录
	private static $appPath; //应用程序根目录
	private static $appName; //应用程序名称
	private static $errorMsg;

	//开启关闭重定向
	private static $rewrite = true;
	//时区
	private static $timezone = 'Asia/Shanghai';
	//实例化的对象
	private static $classes = array();
	//应用程序结构体
	private static $appStruct = array(
		'controller' => 'controller',
		'model' => 'model',
		'view' => 'view'
	);

	public function setTimezone($identifier)
	{
		self::$timezone = $identifier;
	}

	/**
	 * 调试信息
	 * @param string $environment 环境状态
	 * @return void
	 */
	public static function debug($environment)
	{
			switch ($environment)
			{
				//开发环境
				case 'development':
					ini_set("display_errors",'on');
					error_reporting(E_ALL);
					break;
				//测试环境
				case 'testing':
					break;
				//生产环境
				case 'production':
					ini_set('display_errors', 'off');
					error_reporting(0);
					break;
			}
	}

	/**
	 * 自动加载类文件路径
	 * @param string $className 类名称 lib.loader
	 * @return boolean true|false
	 */
	public static function autoload($className)
	{
		$className = mb_strtolower(strtr($className, '\\', DIRECTORY_SEPARATOR));
		//搜索应用程序目录
		$path = self::$appPath.DIRECTORY_SEPARATOR.self::$appName.DIRECTORY_SEPARATOR.$className.'.php';
		if (is_file($path)) {
			include_once($path);
			return true;
		}
		//搜索Cellular目录－包含命名空间
		$path = self::$frameworkPath.DIRECTORY_SEPARATOR.$className.'.php';
		if (is_file($path)) {
			include_once($path);
			return true;
		}
		return false;
	}

	/**
	 * 开启关闭URL重写
	 * @param boolean true|false
	 * @return void
	 */
	public static function setRewrite($status)
	{
		//if (is_bool($status)) self::$rewrite = $status;
	}

	/**
	 * 框架主入口 执行应用程序
	 */
	public static function application($path, $name = null)
	{
		self::$appPath = $path;
		self::$appName = $name;
		self::$frameworkPath = dirname(__FILE__);
		date_default_timezone_set(self::$timezone); //设置默认时区
		if (false === self::hub()) {
			self::error(self::$errorMsg['code'], self::$errorMsg['msg']);
		}
	}

	/**
	 * 加载文件
	 */
	public static function loadFile($file)
	{
		//检查文件名是否安全-防注入
		if (preg_match("/^[A-Za-z0-9_\/.]+$/", $file)) {
			//解析文件路径
			$path = self::$appPath.DIRECTORY_SEPARATOR.self::$appName.DIRECTORY_SEPARATOR.$file;
			if (is_file($path)) {
				include_once($path);
				return true;
			}
			//搜索Cellular目录－包含命名空间
			$path = self::$frameworkPath.DIRECTORY_SEPARATOR.$file;
			if (is_file($path)) {
				include_once($path);
				return true;
			}
		}
		return false;
	}

	/**
	 * 装载类
	 */
	public static function loadClass($className, $param = null)
	{
		//检查类名是否安全-防注入
		if (preg_match("/^[A-Za-z0-9_.]+$/", $className)) {
			//检查是否已实例化
			if (isset(self::$classes[$className])) return self::$classes[$className];
			//实例化类
			$class = '\\'.strtr($className, '.', '\\'); //解析类名
			if (class_exists($class)) {
				return self::$classes[$className] = new $class($param);
			}
			//die('class "'.$class.'" does not exist');
		}
		return false;
	}

	/**
	 * 卸载类
	 */
	public static function remvoeClass($className)
	{
		//检查类名是否安全-防注入
		if (!preg_match("/^[A-Za-z0-9_.]+$/", $className)) {
			if (isset(self::$classes[$className])) {
				unset(self::$classes[$className]);
				return true;
			}
		}
		return false;
	}

	/**
	 * 控制器转发
	 */
	private static function hub()
	{
		$controller = 'index';
		$action = 'main';
		//解析控制器与动作参数
		if (true === self::$rewrite) {
			//获取应用名
			if (null == self::$appName) {
				self::$appName = substr($_SERVER['SCRIPT_NAME'], 1, strripos($_SERVER['SCRIPT_NAME'], '/')-1);
			}
			//获取请求资源ID
			$requestURI = isset($_GET['uri']) ? $_GET['uri'] : (isset($_SERVER['REQUEST_URI']) ? str_replace('/'.self::$appName.'/', '', $_SERVER['REQUEST_URI']) : '');
			//过滤子目录
			$requestURI = str_replace(substr($_SERVER['SCRIPT_NAME'], 0, strripos($_SERVER['SCRIPT_NAME'], '/')), '', $requestURI);
			//执行请求资源
			if (!preg_match("/^[A-Za-z0-9_.\/%&#@]+$/", $requestURI) && $requestURI != '') {
				self::$errorMsg = array(
					'code' => '400',
					'msg' => 'URI not allowed!'
				);
				return false;
			} else {
				//暂时不支持路由器功能
				$removeParamURI = substr($requestURI, 0, strpos($requestURI, '?')); //过滤参数
				$requestURI = isset($removeParamURI{0}) ? $removeParamURI : $requestURI;
				$request = explode('/', $requestURI);
				$request = array_filter($request);
				if (!empty($request)) {
					//获取控制器
					$controller = '';
					$controllerDir = self::$appPath.DIRECTORY_SEPARATOR.self::$appName.DIRECTORY_SEPARATOR.self::$appStruct['controller'];
					foreach ($request as $key => $value) {
						$controller .= DIRECTORY_SEPARATOR.$value;
						unset($request[$key]);
						if (!is_dir($controllerDir.$controller)) break;
					}
					$controller = strtr(substr($controller, 1), DIRECTORY_SEPARATOR, '.');
					//获取动作
					if ($request) {
						$action = array_shift($request);
					}
				}
			}
		} else {
			//暂时不开启此功能,需要增加安全检查
			if (isset($_GET['c'])) {
				$controller = $_GET['c'];
				unset($_GET['c']);
			}
			if (isset($_GET['a'])) {
				$action = $_GET['a'];
				unset($_GET['a']);
			}
		}
		//检查动作名是否安全-防注入
		if (!preg_match("/^[A-Za-z0-9_]+$/", $controller)) {
			self::$errorMsg = array(
				'code' => '400',
				'msg' => 'controller error!'
			);
			return false;
		}
		if (!preg_match("/^[A-Za-z0-9_]+$/", $action)) {
			self::$errorMsg = array(
				'code' => '400',
				'msg' => 'action error!'
			);
			return false;
		}
		//加载控制器执行动作
		$class = self::loadClass(self::$appStruct['controller'].'.'.$controller);
		if (false !== $class) {
			if(method_exists($class, $action)) {
				$class->$action();
				return true;
			}
		}
		self::$errorMsg = array(
			'code' => '404',
			'msg' => 'page not !'
		);
		return false;
	}

	private static function error($code, $msg = null)
	{
		switch ($code) {
			case '400':
				self::loadFile('error/400.html');
				break;
			case '404':
				self::loadFile('error/404.html');
				break;
		}
	}

}

spl_autoload_register(array('Cellular', 'autoload'));

?>
