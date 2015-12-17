<?php

Pi::inc(PI_CORE.'Export.php');

//加载com模块的函数
function picom($mod,$add = ''){
	$mod = strtolower($mod);
	$add = strtolower($add);
	if($add == ''){
		$cls = ucfirst($mod).'Export';
		$file = EXPORT_ROOT.$mod.DOT.$cls.'.php';
	}else if(is_string($add)){
		$cls = ucfirst($mod).ucfirst($add).'Export';
		$file = EXPORT_ROOT.$mod.DOT.$cls.'.php';
	}else{
		throw new Exception('picom can not find mod:'.$mod,',add:'.$add,1001);
	}

	if(is_readable($file)){
		Pi::inc($file);
		if(class_exists($cls)){
			$class = new $cls();
			if(!is_subclass_of($class,'Export')){
				throw new Exception('the class '.$cls.' is not the subclass of Export',1002);
			}
			return $class;
		}else{
			throw new Exception('can not find picom class '.$cls.' from '.$file,1003);
		}
	}
	throw new Exception('can not read mod file: '.$file.' from picom func',1004);
}

//自动加载,禁止pi框架下的util和工程lib目录下出现存在 _ 的类名
function _pi_autoloader_core($class){
	if(($pos = strpos($class,'_')) !== false){
		$class = explode('_',$class);
		if(empty($class)) return false;
		$fileName = array_pop($class);
		$class = array_map("strtolower",$class);
		$file = COM_ROOT.implode(DOT,$class).DOT.$fileName.'.php';
		if(is_readable($file)){
			Pi::inc($file);
		}
	}else{
		//优先加载工程中的lib,其次加载框架中的util
		if(is_readable(PI_UTIl.$class.'.php')){
			Pi::inc(PI_UTIl.$class.'.php');
		}else if(is_readable(COM_ROOT.'lib/'.$class.'.php')){
			Pi::inc(COM_ROOT.'lib/'.$class.'.php');
		}
	}
}

if(function_exists('spl_autoload_register')){
	spl_autoload_register('_pi_autoloader_core');
}