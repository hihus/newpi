<?php

class LoginExport extends Export{

	function dologin($str){
		$cls = new Logic_Login_Login();
		$res = $cls->login();
		return $res.$str;
	}
}
