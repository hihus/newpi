<?php
/**
 * @file Pi.php
 * @author wanghe (hihu@qq.com)
 **/

if(!defined("PI_ROOT")) define("PI_ROOT",dirname(__FILE__).'/');

//定义框架核心类,加载核心配置包括
class Pi {
	private static $saIncludeFiles = array();
	private static $saIsLoaded = array();
	private static $saConfData = array();
	static $appId = 0;//全局id

	//框架内包含文件统一入口
	static function inc($sFile){
		if(isset(self::$saIncludeFiles[$sFile])){
			return true;
		}else{
			if(is_readable($sFile)){
				include($sFile);
				self::$saIncludeFiles[$sFile] = 1;
				return true;
			}
		}
		return false;
	}
	//得到配置，应用如果需要使用配置加载请自定义COM_CONF_PATH目录
	static function get($key,$default=null){
		if(isset(self::$saConfData[$key])){
			return self::$saConfData[$key];
		}
		//没有的自动加载文件和配置项,先看有没有本环境的配置文件，如果没有加载默认路径下的配置文件
		if(defined("COM_CONF_PATH") && strpos($key,'.') !== false){
			$file = explode('.',$key);
			if(!empty($file)){
				array_pop($file);
				$file_name = array_pop($file);
				$file = (count($file) == 0) ? '' : implode(DOT,$file).DOT;
				$env = self::get('com_env','');
				if($env != '' && file_exists($file)){
					$file = COM_CONF_PATH.$env.DOT.$file.$file_name.'.inc.php';
				}else{
					$file = COM_CONF_PATH.$file.$file_name.'.inc.php';
				}
				if(self::inc($file) && isset(self::$saConfData[$key])){
					return self::$saConfData[$key];
				}
			}
		}
		return $default;
	}

	static function set($key,$value){self::$saConfData[$key] = $value; }
	static function has($key){return isset(self::$saConfData[$key]); }
	static function clear(){self::$saIsLoaded = array(); self::$saConfData = array(); }
	static function delItem($key){if(self::has($key)){unset(self::$saConfData[$key]); } } 
}

//应用配置加载类，如只给web项目的配置用这个加载
class Conf {

	private static $saIsLoaded = array();
	private static $saConfData = array();

	//得到配置，应用如果需要使用配置加载请自定义APP_CONF_PATH目录
	static function get($key,$default=null){
		if(isset(self::$saConfData[$key])){
			return self::$saConfData[$key];
		}
		//没有的自动加载文件和配置项
		if(defined("APP_CONF_PATH") && strpos($key,'.') !== false){
			$file = explode('.',$key);
			if(!empty($file)){
				array_pop($file);
				$file_name = array_pop($file);
				$file = (count($file) == 0) ? '' : implode(DOT,$file).DOT;
				$env = self::get('app_env','');
				if($env != '' && file_exists($file)){
					$file = APP_CONF_PATH.$env.DOT.$file.$file_name.'.inc.php';
				}else{
					$file = APP_CONF_PATH.$file.$file_name.'.inc.php';
				}
				if(Pi::inc($file) && isset(self::$saConfData[$key])){
					return self::$saConfData[$key];
				}
			}
		}
		return $default;
	}

	static function set($key,$value){self::$saConfData[$key] = $value; }
	static function has($key){return isset(self::$saConfData[$key]); }
	static function clear(){self::$saIsLoaded = array(); self::$saConfData = array(); }
	static function delItem($key){if(self::has($key)){unset(self::$saConfData[$key]); } } 
}

//利用反射调用类方法公用函数

function pi_call_method($class,$method,$args = array(),&$err = 0){
	if (is_callable(array($class,$method))){
        $reflection = new ReflectionMethod($class,$method);
        $argnum = $reflection->getNumberOfParameters();
        if ($argnum > count($args)) {
               $err = 1;
               return false;
        }
        //公共方法才允许被调用
        return $reflection->invokeArgs($class,$args);
    }
    $err = 2;
    return false;
}

//加载基础配置
Pi::inc(PI_ROOT.'Config.inc.php');
