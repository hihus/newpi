<?php
class LoginCtr extends PageCtr{
	public function index(){
		echo "<br>to login<br>";
		var_dump($this->req('userid'));
	}
	public function _after(){
		echo "<br>login after<br>";
	}
}