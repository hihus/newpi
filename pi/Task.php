<?php
if(!defined('PI_ROOT')) define("PI_ROOT",dirname(__FILE__).'/');
include(PI_ROOT.'App.php');

class TaskApp extends App {
	public $task_name = '';
	public $argv = array();

	public function __construct($argv){
		if(!defined("APP_NAME")){
			die('please define APP_NAME const');
		}
		define('TASK_PATH',APP_ROOT.APP_NAME.DOT);

		$this->mode = 'task';
		$this->app_env = Pi::get('app_env','');
		$this->com_env = Pi::get('com_env','');
		//得到参数
		if(!empty($argv)){
			array_shift($argv);
			$this->task_name = array_shift($argv);
		}
		if(empty($this->task_name)){
			die('please input the task name for this process');
		}
		$this->argv = $argv;
		parent::__construct();
	}

	protected function _begin(){
		parent::_begin();
	}

	protected function _initLogger(){
		//获得log path
		if(!defined("LOG_PATH")) define("LOG_PATH",Pi::get('log.path',''));
		if(!is_dir(LOG_PATH)) die('pi.err can not find the log path');

        Pi::inc(Pi::get('LogLib'));

		$logFile = $this->task_name;
        $logLevel = ($this->debug === true) ? Logger::LOG_DEBUG : Pi::get('log.level',Logger::LOG_TRACE);
		$roll = Pi::get('log.roll',Logger::DAY_ROLLING);
		$basic = array('logid'=>$this->appId);

		Logger::init(LOG_PATH,$logFile,$logLevel,array(),$roll);
		Logger::addBasic($basic);
	}

	public function run(){
		//初始化pipe
		$default_pipe = array('TaskProcessPipe'=>'default');
		$pipes = Conf::get('global.pipes',array());
		if(empty($pipes)){
			$pipes = $default_pipe;
		}
		$this->pipeLoadContainer = $pipes;
		parent::run();
	}
}