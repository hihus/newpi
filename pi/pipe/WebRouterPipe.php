<?php
/**
 * @file WebRouterPipe.php
 * @author wanghe (hihu@qq.com)
 **/

class WebRouterPipe implements PiIpipe {
	public function __construct(){
		$dispatcher = Conf::get('global.dispatcher_path',PI_CORE.'RouteDispatcher.php');
		if(!is_readable($dispatcher) || !Pi::inc($dispatcher)){
			throw new Exception('can not find the dispatcher config : global.dispatcher_path',1032);
		}
	}

	public function execute(PiApp $app){
		//开始路由,query参数需要有url和param两个变量。方便路由选择
		$dispatcher = new PiRouteDispatcher();
		$dispatcher->run();
	}
//end of class
}