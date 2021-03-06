<?php
/**
 * @file PiApiRouter.php
 * @author wanghe (hihu@qq.com)
 **/

class PiApiRouter {
	public $app = null;

	public function __construct(PiApp $app){
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
						$this->output('api.err error format mod:'.$m,1005);
					}
					unset($mod[$k]);
				}
				$mod[$k] = strtolower($m);
				$class .= ucfirst($mod[$k]);
			}	
		}

		if(empty($mod)){
			$this->output('api.err empty api mod:'.$mod,1006);
		}

		if(empty($func) || !is_string($func) ||!preg_match($pattern,$func) ){
			$this->output('api.err empty or error api func:'.$func,1007);
		}

		Pi::inc(PI_CORE.'BaseApi.php');		

		$file = $api_path.implode(DOT,$mod).DOT.$class.'.api.php';
		if(!is_readable($file) || !Pi::inc($file)){
			$this->output('api.err api router can not load file:'.$file,1008);
		}	

		if(!class_exists($class)){
			$this->output('api.err api router not find class:'.$class,1009);
		}
		
		$cls = new $class();
		
		if(!is_subclass_of($cls,'PiBaseApi')){
			$this->output('api.err is not the subclass of BaseApi',1010);
		}
		if (!is_callable(array($cls,$func))){
			$this->output('api.err api class:'.$class.' can not call method:'.$func,1011);
		}
		$res = pi_call_method($cls,$func);
		$this->output($res);
	}

	public function output($info,$err_code = false){
		if($err_code === false){
			echo json_encode(array('res'=>$info));
		}else{
			//和Api.php的output错误输出格式一致
			echo json_encode(array('msg'=>$info,INNER_ERR=>$err_code));
		}
	}
}