<?php

/**
 * 
 */
class Session
{
	public static function put($name, $value)
	{
		return $_SESSION[$name] = $value;
	}
	public static function exist($name)
	{
		return (isset($_SESSION[$name]))? true : false;
	}
	public static function get($name)
	{
		return $_SESSION[$name];
	}
	public static function delete($name)
	{
		if (self::exist($name)) {
			unset($_SESSION[$name]);
		}	
	}
	public static function flash($name, $value = null)
	{
		if (self::exist($name)) {
			$session = self::get($name);
			self::delete($name);
			return $session;
		}else{
			self::put($name, $value);
		}
	}
}