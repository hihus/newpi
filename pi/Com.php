<?php

if(!defined('PI_ROOT')) define("PI_ROOT",dirname(__FILE__).'/');
include(PI_ROOT.'App.php');

class ComApp extends App {

	public function __construct($argv){
		$this->mode = 'com';
		$this->app_env = Pi::get('app_env','');
		$this->com_env = Pi::get('com_env','');
		parent::__construct();
	}

	protected function _comInit(){
		$this->_checkEnv();
		$this->_initPhpEnv();
		$this->_initProjEnv();
		$this->_initLoader();
		$this->_initLogger();
		$this->_initTimer();
		$this->_initDb();
		$this->_initCache();
	}

	protected function _checkEnv(){
		$need_init_const = array('PI_ROOT','COM_ROOT');
		foreach($need_init_const as $c){
			if(!defined($c)){
				die("pi.err you didn't define const: ".$c." \n");
			}
		}
	}

	protected function _initPhpEnv(){
		error_reporting(E_ALL|E_STRICT|E_NOTICE);
	}

	protected function _initComConfig(){
		//com配置目录
		$path = COM_ROOT.'conf'.DOT;
		if(!is_dir($path)){
			die(' pi.err can not find the com conf path ');
		}
		define("COM_CONF_PATH",$path);
	}

	protected function _initLogger(){
		//获得log path
		if(!defined("LOG_PATH")) define("LOG_PATH",Pi::get('log.path',''));
		if(!is_dir(LOG_PATH)) die('pi.err can not find the log path');

        Pi::inc(Pi::get('LogLib'));

		$logFile = 'plugin';
        $logLevel = ($this->debug === true) ? Logger::LOG_DEBUG : Pi::get('log.level',Logger::LOG_TRACE);
		$roll = Pi::get('log.roll',Logger::DAY_ROLLING);
		$basic = array('logid'=>$this->appId);

		Logger::init(LOG_PATH,$logFile,$logLevel,array(),$roll);
		Logger::addBasic($basic);
	}

	public function run(){
		
		parent::run();
	}
}

$_G_Pi_Only_Com = new ComApp();
