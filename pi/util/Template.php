<?php
/** 
 * @date 2012-8-15
 * @version 1.0 
 * @brief 
 *  
 **/
final class Template
{
	private $vars;
	/**
	 * 模板
	 * @param $content 内容
	 * @param $vars 变量
	 */
	function compile($content, array $vars)
	{
		$this->vars = $vars;
		$content = preg_replace_callback('/\${([a-z][a-z0-9\.\-\_]*)}/i', array($this, 'replaceVariable'), $content);
		return $content;
	}
	
	function replaceVariable($matches)
	{
		$arr = explode('.', $matches[1]);
		$ret = $this->vars;
		foreach($arr as $key) {
			if (isset($ret[$key])) {
				$ret = $ret[$key];
			} else {
				return '';
			}
		}
		return $ret;
	}
}