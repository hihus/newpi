<?php

class IndexCtr extends PageCtr {

	public function index(){
		
		//$this->jump('/login',true);
		echo "<br>";
		echo "in index";
		echo "<br>";
		$xz = new Xcrypt();
		$num = rand(10000,20000).rand(10000,20000).rand(10000,20000);
		$res = $xz->encode($num);
		echo $res;
		$login = picom('login');
		$res = $login->dologin(array('111'=>1241,'hihu'=>1241));
		echo ($res);
	}

	public function _before(){
		echo "before index";
	}
	
	public function _after(){
		echo "after index";
	}
} 
