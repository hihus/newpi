<?php
define('APP_NAME','task');
define('PI_ROOT',dirname(dirname(dirname(__FILE__))).'/pi/');
define('APP_ROOT',dirname(dirname(__FILE__)).'/');
define('LOG_PATH',dirname(dirname(dirname(__FILE__))).'/logs');
define('COM_ROOT',APP_ROOT.'com/');

include(PI_ROOT.'Task.php');

//task项目需要的框架配置
Pi::set('global.logFile','task');
//代码环境
Pi::set('com_env','dev');
Pi::set('app_env','dev');

//自定义类可以重构提供的基础TaskApp功能
class PTaskApp extends TaskApp {}

$app = new PTaskApp($argv);
$app->run();
