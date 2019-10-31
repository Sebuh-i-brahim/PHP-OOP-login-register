<?php

include 'core/init.php';

$user = new User();

if (!$user->islogged()) {
	Redirect::to('index.php');
}
if (Request::exist()) {
	if (Token::check(Request::get('_token'))) {
		$validate = new Validate();
		$validate->check(
			array(
				'old_password' => $validate->set('required'),
				'new_password' => (object)array($validate->set('required'), $validate->set('password')),
				'new_password_again' => (object)array($validate->set('required'), $validate->set('match'))
			),
			Request::all()
		);
		if ($validate->passed()) {
			if (Hash::make($validate->data('old_password'), $user->data('salt')) !== $user->data('password')) {
				$errors = array('error' => 'Your Old password isn\'t true'); 
			}else{
				$salt = Hash::salt(32);
				$user->update(array(
					'password' => Hash::make($validate->data('new_password'), $salt),
					'salt' => $salt
				));
				Session::flash('home', "Your Password has been changed Succesfully!");
				Redirect::to('index.php');
			}
		}else{
			$errors = $validate->errors();
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Chage Password</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
	<ul>
		<li><a href="index.php">Index</a></li>
		<li><a href="update.php">Update Your Data</a></li>
		<li><a href="logout.php">Log Out</a></li>
	</ul><br>

	<?php if(isset($errors)):?>
		<ul>
		<?php foreach($errors as $error):?>
			<li><?= $error;?></li>
		<?php endforeach;?>
		</ul>
	<?php endif;?>
	<form action="" method="post">
		<div class="field">
			<label class="label" for="old_password">Old Password:</label>
			<input type="password" name="old_password" id="old_password" class="input">
		</div><br>
		<div class="field">
			<label class="label" for="new_password">New Password:</label>
			<input type="password" name="new_password" id="new_password" class="input">
		</div><br>
		<div class="field">
			<label class="label" for="new_password_again">New Password Again:</label>
			<input type="password" name="new_password_again" id="new_password_again" class="input">
		</div><br>
		<input type="hidden" name="_token" value="<?=Token::generate();?>">
		<div class="field">
			<button type="submit" class="button">Change</button>
		</div>
	</form>
</div>
</body>
</html>