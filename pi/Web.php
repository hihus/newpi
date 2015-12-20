<?php

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
		$this->env = Pi::get('env','online');
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
		print_r(debug_backtrace());
		parent::errorHandler();
		self::page_5xx();
	}
	function exceptionHandler($ex){
		parent::exceptionHandler($ex);
		self::page_4xx();
	}
	
	function page_4xx(){
		include(APP_ROOT.APP_NAME.'/4xx.html');
		exit;
	}
	function page_5xx(){
		include(APP_ROOT.APP_NAME.'/5xx.html');
		exit;
	}
	public function run(){
		//test com autoload
		$login = new Logic_Login_Login();
		$login->login();
		//test model autoload
		$log_table = new Model_login_UserLogin();
		$log_table->doLogin();
		//test the extend picom
		
		$com_login = picom("login","find");
		$res = $com_login->find();
		var_export($res);
		//test picom load
		$com_login = picom("login");
		$res = $com_login->dologin();
		var_export($res);
		//pipe可以定义成配置数组。方便上下线
		$this->pipe->loadPipes('WebTestPipe');
		$this->pipe->loadPipes('DTestPipe','default');
		$this->pipe->execute('WebTestPipe');
		$this->pipe->execute('DTestPipe');
		//test util 
		$xz = new Xcrypt();
		$num = rand(10000,20000).rand(10000,20000).rand(10000,20000);
		$res = $xz->encode($num);
		echo $res;
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
