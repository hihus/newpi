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
		//test com autoload
		$login = new Logic_Login_Login();
		$login->login();
		//test model autoload
		$log_table = new Model_login_UserLogin();
		$log_table->doLogin();
		
	}

	public function _before(){
		echo "before index";
	}
	
	public function _after(){
		echo "after index";
	}
} 
