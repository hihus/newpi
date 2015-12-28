<?php
/**
 * @file PipeExecutor.php
 * @author wanghe (hihu@qq.com)
 **/
 
Pi::inc(PI_CORE.'Ipipe.php');

class PipeExecutor {
	private $arr_pipe = array();
	private $app = null;

	function __construct(PiApp $app){
		$this->app = $app;
	}

	function loadPipes($pipes = null,$root = null){
		//pipe 数组格式 path => class_name
		//加载默认的处理管道
		if($pipes == null){
			$pipes = array();
			$input = Pi::get('DefaultInputPipe');
			$output = Pi::get('DefaultOutputPipe');
			$pipes = array(
						$input => PI_PIPE.$input.'.php',
						$output => PI_PIPE.$output.'.php'
					);
		}else{
			if(is_string($pipes)){
				$pipes = array($pipes);
			}
			if(!empty($pipes)){
				//加载管道位置
				$root = ($root == 'default') ? PI_ROOT : COM_ROOT;
				foreach ($pipes as $k => $cls){
					$pipes[$cls] = $root.'pipe'.DOT.$cls.'.php';
					unset($pipes[$k]);
				}
			}
		}
		foreach($pipes as $cls => $path){
			if(isset($this->arr_pipe[$cls])) continue;
			if(is_readable($path) && Pi::inc($path)){
				if(class_exists($cls)){
					$this->arr_pipe[$cls] = new $cls();
				}else{
					throw new Exception('the pipe class '.$cls.' do not exists,check pipe file',1020);
				}
			}else{
				throw new Exception('the pipe '.$cls.' can not load,check pipe file',1020);
			}
		}
	}

	function execute($pipe){
		if(!isset($this->arr_pipe[$pipe])){
			Logger::fatal('pipe.err not run the pipe: %s',var_export($pipe,true));
			return false;
		}
		$pipe_obj = $this->arr_pipe[$pipe];
		if ($pipe_obj->execute($this->app) === false) {
			Logger::fatal('pipe.err execute error for the pipe: %s',var_export($pipe,true));
			return false;
		}
		return true;
	}
//end of class
}