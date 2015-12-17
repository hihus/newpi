<?php

Class Comm {

	static function getClientIp(){
		if (isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP'])){
			return _IPFilter($_SERVER['HTTP_CLIENT_IP']);
		}
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
			do{
				$ip = ip2long($ip);
				//-------------------
				// skip private ip ranges
				//-------------------
				// 10.0.0.0 - 10.255.255.255
				// 172.16.0.0 - 172.31.255.255
				// 192.168.0.0 - 192.168.255.255
				// 127.0.0.1, 255.255.255.255, 0.0.0.0
				//-------------------
				if (!(($ip == 0) or ($ip == 0xFFFFFFFF) or ($ip == 0x7F000001) or
				(($ip >= 0x0A000000) and ($ip <= 0x0AFFFFFF)) or
				(($ip >= 0xC0A8FFFF) and ($ip <= 0xC0A80000)) or
				(($ip >= 0xAC1FFFFF) and ($ip <= 0xAC100000)))){
					return long2ip($ip);
				}
			} while ($ip = strtok(','));
		}
		if (isset($_SERVER['HTTP_PROXY_USER']) and !empty($_SERVER['HTTP_PROXY_USER'])){
			return _IPFilter($_SERVER['HTTP_PROXY_USER']);
		}
		if (isset($_SERVER['REMOTE_ADDR']) and !empty($_SERVER['REMOTE_ADDR'])){
			return _IPFilter($_SERVER['REMOTE_ADDR']);
		}else{
			return "0.0.0.0";
		}
	}

	static function ContentFilter($v){
		return $v;
	}

	//输入安全过滤
	static function filter($v){
		if(is_numeric($v) || is_object($v)){
			return $v;
		}
		if(is_array($v)){
			while(list($key,$val) = each($v)){
				$v[$key] = self::ContentFilter($val);
			}
		}else{
			$v = self::ContentFilter($v);
		}
		return $v;
	}
	//输出安全过滤
	static function filterOutput($v){
		if(is_numeric($v) || is_object($v)){
			return $v;
		}
		return $v;
	}

	static function get($name,$default = ''){
		if(isset($_GET[$name])){
			return self::filter($_GET[$name]);
		}
		return $default;
	}
	
	static function post($name,$default = ''){
		if(isset($_POST[$name])){
			return self::filter($_POST[$name]);
		}
		return $default;
	}
	
	static function req($name,$default = ''){
		if(isset($_REQUEST[$name])){
			return self::filter($_REQUEST[$name]);
		}
		return $default;
	}
	static function setReq($name,$value){
		$_REQUEST[$name] = self::filter($value);
	}

	static function getFile($name){
		if (isset($_FILES[$name])) {
			return $_FILES[$name];
		}
		return null;
	}
	
	static function getCookie($name,$default = ''){
		if(isset($_COOKIE[$name])){
			return $_COOKIE[$name];
		}
		return $default;
	}
	
	static function setCookie($name,$value,$expire = '',$path = '/'){
		$expire  = ($expire === '') ? (time() + 86400) : intval($expire);
		return setcookie($name,$value,$expire,$path);
	}
	
	static function delCookie($name){
		return self::setCookie($name,'',time() - 8640000);
	}

	static function getSession($name,$default = ''){
		if(isset($_SESSION[$name])){
			return $_SESSION[$name];
		}
		return $default;
	}

	static function setSession($name,$value){
		if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
		$_SESSION[$name] = $value;
		return $value;
	}

	static function getHeader($name,$default = ''){
		$name = 'HTTP_'.strtoupper($name);
		return self::getServer($name,$default);
	}

	static function getServer($name,$default = ''){
		if (isset($_SERVER[$name])) {
			return $_SERVER[$name];
		}
		return $default;
	}

	static function getHost(){
		return self::getHeader('HOST');
	}

	static function getRefer(){
		return self::getHeader('REFERER');
	}

	static function getUA($format = false){
		$userAgent = self::getHeader('USER_AGENT');
		if($format !== false){
			$userAgent = self::formatUA($userAgent);
		}
		return $userAgent;
	}

	static function formatUA($ua){
		//规则自定义，比如把 user_agent 简化
		return $ua;
	}

	static function getServers($name,$default = ''){
		if(isset($_SERVER[$name])){
			return $SERVER[$name];
		}
		return $default;
	}

	//预留，预处理HTTP的一些参数
	static function ReqFilter($params = array()){
		return false;
	}

	static function Jump($url){
		header('Location:'.$url);
		exit;
	}

//end of class
}