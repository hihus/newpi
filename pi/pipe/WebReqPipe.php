<?php
/**
 * @file WebReqPipe.php
 * @author wanghe (hihu@qq.com)
 **/

class WebReqPipe implements Ipipe {
	public $app = null;
	
	public function execute(App $app){
		$this->app = $app;
		$this->filterInput();
	}
	//对于web,可以对 get post request cookie做一些过滤
	public function filterInput(){
		Comm::reqFilter();
	}
//end of class
}