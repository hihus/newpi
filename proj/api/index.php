<?php
define(APP_NAME,'api');
define(PI_ROOT,dirname(dirname(dirname(__FILE__))).'/pi/');
define(APP_ROOT,dirname(dirname(__FILE__)).'/');
define(LOG_PATH,dirname(dirname(dirname(__FILE__))).'/logs');
define(COM_ROOT,APP_ROOT.'com/');

define("__PI_EN_DEBUG",1);

include(PI_ROOT.'Api.php');

//api项目需要的框架配置
Pi::set('global.logFile','api');
Pi::set('env','dev');// dev test pre online

$app = new ApiApp($argv);
$app->run();
