<?php
//web路由器v2版
class RouteDispatcher {
	private $uri = null;
	private $host = '';
	private $host_pre = 'www';
	private $base_path = '';
	private $mod = '';
	private $func = '';
	private $class_pre = '';
	private $prtected = array(  
								'_before'=>1,'_after'=>1,'_p_before'=>1,
								'_p_after'=>1,'initTmpl'=>1,'setRouter'=>1,
								'setAjax'=>1
							 );
	public function __construct(){
		//包含controller需要继承的父类
		Pi::inc(Pi::get('PageCtr'));
	}

	public function run(){
		$this->buildQuery();
		$this->customRouter();
		$this->dispatch();
	}

	public function buildQuery(){
		$this->host = strtolower(Comm::getHost());
		$this->uri = strtolower(Comm::getServer('PATH_INFO'));
	}
	//自定义路由检查,最后反应到uri变量上,参数用request保存代替
	public function customRouter($url = null,$domain = ''){
		$uri = ($url == null) ? $this->uri : $url;
		if(empty($uri)) return false;
		//去掉空path
		$tmp_uri = explode('/',$uri);
		$uri = array();
		//去掉空元素
		if(!empty($tmp_uri)){
			foreach($tmp_uri as $v){
				if(!empty($v)){
					$uri[] = $v;
				}
			}
		}

		//附加二级域名处理
		$host = explode('.',$this->host);
		$this->host_pre = empty($host) ? 'www' : $host[0];
		if(is_string($domain) && !empty($domain)){
			$this->host_pre = $domain;
		}
		if($this->host_pre == 'ajax'){
			array_unshift($uri,'ajax');
		}
		if(isset($uri[0]) && $uri[0] == 'ajax'){
			$this->setBasePath(APP_CTR_ROOT.'ajax'.DOT);
			array_shift($uri);
			$this->class_pre = 'Ajax';
		}else{
			$this->setBasePath(APP_CTR_ROOT);
			$this->class_pre = '';
		}
		if(Conf::get('route.second_domain_enable',true)){
			$base = Conf::get("route.alias_".$this->host_pre,$this->host_pre);
			if(!empty($base) && $this->host_pre != 'www' && $this->host_pre != 'ajax'){
				array_unshift($uri,$base);
			}
		}

		$uri = implode('/',$uri);
		//检查uri安全性
		if(!preg_match('/^[\+\-_0-9a-zA-Z\/]*$/',$uri)){
			throw new Exception('router.err illegal uri visit '.$uri,1027);
		}

		//处理自定义路由,支持目前只匹配带有数字,数字字母混合参数的uri和绝对跳转
		$self_router = Conf::get('route.custom_router');
		//绝对跳转,没有参数在path_info
		if(isset($self_router[$uri])){
			$uri = $self_router[$uri];
		}else{
			$param_pattern = '/[\+\-_0-9]+/';
			if(preg_match($param_pattern,$uri)){
				//带参转换
				$tmp_mod = explode('/',$uri);
				$guess_router = '';
				$params = array();
				if(!empty($tmp_mod)){
					foreach($tmp_mod as $p){
						if(preg_match($param_pattern,$p)){
							$params[] = $p;
							$p = '(:p)';
						}
						$guess_router .= $p.'/';
					}
					$guess_router = rtrim($guess_router,'/');
					if(isset($self_router[$guess_router])){
						$uri = $self_router[$guess_router]['url'];
						$self_params = $self_router[$guess_router]['p'];
						if(!empty($self_params)){
							foreach($self_params as $pn){
								Comm::setReq($pn,array_shift($params));
							}
						}
					}
				}
			}
		}
		//处理完自定义路由后防止内部调用dispath循环跳转。如果有发现问题，跳转到主页
		$this->uri = ($url != null) && ($uri == $this->uri) ? '' : $uri;
		return $this->uri;
	}

	public function setUri($uri){
		$this->uri = $uri;
	}

	public function setParams($p){
		$this->params = $p;
	}

	public function setBasePath($path){
		$this->base_path = $path;
	}

	public function dispatch($url = null,$domain = ''){
		//正常逻辑保证按照目录逻辑加载，需要美化url用path_info传递参数的需要自定义路由配置(保持高效)
		//ajax自动去掉第一层，然后按照路径加载，二级域名去掉第一层，给域名定义alias配置,否则按照二级域名寻找
		
		if($url != null){
			$this->customRouter($url,$domain);
		}
		
		$uri = empty($this->uri) ? array() : explode('/',$this->uri);
		
		//如果没有任何path info,走默认配置，没有配置改成index
		if(empty($uri)){
			$this->mod = array(Conf::get('global.main_page','index'));
			$this->func = Conf::get('global.main_func','index');
		}else if(count($uri) == 1 && !empty($uri[0])){
			$this->mod = array($uri[0]);
			$this->func = Conf::get('global.main_func','index');
		}else{
			$this->func = array_pop($uri);
			$this->mod = $uri;
		}
		//保护不让访问的页面
		if(isset($this->prtected[$this->func])){
			throw new Exception('router.err can not reach the protected ctr',1021);
		}
		$mod_path = '';
		$cls = '';
		foreach($this->mod as $mod){
			$mod_path = $mod_path.$mod.DOT;
			$cls = $cls.ucfirst($mod);
		}
		$cls = $this->class_pre.$cls."Ctr";
		$file_path = $this->base_path.$mod_path.$cls.'.php';

		if(is_readable($file_path)){
			Pi::inc($file_path);
		}else{
			throw new Exception('router.err not found the router file : '.$file_path,1022);
		}
		if(!class_exists($cls)){
			throw new Exception('router.err not found the router class: '.$cls,1023);
		}
		//执行
		$class = new $cls();
		try{
			$this->_call_method($class,'_p_before');
			$this->_call_method($class,'initTmpl');
			$this->_call_method($class,'setRouter',array($this));
			if($this->class_pre === 'Ajax'){
				$this->_call_method($class,'setAjax',array(true));
			}
			$this->_call_method($class,'_before');
			if(substr($this->func,0,1) == '_' || !$this->_call_method($class,$this->func)){
				throw new Exception('router.err not '.$cls.' can not call : '.$this->func,1025);
			}
			$this->_call_method($class,'_after');
			$this->_call_method($class,'_p_after');
		}catch(Exception $ex){
			$this->_call_method($class,'_after');
			$this->_call_method($class,'_p_after');
			throw $ex;
		}
	}

	private function _call_method($class,$method,$args = array()){
		if (is_callable(array($class,$method))) {
            $reflection = new ReflectionMethod($class,$method);
            $argnum = $reflection->getNumberOfParameters();
            if ($argnum > count($args)) {
                    throw new Exception('router.err reflection args not match',1024);
            }
            //公共方法才允许被调用
            $reflection->invokeArgs($class,$args);
            return true;
        }

        return false;
	}
//end of class
}