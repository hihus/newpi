<?php
ini_set('display_errors',1);

define(APP_ROOT,dirname(dirname(dirname(__FILE__))).'/');
define(LOG_PATH,dirname(dirname(dirname(__FILE__))).'/logs');

Pi::inc(APP_ROOT.'/pi/App.php');

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
