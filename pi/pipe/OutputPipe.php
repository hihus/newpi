<?php
/**
 * @file OnputPipe.php
 * @author wanghe (hihu@qq.com)
 * @date 2015/12/08
 * @version 1.0 
 **/

class OutputPipe implements Ipipe {
	public function execute(App $app){
		echo "default output pipe ~ \n";
		Logger::trace("hihu get the log ".date("Y-m-d"));
	}
}