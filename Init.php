<?php
/**
 *
 * Cellular Framework
 *
 * @author mark weixuan.1987@hotmail.com
 * @version 1.0 2015-12-1
 *
 */

class Cellular {

	private static $frameworkPath; //框架根路径
	private static $applicationPath; //应用程序路径
	private static $rewrite; //开启关闭重定向
	private static $classes; //实例化的对象

	public function __construct()
	{
		self::$frameworkPath = dirname(__FILE__).DIRECTORY_SEPARATOR;
		self::$applicationPath = null;
		self::$rewrite = true;
		self::$classes = array(); //默认为空的关系数组
	}

	/**
	 * 自动加载类文件路径
	 * @param string $className 类名称 lib.loader
	 * @return boolean true|false
	 */

	public static function autoload($className)
	{
		$path = self::$frameworkPath . strtolower(strtr($className, '\\', DIRECTORY_SEPARATOR)) . '.php';
		if (!is_file($path))
		{
			$path = self::$applicationPath . strtolower(strtr($className, '\\', DIRECTORY_SEPARATOR)) . '.php';
			if (!is_file($path)) {
				die ('File "'.$path.'"does not exist!');
				return false;
			}
		}
		//echo $path.'<br/>';
		include_once($path);
		return true;
	}

	/**
	 * 设置应用属性
	 * @param string $path 应用程序路径
	 * @return void
	 */

	public static function setApp($path)
	{
		self::$applicationPath = $path;
	}

	/**
	 * 开启关闭URL重写
	 * @param boolean true|false
	 * @return void
	 */

	public static function setRewrite($status)
	{
		if (is_bool($status)) self::$rewrite = $status;
	}

	/**
	 * 执行应用程序
	 */

	public static function application()
	{
		if (null !== self::$applicationPath) {
			self::hub();
		} else {
			die('application path not null');
		}
	}

	/**
	 * 实例化类
	 */

	public static function loadClass($className)
	{
		//检查类名是否安全
		if (!preg_match("/^[A-Za-z0-9_.]+$/", $className)) die('class name error!');
		//检查是否已实例化
		if (isset(self::$classes[$className])) return self::$classes[$className];
		//实例化类
		$class = strtr($className, '.', '\\'); //解析类名
		if (class_exists($class)) {
			$fun = create_function(null, 'return new ' .$class. ';');
			self::$classes[$className] = $fun();
			return self::$classes[$className];
		} else {
			die('class "'.$class.'" does not exist');
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
		//解析请求
		if (false !== self::$rewrite) {
			//获取请求资源ID
			$requestURI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : false; //暂时只支持apache服务器
			//暂时不支持路由器功能
			$removeParamURI = substr($requestURI, 0, strpos($requestURI, '?')); //过滤参数
			$requestURI = isset($removeParamURI{0}) ? $removeParamURI : $requestURI;
			$request = explode('/', $requestURI);
			$request = array_filter($request);
			//获取控制器
			$controller = 'controller';
			foreach ($request as $key => $value)
			{
				$controller .= DIRECTORY_SEPARATOR.$value;
				unset($request[$key]);
				if (!is_dir(self::$applicationPath.DIRECTORY_SEPARATOR.$controller)) break;
			}
			$controller = strtr($controller, '/', '.');
			//获取动作
			if ($request)
			{
				$action = array_shift($request);
			}
			//echo $controller.'->'.$action;
			exit();
		} else {
			if (isset($_GET['c'])) {
				$controller = $_GET['c'];
				unset($_GET['c']);
			}
			if (isset($_GET['a'])) {
				$action = $_GET['a'];
				unset($_GET['a']);
			}
		}
		//检查动作名是否安全
		if (!preg_match("/^[A-Za-z0-9_]+$/", $action)) die('action name error!');
		//加载控制器执行动作
		$class = self::loadClass('controller.'.$controller);
		if(method_exists($class, $action)) {
			$class->$action();
		}
	}

}

spl_autoload_register(array('Cellular', 'autoload'));

?>