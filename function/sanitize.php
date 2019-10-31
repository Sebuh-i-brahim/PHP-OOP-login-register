<?php
function e($string)
{
	return htmlspecialchars($string,ENT_QUOTES, 'UTF-8');
}
function is_email($string)
{
	if (!filter_var($string, FILTER_VALIDATE_EMAIL)) {
		return false;
	}
	return true;
}