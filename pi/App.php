<?php
/**
 * @file App.php
 * @author wanghe (hihu@qq.com)
 **/

include(dirname(__FILE__).'/Pi.php');

class App {
	public $debug = false;  //true false
	public $appId = 0;
	public $mode = null;    //exp: web task api com
	public $app_env = '';     //exp: dev test pre online
	public $com_env = '';     //exp: dev test pre online
	public $pipe = null;
	public $pipeContainer = array();
	public $timer = null;
	public $status = 'ok';

	//子类构造函数先完成初始化设置在调用父类的构造函数
	public function __construct(){
		//设置主环境
		ini_set('date.timezone',TIMEZONE);
		ini_set('internal_encoding',ENCODE);
		ini_set('output_buffering','On');
		error_reporting(E_ALL|E_STRICT|E_NOTICE);

		//设置是否开启调试,线上环境不要开启
		if(defined('__PI_EN_DEBUG') || isset($_GET['__PI_EN_DEBUG'])){
			$this->debug = true;
		}
		//必须先设置运行的类型和运行的环境
		if(empty($this->mode) || !is_string($this->mode)){
			die('pi.err not set or set a wrong mode');
		}

		$this->appId = $this->_genAppid();
		Pi::$appId = $this->appId;

		//当要在其他框架或者非pi框架的项目内部嵌入的pi框架的代码
		//需要定义mode为com,然后执行嵌入式的初始化
		if('com' == $this->mode){
			$this->_comInit();
		}else{
			$this->_init();
		}
	}

	//运行开始，执行流程
	public function run(){
		if(!empty($this->pipeLoadContainer)){
			foreach($this->pipeLoadContainer as $pipe => $from){
				if($from == 'default'){
					$this->pipe->loadPipes($pipe,$from);
				}else{
					$this->pipe->loadPipes($pipe);
				}
				$this->pipe->execute($pipe);
			}
		}
	}

	//不同应用可以复写自己的进程唯一标示
	protected function _genAppid(){
		$time = gettimeofday();
		$time = $time['sec'] * 100 + $time['usec'];
		$rand = mt_rand(1, $time+$time);
		$id = ($time ^ $rand)  & 0xFFFFFFFF;
		return floor($id/100)*100;
	}

	protected function _init(){
		$this->_initBegin();
		$this->_checkEnv();
		$this->_initPhpEnv();
		$this->_initProjEnv();
		$this->_initLogger();
		$this->_initLoader();
		$this->_initTimer();
		$this->_initPipes();
		$this->_initDb();
		$this->_initCache();
		$this->_begin();
	}

	protected function _checkEnv(){
		$mustConst = Pi::get('MUST_CONST',array('PI_ROOT','APP_ROOT','COM_ROOT'));
		foreach($mustConst as $c){
			if(!defined($c)){
				die('pi.err first you must define the const: '.$c);
			}
			if(!is_dir(constant($c))){
				die('pi.err it is not a dir of the const: '.$c);
			}
		}
		//对com_root的目录要求
		$com_need_dirs = Pi::get('COM_DIR',array('export','lib','logic','model','conf'));
		$com_dirs = array_flip(scandir(COM_ROOT));
		foreach($com_need_dirs as $d){
			if(!isset($com_dirs[$d])){
				die('pi.err com root need init the dir: '.$d);
			}
		}
		define("EXPORT_ROOT",COM_ROOT.'export'.DOT);
	}

	protected function _initPhpEnv(){
		set_error_handler(array($this,'errorHandler'));
		set_exception_handler(array($this,'exceptionHandler'));
		register_shutdown_function(array($this,'shutdownHandler'));
	}

	protected function _initDb(){
		$db_lib = Pi::get('DbLib');
		if(!Pi::inc($db_lib)){
			die('pi.err can not read the core db lib');
		}
		//对数据库表操作的类需要集成下面文件的类
		if(!Pi::inc(PI_CORE.'BaseModel.php')){
			die('pi.err can not read the BaseModel lib');
		}
	}

	protected function _initCache(){
		$is_enable_memcache = Pi::get('global.enable_memcache',true);
		$is_enable_redis = Pi::get('global.enable_redis',true);
		if($is_enable_memcache){
			if(!Pi::inc(Pi::get('MemcacheLib'))){
				die('pi.err can not read the Memcache Lib');
			}
		}
		if($is_enable_redis){
			if(!Pi::inc(Pi::get('RedisLib'))){
				die('pi.err can not read the Redis Lib');
			}
		}
	}
	
	protected function _initTimer(){
		$this->timer = new EXTimer();
	}
	
	protected function _initPipes(){
		if(!Pi::inc(Pi::get('PipeExe'))){
			die('pi.err can not read the Pipe Lib');
		}
		$this->pipe = new PipeExecutor($this);
		$this->pipe->loadPipes();
	}
	
	protected function _initLoader(){
		if(!Pi::inc(Pi::get('LoaderLib'))){
			die('pi.err can not read the Loader Lib');
		}
	}

	protected function _initProjEnv(){
		if(true === $this->debug){
			ini_set('display_errors',1);
		}
		$this->_initComConfig();
	}

	protected function _initComConfig(){
		//com配置目录
		$path = COM_ROOT.'conf'.DOT;
		if(!is_dir($path)){
			die('pi.err can not find the com conf path');
		}
		define("COM_CONF_PATH",$path);
		//项目配置目录
		$path = APP_ROOT.$this->mode.DOT.'conf'.DOT;
		if(!is_dir($path)){
			die('pi.err can not find the app conf path');
		}
		define("APP_CONF_PATH",$path);
	}

	protected function _initLogger(){
		//获得log path
		if(!defined("LOG_PATH")) define("LOG_PATH",Pi::get('log.path',''));
		if(!is_dir(LOG_PATH)){
			die('pi.err can not find the log path');
		}
		if(!Pi::inc(Pi::get('LogLib'))){
			die('pi.err can not read the Log Lib');
		}

		$logFile = Pi::get('global.logFile','pi');
		$logSeg = Pi::get('global.logSeg',Logger::NONE_ROLLING);
        $logLevel = ($this->debug === true) ? Logger::LOG_DEBUG : Pi::get('log.level',Logger::LOG_TRACE);
		$roll = Pi::get('log.roll',Logger::NONE_ROLLING);
		$basic = array('logid'=>$this->appId);

		Logger::init(LOG_PATH,$logFile,$logLevel,array(),$roll);
		Logger::addBasic($basic);
	}

	function errorHandler(){
		restore_error_handler();
		$error = func_get_args();
		$res = false;
		if (!($error[0] & error_reporting())) {
			Logger::debug('error info, errno:%d,errmsg:%s,file:%s,line:%d',$error[0],$error[1],$error[2],$error[3]);
		} elseif ($error[0] === E_USER_NOTICE) {
			Logger::trace('error trace, errno:%d,errmsg:%s,file:%s,line:%d',$error[0],$error[1],$error[2],$error[3]);
		} elseif ($error[0] === E_USER_WARNING) {
			Logger::warning('error warning, errno:%d,errmsg:%s,file:%s,line:%d',$error[0],$error[1],$error[2],$error[3]);
		} elseif ($error[0] === E_USER_ERROR) {
			Logger::fatal('error error, errno:%d,errmsg:%s,file:%s,line:%d',$error[0],$error[1],$error[2],$error[3]);
		} else {
			Logger::fatal('error error, errno:%d,errmsg:%s,file:%s,line:%d',$error[0],$error[1],$error[2],$error[3]);
			$this->status = 'error';
			$res = true;
		}
		set_error_handler(array($this,'errorHandler'));
		return $res;
	}

	function exceptionHandler($ex){
		restore_exception_handler();
		$errcode = $ex->getMessage();
		$code = $ex->getCode();
		if($this->needToLog($code)){
			$errmsg = sprintf('<< exception:%s, errcode:%s, trace: %s >>',$code,$errcode,$ex->__toString());
			if (($pos = strpos($errcode,' '))) {
				$errcode = substr($errcode,0,$pos); 
			}
			$this->status = $errcode;
			Logger::fatal($errmsg);
		}
	}

	//不需要记录日志的异常值代码，防止有些没有意义的记录冲刷日志,取核心代码和项目代码的两个配置
	protected function needToLog($code){
		$core_no_need_log_code = Conf::get('global.nolog_exception',array());
		$app_no_need_log_code = Pi::get('global.nolog_exception',array());
		if(isset($core_no_need_log_code[$code]) || isset($app_no_need_log_code[$code])){
			return false;
		}
		return true;
	}

	function shutdownHandler(){
		if($this->debug && !empty($res)){
			$res = $this->timer->getResult();
			$str[] = '[time:';
			foreach($res as $time) {
				$str[] = ' '.$time[0].'='.$time[1];
			}
			$str[] = ']';
			Logger::notice(implode('',$str).' status='.$this->status);
			Logger::flush();
		}
		$this->_clearDbOrCache();
		$this->_end();
	}

	//预留清理数据库
	protected function _clearDbOrCache(){
		return true;
	}

	protected function _begin(){
		$this->pipe->execute('InputPipe');
	}

	protected function _end(){
		$this->pipe->execute('OutputPipe');
	}
	
	//预留
	protected function _initBegin(){
		return true;
	}

//end of class
}