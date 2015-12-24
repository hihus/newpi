<?php
/**
 * @file WebRouterPipe.php
 * @author wanghe (hihu@qq.com)
 **/

class WebRouterPipe implements Ipipe {
	public function __construct(){
		$dispatcher = Conf::get('global.dispatcher_path',PI_CORE.'RouteDispatcher.php');
		if(!file_exists($dispatcher) || !Pi::inc($dispatcher);){
			throw new Exception('can not find the dispatcher config : global.dispatcher_path',1032);
		}
	}

	public function execute(App $app){
		//开始路由,query参数需要有url和param两个变量。方便路由选择
		$dispatcher = new RouteDispatcher();
		$dispatcher->run();
	}
//end of class
}