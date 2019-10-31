<?php

/**
 * 
 */
class Cookie
{
	public static function exist($cookie)
	{
		return (isset($_COOKIE[$cookie]))? true : false;
	}
	public static function put($name, $value, $expiry)
	{
		if (setcookie($name, $value, time() + $expiry, "/")) {
			return true;
		}
		return false;
	}
	public static function get($cookie)
	{
		return $_COOKIE[$cookie];
	}
	public static function delete($name)
	{
		self::put($name,'',time() - 1);
	}
}