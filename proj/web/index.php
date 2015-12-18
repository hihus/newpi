<?php
define('APP_NAME','web');
define('PI_ROOT',dirname(dirname(dirname(__FILE__))).'/pi/');
define('APP_ROOT',dirname(dirname(__FILE__)).'/');
define('COM_ROOT',APP_ROOT.'com/');
define('APP_CTR_ROOT',APP_ROOT.APP_NAME.'/ctr/');
define('LOG_PATH',dirname(dirname(dirname(__FILE__))).'/logs');
define("__PI_EN_DEBUG",1);

include(PI_ROOT.'Web.php');

//web项目需要的框架配置
Pi::set('global.logFile','web');
Pi::set('env','dev');

$app = new WebApp();
$app->run();
