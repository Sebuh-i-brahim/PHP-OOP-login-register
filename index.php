<?php

require_once 'core/init.php';


if (Session::exist("home")) {
	echo "<h2>".Session::flash('home')."</h2><br>";
}

$user = new User();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
	<?php if ($user->islogged()): ?>
		<h2>Hello <a href="profile.php?user=<?= e($user->data('username'));?>"><?= e($user->data('username'));?></a></h2>
		<br>
		<ul>
			<li><a href="update.php">Update Your Data</a></li>
			<li><a href="changepassword.php">Change Your Password</a></li>
			<li><a href="logout.php">Log out</a></li>
		</ul>
		<?php if($user->hasPermission('admin')):?>
			<div class="field"><h3>You are an Adminstrator!</h3></div>
		<?php endif;?>
	<?php else :?>
		<h3>You need to <a href="login.php">login</a> or <a href="register.php">register</a></h3>
	<?php endif;?>
</div>
</body>
</html>
