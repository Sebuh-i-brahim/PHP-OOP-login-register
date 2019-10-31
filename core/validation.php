<?php
class validation
{
	public static function required ($inp)
	{ 
		return "{$inp} must be required";
	}
	public static function text($inp)
	{ 
		return "{$inp} construct only letters";
	}
	public static function email($inp)
	{
		return "{$inp} isn't true email";
	}
	public static function min($inp)
	{
		return "{$inp} must be minimum 3 characters";
	}
	public static function max($inp)
	{
		return "{$inp} must be maximum 30 characters";
	}
	public static function unique($inp)
	{
		return "{$inp} has used already";
	}
	public static function match($inp = null)
	{
		return "Passwords aren't same";
	}
	public static function password($inp = null)
	{
		return null;
	}
}

