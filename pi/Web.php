<?php
/**
 * @file Web.php
 * @author wanghe (hihu@qq.com)
 **/

if(!defined('PI_ROOT')) define("PI_ROOT",dirname(__FILE__).'/');
include(PI_ROOT.'App.php');

class WebApp extends App {
	public function __construct(){
		if(!defined("APP_CTR_ROOT")){
			die('please define APP_CTR_ROOT const');
		}
		if(!defined("APP_NAME")){
			die('please define APP_NAME const');
		}
		$this->mode = 'web';
		$this->app_env = Pi::get('app_env','');
		$this->com_env = Pi::get('com_env','');
		parent::__construct();
	}

	protected function _begin(){
		parent::_begin();
		$this->_initHttp();
		$this->_initTemplate();
	}

	protected function _initHttp(){
		//初始化session,php>5.4 换成 if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
		if(!isset($_SESSION)) {session_start();}
	}

	protected function _initTemplate(){
		$views = Conf::get('global.view_lib_path');
		if(!is_readable(PI_ROOT.$views)){
			die('can not find the web view libs ');
		}
		Pi::inc(PI_ROOT.$views);
		$cls = Conf::get('global.view_engine');
		if(!class_exists($cls)){
			die('can not init the template engine class');
		}
	}
	
	function errorHandler(){
		parent::errorHandler();
		self::page_5xx();
	}
	
	function exceptionHandler($ex){
		parent::exceptionHandler($ex);
		self::page_4xx();
	}
	
	//webserver 配置html访问不走框架
	function page_4xx(){
		$url = Conf::get('global.404',APP_ROOT.APP_NAME.'/4xx.html');
		Comm::jump($url);
	}

	//webserver 配置html访问不走框架
	function page_5xx(){
		$url = Conf::get('global.404',APP_ROOT.APP_NAME.'/5xx.html');
		Comm::jump($url);
	}

	public function run(){
		//初始化pipe
		$default_pipe = array('WebReqPipe'=>'default','WebRouterPipe'=>'default');
		$pipes = Pi::get('global.pipes',array());
		if(empty($pipes)){
			$pipes = $default_pipe;
		}
		$this->pipeLoadContainer = $pipes;
		parent::run();
	}
}
