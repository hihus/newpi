<?php
/**
 * @file Fcrypt.php
 * @date 2010/05/28 15:57:20
 * @version 1.0 
 * @brief 
 *  
 **/

/**
 * 加解密类。主要支持fcrypt方式的加解密
 * 支持批量加解密，含有以id结尾的key的关联数组的加解密
 */ 
final class Fcrypt
{
	const IDNAME	= 'id';
	const FKEY      = 'Eb';
	const IDSTR_LEN = 24;

	var $cacheable = true;

	function isId($str)
	{
		return preg_match('/^[a-z0-9]{24,24}$/',trim($str)) > 0;
	}

	/**
	 * 生成一个字符串的8字节签名。
	 * @params string $str 输入字符串
	 * @return int64 签名数字，可能为负数
	 */ 
	function stringSign($str)
	{
		$hash = md5($str, true);
		$high = substr($hash, 0, 8);
		$low  = substr($hash, 8, 8);
		$sign = $high ^ $low;
		$sign1 =  hexdec(bin2hex(substr($sign, 0, 4)));
		$sign2 =  hexdec(bin2hex(substr($sign, 4, 4)));
		return ($sign1 << 32) | $sign2;
	}

	function encode2Num($d1,$d2,$fkey=self::FKEY)
	{
		return fcrypt_id_2hstr($fkey,$d1,$d2);
	}

	function encode($digit,$fkey=self::FKEY)
	{
		$d1 = $digit >> 32;
		$d2 = $digit & 0xFFFFFFFF;
		return fcrypt_id_2hstr($fkey,$d1,$d2);
	}

	function decode($digitStr,$fkey=self::FKEY)
	{
		$ret = fcrypt_hstr_2id($fkey, $digitStr);
		return ($ret[0] << 32) | $ret[1];
	}

	function encodeString(&$idstr,$fkey=self::FKEY)
	{
		$idstr = trim($idstr);
		if (!$idstr) {
			return true;
		}
		$arr = explode(',', $idstr);
		foreach($arr as &$id) {
			$id = trim($id);
			if (!$id) {
				continue;
			}
			if (!ctype_digit($id) &&
				!($id[0] == '-' && ctype_digit(substr($id,1)))) {
				//不是数字
				return false;
			}
			if ($id !== '0' && $id !== '-1') {
				$id = $this->encode($id,$fkey);
			}
		}
		$idstr = implode(',', $arr);
		return true;
	}

	function decodeString(&$idstr,$fkey=self::FKEY)
	{
		$idstr = trim($idstr);
		if (!$idstr) {
			return true;
		}
		$arr = explode(',', $idstr);
		foreach($arr as &$id) {
			$id = trim($id);
			if (!$id) {
				continue;
			}
			if ($id !== '0' && $id !== '-1') {
				//有时候0是个有意义的数字，而-1则表示全部
				//这里做了个特殊支持不解密
				$id = $this->decode($id,$fkey);
				if (!$id) {
					return false;
				}
			}
		}
		$idstr = implode(',', $arr);
		return true;
	}
	

	function decodeArray(Array &$arr,$fkey=self::FKEY)
	{
		foreach($arr as $key=>&$value) {
			if (!$value) {
				continue;
			}
			$isId = is_string($key) &&  substr($key,-2) === self::IDNAME;
			if (is_string($value)) {
				if ($isId) {
					if (!$this->decodeString($value,$fkey)) {
						return false;
					}
				}
			} elseif (is_array($value)) {
				if ($isId) {
					//支持xid=>array(x,y,z)的解密
					foreach($value as &$v) {
						if (!$v) {
							continue;
						}
						if (is_string($v)) {
							if (!$this->decodeString($v,$fkey)) {
								return false;
							}
						} elseif(is_array($v)) {
							if (!$this->decodeArray($v,$fkey)) {
								return false;
							}
						}
					}
				} else {
					if (!$this->decodeArray($value,$fkey)) {
						return false;
					}
				}
			}
		}
		return true;
	}

	function encodeArray(Array &$arr,$fkey=self::FKEY)
	{
		foreach($arr as $key=>&$value) {
			if (is_array($value)) {
				$this->encodeArray($value,$fkey);
			}elseif ($value && is_string($key) && 
				substr($key,-2) === self::IDNAME) {
					if (is_int($value) || is_string($value)) {
						if ($this->encodeString($value,$fkey)) {
							continue;
						}
					}
					return false;
				}
		}
		return true;
	}

	function encodeArray2(Array &$arr,$fkey=self::FKEY)
	{
		if ( empty($arr) ) {
			return true;
		}
		if (function_exists("fcrypt_enc_array")) {
			if (fcrypt_enc_array($arr, $fkey, self::IDNAME)) {
				return true;
			}
		} else {
			if ($this->encodeArray($arr,$fkey)) {
				return true;
			}
		}
		return false;
	}

	function encodeObject($obj,$fkey=self::FKEY)
	{
		$objstr = serialize($obj);
		$ret = fcrypt_encode_hmac($fkey,$objstr);
		if ($ret === false) {
			trigger_error('encodeObject fail key='.$fkey, E_USER_NOTICE);
			return false;
		}
		return bin2hex($ret);
	}

	function decodeObject($str,$fkey=self::FKEY)
	{
		if (empty($str)) {
			return false;
		}
		$b = pack('H' . strlen($str), $str);
		$r = fcrypt_decode_hmac($fkey,$b);
		if ( false===$r ) { 
			trigger_error('decodeObject fail key='.$fkey, E_USER_NOTICE);
			return false;
		}   
		return unserialize($r);
	}

}



/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
