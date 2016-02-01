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
	private static $config; //应用配置文件
	private static $URI; //URI请求资源
	private static $errorMsg; //web访问错误信息

	//开启关闭重定向
	private static $rewrite = true;
	//时区
	private static $timezone = 'Asia/Shanghai';
	//实例化的对象
	private static $classes = array();
	//应用程序结构体
	public static $appStruct = array(
		'controller' => 'controller',
		'model' => 'model',
		'view' => 'view'
	);

	/**
	 * 框架主入口 执行应用程序
	 */
	public static function application($path, $name = null)
	{
		self::$appPath = $path;
		self::$appName = $name;
		self::$frameworkPath = dirname(__FILE__);
		date_default_timezone_set(self::$timezone); //设置默认时区
		//获取uri
		if (false === self::getURI()) {
			self::error(self::$errorMsg['code'], self::$errorMsg['msg']);
		}
		//加载应用配置文件
		$config = self::loadFile('config/app.php');
		//定义常量
		define('ASSETS', isset($config['assets_path']) ? $config['assets_path'] : '');
		//启动转发器
		if (false === self::hub()) {
			self::error(self::$errorMsg['code'], self::$errorMsg['msg']);
		}
	}

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
		$className = mb_strtolower(strtr($className, '\\', DIRECTORY_SEPARATOR)).'.php';
		return self::loadFile($className);
	}

	/**
	 * 获取文件路径
	 */
	public static function getFilePath($file)
	{
		//检查文件名是否安全-防注入
		if (preg_match("/^[A-Za-z0-9_\/.]+$/", $file)) {
			//解析文件路径
			$path = self::$appPath.DIRECTORY_SEPARATOR.self::$appName.DIRECTORY_SEPARATOR.$file;
			if (is_file($path)) {
				return $path;
			}
			//搜索Cellular目录－包含命名空间
			$path = self::$frameworkPath.DIRECTORY_SEPARATOR.$file;
			if (is_file($path)) {
				return $path;
			}
		}
		return false;
	}

	/**
	 * 读取文件
	 */
	public static function getFile($file)
	{
		if ($path = self::getFilePath($file)) {
			return file_get_contents($path);
		}
		return false;
	}

	/**
	 * 加载文件
	 */
	public static function loadFile($file)
	{
		if ($path = self::getFilePath($file)) {
			return include_once($path);
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

	private static function getURI()
	{
		//获取请求资源ID
		$requestURI = isset($_GET['uri']) ? $_GET['uri'] : (isset($_SERVER['REQUEST_URI']) ? str_replace('/'.self::$appName.'/', '', $_SERVER['REQUEST_URI']) : '');
		//过滤脚本目录
		$requestURI = str_replace(substr($_SERVER['SCRIPT_NAME'], 0, strripos($_SERVER['SCRIPT_NAME'], '/')), '', $requestURI);
		//请求资源检查
		if (!preg_match("/^[A-Za-z0-9_.\/%&#@]+$/", $requestURI) && !empty($requestURI)) {
			self::$errorMsg = array(
				'code' => '400',
				'msg' => 'URI not allowed!'
			);
			return false;
		}
		//通过脚本路径获取应用名
		if (null == self::$appName) {
			self::$appName = substr($_SERVER['SCRIPT_NAME'], 0, strripos($_SERVER['SCRIPT_NAME'], '/'));
		}
		if ($requestURI != '') {
			$removeParamURI = substr($requestURI, 0, strpos($requestURI, '?')); //过滤参数
			$requestURI = isset($removeParamURI{0}) ? $removeParamURI : $requestURI;
			$request = explode('/', $requestURI);
			$request = array_filter($request);
			//通过URI获取应用名
			if (!self::$appName) {
				$_var = self::$appPath;
				foreach ($request as $key => $value) {
					$_var .= DIRECTORY_SEPARATOR.$value;
					if (!is_dir($_var)) break;
					self::$appName .= DIRECTORY_SEPARATOR.$value;
					unset($request[$key]);
				}
				self::$appName = substr(self::$appName, 1);
			}
			self::$URI = $request;
		}
		return true;
	}

	/**
	 * 控制器转发
	 */
	private static function hub()
	{
		$controller = 'Index';
		$action = 'main';
		//解析控制器与动作参数
		if (self::$URI) {
			//获取控制器
			$request = self::$URI;
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
		//检查动作名是否安全-防注入
		if (!preg_match("/^[A-Za-z0-9_]+$/", $controller)) {
			self::$errorMsg = array(
				'code' => '400',
				'msg' => 'Controller not allowed!'
			);
			return false;
		}
		if (!preg_match("/^[A-Za-z0-9_]+$/", $action)) {
			self::$errorMsg = array(
				'code' => '400',
				'msg' => 'Action not allowed!'
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
			'msg' => 'Page not Found!'
		);
		return false;
	}

	private static function error($code, $msg = null)
	{
		$request = self::getFile('error/400.html');
		$var = array('<header></header>','<p></p>');
		switch ($code) {
			case '400':
				$value = array('<header>400</header>','<p>'.$msg.'</p>');
				$request = str_replace($var, $value, $request);
				break;
			case '404':
				$value = array('<header>404</header>','<p>'.$msg.'</p>');
				$request = str_replace($var, $value, $request);
				break;
		}
		die($request);
	}

}

spl_autoload_register(array('Cellular', 'autoload'));

?>
