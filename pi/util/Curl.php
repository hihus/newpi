<?php 
/**
 * @file Curl.php
 * @date 2010/06/02 17:59:27
 * @version 1.0 
 * @brief 
 *  
 **/

final class Curl
{
	var $cacheable = false;

	private static $defaultOpts = array(
		CURLOPT_RETURNTRANSFER=>1, 
		CURLOPT_HEADER=>1,
		CURLOPT_FOLLOWLOCATION=>3, 
		CURLOPT_ENCODING=>'',
		CURLOPT_USERAGENT=>'HapN',
		CURLOPT_AUTOREFERER=>1,    
		CURLOPT_CONNECTTIMEOUT=>2, 
		CURLOPT_TIMEOUT=>5,
		CURLOPT_MAXREDIRS=>3,
	//	CURLOPT_VERBOSE=>true
	);
	private static $fixedOpts = array(CURLOPT_RETURNTRANSFER,CURLOPT_HEADER);

	private function getOptions($opt)
	{
		foreach(self::$fixedOpts as $key) {
			unset($opt[$key]);
		}
		//数字下标的array不能merge，否则下标会从0开始计
		$ret = self::$defaultOpts;
		foreach($opt as $key=>$value) {
			$ret[$key] = $value;
		}
		return $ret;
	}

	function get($url,$opt=array())
	{
		$opt[CURLOPT_HTTPGET] = true;
		$ret = $this->request($url,$opt);
		return $this->returnData($ret);
	}

	function post($url,$postData=array(),$opt=array())
	{
		$opt[CURLOPT_POST] = true;
		if (is_array($postData)) { 
			$opt[CURLOPT_POSTFIELDS] = http_build_query($postData);
		} else {
			$opt[CURLOPT_POSTFIELDS] = $postData;
		}
		$ret = $this->request($url,$opt);
		return $this->returnData($ret);
	}
    
    function fetchData($url,$opt=array()) 
    {
        $response = $this->get($url,$opt);
		if (!$response || $response['code'] != 200) {
			return false;
		} 
		return $response['content'];
    }

	private function request($url,$opts)
	{
		$opts = $this->getOptions($opts);
		$ch = @curl_init($url);
		if (!$ch) {
			throw new Exception('mcutil.curlerr init failed:'.$url);
		}
		curl_setopt_array($ch,$opts);
		$data = @curl_exec($ch);
		if ($data === false) {
			$info = curl_getinfo($ch);
			if ($info['http_code'] == 301 ||
				$info['http_code'] == 302) {
					throw new Exception('mcutil.curlerr redirect occurred:'.$info['url']);
				}
		}
		$err = curl_errno($ch);
		$errmsg = curl_error($ch);
		curl_close( $ch );
		return array($err,$errmsg,$data);
	}

	private function parse($ret)
	{
		$pos = strpos($ret,"\r\n\r\n");
		if (!$pos) {
			throw new Exception('mcutil.curlerr redirect occurred:'.$ret);
		}
		$header = substr($ret,0, $pos);
		$body = substr($ret,$pos+4);
		$headerLines = explode("\r\n",$header);
		$head = array_shift($headerLines);
		$cookies = array();
		$headers = array();
		$codes = explode(' ', $head);
		$protocol = array_shift($codes);
		$code = array_shift($codes);
		$status = implode(' ', $codes);
		foreach($headerLines as $line) {
			list($k,$v) = explode(":",$line);
			$k = trim($k);
			$v = trim($v);
			if ($k == 'Set-Cookie') {
				list($ck,$cv) = explode("=",$v);
				$cookies[trim($ck)] = trim($cv);
			} else {
				$headers[$k] = $v; 
			}   
		}   
		return array('header'=>$headers,
			'protocol' => $protocol,
			'code' => intval($code),
			'status' => $status,
			'cookie'=>$cookies,
			'content'=>$body
		);
	}

	private function returnData($ret)
	{
		list($err,$errmsg,$data) = $ret;
		if ($err) {
			throw new Exception('mcutil.curlerr '.$errmsg);
		}
		return $this->parse($data);
	}
    /**
     * 发起HTTPS请求
     */
    public function curl_postXML($url, $data, $header, $post = 1) {
        //初始化curl
        $ch = curl_init();
        //参数设置
        $res = curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, $post);
        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        //连接失败
        if ($result == false) {
            print curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }
}





/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
