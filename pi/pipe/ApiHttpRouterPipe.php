<?php
/**
 * @file ApiHttpRouterPipe.php
 * @author wanghe (hihu@qq.com)
 **/

class ApiHttpRouterPipe implements Ipipe {
	public $app = null;
	
	public function execute(App $app){
		$this->app = $app;
 		$router = Conf::get('global.router_file','ApiRouter.php');
		$router_class = Conf::get('global.router_class','ApiRouter');
		if(is_readable(PI_CORE.$router)){
			Pi::inc(PI_CORE.$router);
		}else{
			throw new Exception('api.router can not find the api router : '.$router,1030);
		}
		if(class_exists($router_class)){
			$cls = new $router_class($app);
			$cls->dispatch();
		}else{
			throw new Exception('api.router can not find the router class : '.$router_class,1031);
		}
	}

//end of class
}