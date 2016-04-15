<?php
/**
 *
 * Cellular Framework
 *
 * @author mark weixuan.1987@hotmail.com
 * @version 1.0 2015-12-9
 *
 */

class Cellular
{
	private static $config; //应用配置文件
	private static $frameworkPath; //框架根目录
	private static $webRootPath; //web根目录
	private static $appPath; //应用程序根目录
	private static $assetsPath; //静态资源目录
	private static $classes = array(); //实例化的对象
	private static $models = array(); //实例化数据库模型

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
	 * 设置调试环境
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
	 * 框架主入口 执行应用程序
	 */
	public static function application($path = null)
	{
		self::$frameworkPath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		self::$appPath = ($path == null) ? './' : $path . DIRECTORY_SEPARATOR;

		//加载配置参数
		self::$config = self::config('app');

		//设置默认时区
		if (isset(self::$config['timezone'])) date_default_timezone_set(self::$config['timezone']);

		//获取uri
		$uri = self::URI(); $ttt = $uri;
		if ($uri === false) self::error('400', 'URI not allowed!');

		//解析uri
		if ($uri) $uri = self::parseURI($uri);

		//定义静态资源路径
		$assets = self::$assetsPath . DIRECTORY_SEPARATOR . self::$config['struct']['assets'];
		define('ASSETS', self::$config['assets'] ? self::$config['assets'] : $assets);
		define('FILE', self::$config['file'] ? self::$config['file'] : $assets);
		define('APP_ROOT', self::$appPath ? self::$appPath : '');

		//定义试图目录
		define('VIEWROOT', self::$appPath . self::$config['struct']['view'] . DIRECTORY_SEPARATOR);

		//控制器转发
		$result = self::hub($uri, self::$config['struct']['controller'], self::$config['controller'], self::$config['action']);
		if (!$result) self::error('404', 'Page not Found!');
	}

	private static function URI()
	{
		//获取请求资源ID
		$uri = isset($_GET['uri']) ? $_GET['uri'] : (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
		unset($_GET['uri']);
		//获取web根目录，当应用入口不在web根目录时有效
		if (isset($_SERVER['DOCUMENT_URI'])) {
			self::$webRootPath = substr($_SERVER['DOCUMENT_URI'], 0, strrpos($_SERVER['DOCUMENT_URI'], '/'));
		} else {
			self::$webRootPath = null;
		}
		if (!empty(self::$webRootPath)) {
			$uri = str_replace(self::$webRootPath, '', $uri); //过滤脚本目录
			self::$assetsPath = self::$webRootPath;
		}
		//请求资源检查
		if (!preg_match("/^[A-Za-z0-9_\\-\\/.%&#@?=]+$/", $uri) && !empty($uri)) {
			return false;
		}
		return $uri;
	}

	private static function parseURI($uri)
	{
		//过滤GET参数
		$removeParamURI = substr($uri, 0, strpos($uri, '?'));
		$uri = isset($removeParamURI{0}) ? $removeParamURI : $uri;
		//字符串转数组
		$request = explode('/', $uri);
		//去除空数组
		$request = array_filter($request);
		//获取应用名，当一个入口下有多个应用时有效
		foreach ($request as $key => $value) {
			if (!is_dir(self::$appPath.$value)) break;
			self::$appPath .= $value.DIRECTORY_SEPARATOR;
			self::$webRootPath .= DIRECTORY_SEPARATOR.$value;
			self::$assetsPath .= DIRECTORY_SEPARATOR.$value;
			unset($request[$key]);
		}
		return $request;
	}

	/**
	 * 控制器转发
	 */
	private static function hub($uri, $dir, $controller, $action)
	{
		//解析控制器与动作参数
		if (!empty($uri) && is_array($uri)) {
			//获取控制器
			$controller = '';
			$controllerDir = self::$appPath . $dir;
			foreach ($uri as $key => $value) {
				$controller .= DIRECTORY_SEPARATOR . $value;
				unset($uri[$key]);
				if (!is_dir($controllerDir . $controller)) break;
			}
			$controller = strtr(substr($controller, 1), DIRECTORY_SEPARATOR, '.');
			if ($uri) {
				//获取动作
				$action = array_shift($uri);
				//基本路由参数格式的解析
				if ($uri) {
					while (count($uri) > 0) {
						$key = array_shift($uri);
						$value = array_shift($uri);
						$_GET[$key] = $value;
					}
				}
			}
			//检查动作名是否安全-防注入
			if (!preg_match("/^[A-Za-z0-9_.]+$/", $controller)) {
				return false;
			}
			if (!preg_match("/^[A-Za-z0-9_]+$/", $action)) {
				return false;
			}
		}
		//加载控制器执行动作
		$class = self::loadClass($dir . '.' . $controller);
		if (false !== $class) {
			if(method_exists($class, $action)) {
				$class->$action();
				return true;
			}
		}
		return false;
	}

	/**
	 * 返回路由格式的URL访问地址
	 */
	public static function getURL($controller = null, $action = null, $param = null)
	{
		$controller = strtr($controller, '.', '/');
		$url = self::$webRootPath;

		if ($controller) {
			$url .= DIRECTORY_SEPARATOR . $controller;
		} else {
			if ($action || $param) $url .= DIRECTORY_SEPARATOR . self::$config['controller'];
		}

		if ($action) {
			$url .= DIRECTORY_SEPARATOR .$action;
		} else {
			if ($param) $url .= DIRECTORY_SEPARATOR . self::$config['action'];
		}

        if (!empty($param)) {
            $url .= DIRECTORY_SEPARATOR;
            if (is_array($param)) {
                $_var = null;
                foreach ($param as $key => $value) {
                    if (is_numeric($value) || !empty($value)) $_var .= '/'.$key.'/'.$value;
                }
                $url .= substr($_var, 1);
            } else {
                $url .= $param;
            }

        }
        return $url;
	}

	/**
	 * 请求错误提示
	 */
	public static function error($code, $msg = null)
	{
		$request = self::getFile('error/400.html');
		$var = array('<header name="header"></header>','<p name="msg"></p>');
		switch ($code) {
			case '400':
				$value = array('<header name="header">400</header>','<p name="msg">'.$msg.'</p>');
				$request = str_replace($var, $value, $request);
				break;
			case '404':
				$value = array('<header name="header">404</header>','<p name="msg">'.$msg.'</p>');
				$request = str_replace($var, $value, $request);
				break;
		}
		die($request);
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
	 * 加载配置
	 */
	public static function config($name)
	{
		static $_config = array();
		$path = 'config' . DIRECTORY_SEPARATOR . $name;
		if (!isset($_config[$path])) {
			$_config[$path] = self::loadFile($path.'.php');
		}
		return $_config[$path];
	}

	/**
	 * 获取文件路径
	 */
	public static function getFilePath($file)
	{
		//检查文件名是否安全-防注入
		if (preg_match("/^[A-Za-z0-9_\\-\\/.]+$/", strtr($file, DIRECTORY_SEPARATOR, '/'))) {
			//解析文件路径
			$path = self::$appPath.$file;
			if (is_file($path)) return $path;
			//搜索Cellular目录－包含命名空间
			$path = self::$frameworkPath . $file;
			if (is_file($path)) return $path;
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
			//解析类名
			if (strpos($className, '.')) {
				$class = '\\'.strtr($className, '.', '\\');
			} else {
				$class = 'core' . '\\' . $className;
			}
			//实例化类
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

	/**
	 * 装载数据模型
	 * @param $name
	 * @param string $model
	 * @return bool
	 */
	public static function loadModel($name, $model = 'core.model')
	{
		//检查是否已实例化
		if (isset(self::$models[$name])) return self::$models[$name];
		$class = '\\' . self::$config['struct']['model'] . '\\' . $name; //解析类名
		if (class_exists($class)) {
			return self::$models[$name] = new $class($name);
		}
		$class = '\\' . strtr($model, '.', '\\'); //解析类名
		if (class_exists($class)) {
			return self::$models[$name] = new $class($name);
		}
		return false;
	}

	/**
	 * 卸载数据模型
	 * @param $name
	 */
	public static function removeModel($name)
	{
		if (isset(self::$models[$name])) unset(self::$models[$name]);
	}

	public static function view($name, $value, &$cache)
	{
        self::helper('view'); //向视图中注入辅助函数
		if ($value) extract($value); //向视图中注入变量参数
        //载入视图文件
		$path = self::$config['struct']['view'] . DIRECTORY_SEPARATOR . $name . '.php';
		if ($path = self::getFilePath($path)) {
			//ob_start(); //开启缓冲区
			include($path);
			//$cache = ob_get_contents();
			//ob_end_flush(); //关闭缓存并清空
		}
	}

	/**
	 * 载入扩展功能
	 * @param $name
	 * @return bool
	 */
	public static function loadExt($name)
	{
		return self::loadClass(self::$config['struct']['ext'] . '.' . $name);
	}

	/**
	 * 卸载扩展功能
	 * @param $name
	 * @return bool
	 */
	public static function removeExt($name)
	{
		return self::remvoeClass(self::$config['struct']['ext'] . '.' . $name);
	}

    /**
     * 载入辅助类
     * @param $name
     */
    public static function helper($name)
    {
        $name = strtr($name, '.', '/');
        self::loadFile('helper/' . $name . '.php');
    }
}

define('STARTTIME', microtime(true));
spl_autoload_register(array('Cellular', 'autoload'));

?>
