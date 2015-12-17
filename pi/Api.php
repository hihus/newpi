<?php

if(!defined(PI_ROOT)) define("PI_ROOT",dirname(__FILE__).'/');
include(PI_ROOT.'App.php');

class ApiApp extends App {
	protected $data_types = array('json'=>1,'serialize'=>1);
	public $data_type = 'json';
	public function __construct($argv = array()){
		if(!defined("APP_NAME")){
			die("app.err please define APP_NAME const \n");
		}
		$this->mode = 'api';
		$this->env = Pi::get('env','online');
		$data_type = Conf::get("global.data_type",'json');
		if(isset($this->data_types[$data_type])){
			$this->data_type = $data_type;
		}

		parent::__construct();

		//debug
		if($this->debug && !empty($argv)){
			$_REQUEST['mod'] = $argv[1];
			$_REQUEST['func'] = $argv[2];
		}
	}

	protected function _begin(){
		parent::_begin();
		$this->_initHttp();
	}

	protected function _initHttp(){
		//初始化session,php>5.4 换成 if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
		if(!isset($_SESSION)) {session_start();}
	}

	public function run(){
		//初始化pipe
		$default_pipe = array('ApiReqPipe'=>'default','ApiHttpRouterPipe'=>'default');
		$pipes = Pi::get('global.pipes',array());
		if(empty($pipes)){
			$pipes = $default_pipe;
		}
		$this->pipeLoadContainer = $pipes;
		parent::run();
	}
}