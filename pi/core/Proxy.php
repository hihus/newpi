<?php

//虚拟类
class Abs_PiCom {
	public $mod = '';
	public $add = '';
	public $conf = '';
	public function __construct($mod,$add,$conf){
		$this->mod = $mod;
		$this->add = $add;
		$this->conf = $conf;
	}

	public function __call($n,$r){
		$s = new PI_RPC();
		return $s->req($n,$r,$this->mod,$this->add,$this->conf);
	}
	public function __set($n,$v){
		throw new Exception("the com that support rpc can not set var", 5001);
	}
	public function __get($n){
		throw new Exception("the com that support rpc can not get var", 5002);
	}

	static function Server(){
		$mod = Comm::req('mod');
		$add = Comm::req('add');
		$method = Comm::req('method');
		$args = Comm::req('param',array());
		try {
			$class = picom($mod,$add,true);
			if (is_callable(array($class,$method))) {
	            $reflection = new ReflectionMethod($class,$method);
	            $argnum = $reflection->getNumberOfParameters();
	            if ($argnum > count($args)) {
	                    throw new Exception('inner api err args for class:'.$mod.' - '.$add.' - '.var_export($args),5009);
	            }
	            //公共方法才允许被调用
	            $res = $reflection->invokeArgs($class,$args);
	            return serialize($res);
	        }
			return serialize(array('err'=>5010));
		} catch (Exception $e) {
			return serialize(array('err'=>5008));
		}
	}
//end of class
}

//网络操作
class PI_RPC {
	public function req($method,$params,$mod,$add,$conf){
		$sign = Pi::get('global.innerapi_sign','');
		$sign_name = Pi::get('global.innerapi_sign_name','_pi_inner_nm');
		if(isset($conf['ip']) && isset($conf['net']) && $conf['net'] == 'http'){
			$args = array();
			$args['mod'] = $mod;
			$args['add'] = $add;
			$args['method'] = $method;
			$args['param'] = $params;
			$args[$sign_name] = $sign;
			try {
				$curl = new HttpClient();
				$timeout = (isset($conf['timeout'])) ? intval($conf['timeout']) : 10;
				$res = $curl->sendPostData($conf['ip'],$args,$timeout);
				if($curl->hasError() === false){
					if(isset($res['content'])){
						return unserialize($res['content']);
					}else{
						return unserialize($res);
					}
				}
				throw new Exception("inner api err conf : ".var_export($conf).' - curl info:'.var_export($res->getErrorMsg(),true),5011);
			} catch (Exception $e) {
				throw new Exception("inner api get response err: ".var_export($conf).' and ex:'.$e->getMessage(),5003);
			}
		}
		throw new Exception("inner api err conf : ".var_export($conf),5004);
	}
//end of class
}