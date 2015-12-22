<?php

class Model_Login_UserLogin extends PIBaseModel {
	function doLogin(){
		$db = Db::init('hihus');
		$sql = 'select * from users where 1=1';
		$res = $db->query($sql);
		while($l = $res->fetch(PDO::FETCH_ASSOC)){
			print_r($l);
		}
	}
}