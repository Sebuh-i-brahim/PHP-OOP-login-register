<?php

require_once "core/init.php";

if (!$username = Request::get('user')) {
	Redirect::to('index.php');
}else{
	$user = new User($username);
	if (!$user->exist()) {
		Redirect::to(404);
	}
	else{
		$data = $user->data();
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title><?=e($data->username);?></title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
	<h1><?=e($data->name);?></h1>
	<br>
	<ul>
		<li><a href="index.php">Home</a></li>
		<li><a href="update.php">Update Your Data</a></li>
		<li><a href="changepassword.php">Change Your Password</a></li>
		<li><a href="logout.php">Log out</a></li>
	</ul>
</div>
</body>
</html>