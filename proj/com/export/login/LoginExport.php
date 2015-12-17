<?php

class LoginExport extends Export{

	function dologin(){
		$cls = new Logic_Login_Login();
		$cls->login();
		echo "pi com is right ~ \n";
	}
}
