<?php
/**
 * @file ApiReqPipe.php
 * @author wanghe (hihu@qq.com)
 **/

class ApiReqPipe implements Ipipe {
	public $app = null;
	
	public function execute(App $app){
		$this->app = $app;
		$this->filterInput();
	}
	//对于web,可以对 get post request cookie做一些过滤
	public function filterInput(){
		//Comm::ReqFilter();
	}
//end of class
}