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

	private static $rewrite; //开启关闭重定向
	private static $classes; //实例化的对象
	private static $frameworkPath; //框架根目录
	private static $appRootPath; //应用程序根目录
	//应用程序结构体
	private static $appStruct = array(
		'controller' => 'controller',
		'model' => 'model',
		'view' => 'view'
	); 

	public function __construct()
	{
		if (!isset($_SERVER['DOCUMENT_ROOT'])) die('DOCUMENT_ROOT error!');
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
		$className = strtolower(strtr($className, '\\', DIRECTORY_SEPARATOR));
		//搜索应用程序目录
		$path = self::$appRootPath.DIRECTORY_SEPARATOR.$className.'.php';
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
	 * 设置应用程序路径
	 * @param array $path 应用程序参数
	 * @return void
	 */

	public static function setAppRootPath($path)
	{
		self::$appRootPath = $path;
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
	 * 获取应用程序结构属性
	 * @param string $name 属性名
	 * @return string | array
	 */

	public static function getAppStruct($name = null)
	{
		if (null !== $name) {
			return isset(self::$appStruct[$name]) ? self::$appStruct[$name] : null;
		}
		return self::$appStruct;
	}

	/**
	 * 执行应用程序
	 */

	public static function application()
	{
		self::$frameworkPath = dirname(__FILE__);
		self::hub();
	}

	/**
	 * 加载文件
	 */

	public static function loadFile($fileName, $return = false)
	{
		//检查文件名是否安全-防注入
		if (!preg_match("/^[A-Za-z0-9_.]+$/", $fileName)) die('File name error!');
		//解析文件路径
		$file = strtr($fileName, '.', DIRECTORY_SEPARATOR);
		$path = self::$appRootPath.DIRECTORY_SEPARATOR.$file.'.php';
		if (false === $return) include_once($path);
		else return $path;
	}

	/**
	 * 实例化类
	 */

	public static function loadClass($className)
	{
		//检查类名是否安全-防注入
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
			//获取入口相对web服务根目录路径
			$webRootPath = substr($_SERVER['SCRIPT_NAME'], 0, strripos($_SERVER['SCRIPT_NAME'], '/'));
			if (!empty($webRootPath)) {
				$requestURI = preg_replace("/^\\".$webRootPath."/", '', $requestURI); //过滤应用相当路径
			}
			$request = explode('/', $requestURI);
			$request = array_filter($request);
			if (!empty($request)) {
				//获取控制器
				$controller = '';
				$controllerDir = self::$appRootPath.DIRECTORY_SEPARATOR.self::$appStruct['controller'];
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
		//检查动作名是否安全-防注入
		if (!preg_match("/^[A-Za-z0-9_]+$/", $action)) die('action name error!');
		//加载控制器执行动作
		//echo $controller.'->'.$action.'<br/>';
		$class = self::loadClass(self::$appStruct['controller'].'.'.$controller);
		if(method_exists($class, $action)) {
			$class->$action();
		}
	}

}

spl_autoload_register(array('Cellular', 'autoload'));

?>