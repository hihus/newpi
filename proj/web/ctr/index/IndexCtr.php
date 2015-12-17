<?php

class IndexCtr extends PageCtr {

	public function index(){
		echo "<br>";
		echo "in index";
		echo "<br>";
		//test com autoload
		$login = new Logic_Login_Login();
		$login->login();
		//test model autoload
		$log_table = new Model_login_UserLogin();
		$log_table->doLogin();
		$this->assign("hihu","cadsl");
		$this->display('login/index.tpl',true);
		//$this->jump('/login',true);
	}

	public function _before(){
		echo "before index";
	}
	
	public function _after(){
		echo "after index";
	}
} 
