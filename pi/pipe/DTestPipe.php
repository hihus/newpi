<?php
/**
 * @file DTestPipe.php
 * @author wanghe (hihu@qq.com)
 * @date 2015/12/08
 * @version 1.0 
 **/

class DTestPipe implements Ipipe {
	public function execute(App $app){
		echo " \n load default test pipe run ~ \n";
	}
}
