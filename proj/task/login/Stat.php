<?php

class LoginStat extends BaseTask {
	public function execute($argv){
		//test com autoload
		$login = new Logic_Login_Login();
		$login->login();
		echo "sadgasg";
		//test model autoload
		$log_table = new Model_login_UserLogin();
		$log_table->doLogin();
	}
}