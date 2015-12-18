<?php
class ApiRouter {
	public $app = null;
	public function __construct(App $app){
		$this->app = $app;
	}

	public function dispatch(){
		$mod_name = Conf::get("global.mod",'mod');
		$func_name = Conf::get("global.func",'func');
		$mod_seg = Conf::get("global.mod_seg",'/');
		$api_path = Conf::get("global.base_path",APP_ROOT.APP_NAME.DOT.'logic'.DOT);

		$mod = Comm::Req($mod_name);
		$func = Comm::Req($func_name);
		$mod = explode($mod_seg,$mod);
		$pattern = '/^[0-9a-zA-Z\/]*$/';
		$class = '';
		if(!empty($mod)){
			foreach ($mod as $k => $m) {
				if(empty($m) || !is_string($m)){
					if(!preg_match($pattern,$m)){
						throw new Exception('error format mod:'.$m,1005);
					}
					unset($mod[$k]);
				}
				$mod[$k] = strtolower($m);
				$class .= ucfirst($mod[$k]);
			}	
		}

		if(empty($mod)){
			throw new Exception('empty api mod:'.$mod,1006);
		}

		if(empty($func) || !is_string($func) ||!preg_match($pattern,$func) ){
			throw new Exception("empty api func:".$func,1007);
		}
		
		$file = $api_path.implode(DOT,$mod).DOT.$class.'.api.php';

		if(!is_readable($file)){
			throw new Exception('api router can not load file:'.$file,1008);
		}
		Pi::inc(PI_CORE.'BaseApi.php');
		Pi::inc($file);
		if(!class_exists($class)){
			throw new Exception('api router not find class:'.$class,1009);
		}
		$cls = new $class();
		if(!is_subclass_of($cls,'BaseApi')){
			throw new Exception('api.err is not the subclass of BaseApi ',1010);
		}
		$res = $this->_call_method($cls,$func);
		if($res === false){
			throw new Exception('api class:'.$class.' call method '.$func.' err ',1011);
		}
	}

	private function _call_method($class,$method,$args = array()){
		if (is_callable(array($class,$method))) {
            $reflection = new ReflectionMethod($class,$method);
            $argnum = $reflection->getNumberOfParameters();
            if ($argnum > count($args)) {
                    throw new Exception('api.err reflection args not match',1012);
            }
            //公共方法才允许被调用
            $reflection->invokeArgs($class,$args);
            return true;
        }

        return false;
	}

}