<?php
define('APP_NAME','test');
define('PI_ROOT',dirname(dirname(dirname(__FILE__))).'/pi/');
define('APP_ROOT',dirname(dirname(__FILE__)).'/');
define('LOG_PATH',dirname(dirname(dirname(__FILE__))).'/logs');
define('COM_ROOT',APP_ROOT.'com/');

define("__PI_EN_DEBUG",1);

include(PI_ROOT.'Api.php');

//api项目需要的框架配置
Pi::set('global.logFile','test');
Pi::set('env','test');// dev test pre online

class testApp extends App {
	public function __construct(){
		$this->mod = 'test';
		$this->debug = true;
		$this->logger = 'test';
		parent::__construct();
	}

	public function run(){
		$login = new com_login_login();
		$login->login();
		Logger::trace('get trace, errno:%d,errmsg:%s,file:%s,line:%d',33,44,55,66);
	}
}

$app = new testApp();
$app->run();
