<?php

class TaskProcessPipe implements Ipipe {
	public function execute(App $app){
		$argv = $app->argv;
		$script = $app->task_name;
		$script = explode('_',$script);
		$cls_file =  ucfirst(strtolower(array_pop($script)));
		if(empty($cls_file)){
			throw new Exception('task.err for run the task for :'.$this->task_name,1033);
		}
		$path = '';
		$class = '';
		if(!empty($script)){
			foreach ($script as $p) {
				$p = strtolower($p);
				$path .= $p.DOT;
				$class .= ucfirst($p);
			}
		}
		$class .= $cls_file;
		$path = TASK_PATH.$path;
		$file = $path.$cls_file.'.php';
		
		Pi::inc(PI_CORE.'BaseTask.php');
		if(!is_readable($file)){
			throw new Exception('task.err can not load the file :'.$file,1034);
		}
		
		Pi::inc($file);
		if(!class_exists($class)){
			throw new Exception('task.err can not find the class :'.$class,1035);
		}

		$cls = new $class();
		if(!is_subclass_of($cls,'BaseTask')){
			throw new Exception('task.err the class '.$class.' is not the subclass of BaseTask ',1036);
		}
		$cls->execute($argv);
	}
}