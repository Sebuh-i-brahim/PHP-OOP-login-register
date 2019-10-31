<?php

session_start();

$GLOBALS['config'] = array(
	'mysql' => array(
		'host' => '127.0.0.1',
		'username' => 'root',
		'password' =>'',
		'dbname' => 'OOP_log-reg'
	),
	'remember' => array(
		'cookie_name' => 'hash',
		'cookie_expiry' => '604800'
	),
	'session' => array(
		'session_name' => 'user',
		'token_name' => '_token'
	) 
);

spl_autoload_register(function ($class) {
	require_once 'class/'.$class.'.php';
});
require_once 'function/sanitize.php';
require_once 'validation.php';

if (Cookie::exist(Config::get('remember/cookie_name')) && !Session::exist(Config::get('session/session_name'))) {
	$hash = Cookie::get(Config::get('remember/cookie_name'));
	$hash_db = DB::getInstance()->select('users_session', array('hash' => $hash));
	if ($hash_db->_count()) {
		$user = new User($hash_db->first('user_id'));
		$user->login();
	}
}