<?php
class LoginCtr extends PageCtr{
	public function index(){
		echo "<br>to login<br>";
	}
	public function _after(){
		echo "<br>login after<br>";
	}
}